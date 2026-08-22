<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SearchInput;
use App\Models\Invoice;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InvoiceController extends Controller
{
    private const INVOICE_TYPES = ['course', 'subscription', 'membership', 'learning_path', 'other'];

    /**
     * عرض قائمة الفواتير
     * محمي من: XSS, SQL Injection, Brute Force
     */
    public function index(Request $request)
    {
        try {
            $query = Invoice::with('user')
                ->orderBy('created_at', 'desc');

            // فلترة حسب الحالة - حماية من SQL Injection
            if ($request->filled('status')) {
                $status = strip_tags(trim($request->status));
                $status = preg_replace('/[^a-z_]/', '', $status); // السماح فقط بالأحرف الصغيرة والشرطة السفلية
                if (in_array($status, ['pending', 'paid', 'overdue', 'cancelled'], true)) {
                    $query->where('status', $status);
                }
            }

            // البحث - حماية من XSS و SQL Injection
            if ($request->filled('search')) {
                $search = SearchInput::sanitizeForLike((string) $request->search);
                if ($search !== '') {
                    $query->where(function ($q) use ($search) {
                        $q->where('invoice_number', 'like', "%{$search}%")
                          ->orWhereHas('user', function ($uq) use ($search) {
                              $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                          });
                    });
                }
            }

            $invoices = $query->paginate(20);

            // إحصائيات سريعة
            $stats = [
                'total' => Invoice::count(),
                'pending' => Invoice::pending()->count(),
                'paid' => Invoice::paid()->count(),
                'overdue' => Invoice::overdue()->count(),
            ];

            return view('admin.invoices.index', compact('invoices', 'stats'));
        } catch (\Throwable $e) {
            Log::error('Error in InvoiceController@index', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            abort(500, 'حدث خطأ أثناء تحميل الصفحة');
        }
    }

    public function create()
    {
        try {
            $users = User::query()
                ->where('role', 'student')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'phone']);

            return view('admin.invoices.create', compact('users'));
        } catch (\Throwable $e) {
            Log::error('Error in InvoiceController@create', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            abort(500, 'حدث خطأ أثناء تحميل صفحة إنشاء الفاتورة');
        }
    }

    /**
     * حفظ فاتورة جديدة
     * محمي من: XSS, SQL Injection, Mass Assignment, Brute Force
     */
    public function store(Request $request)
    {
        // Rate Limiting - حماية من Brute Force
        $key = 'create-invoice:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', 'لقد قمت بمحاولات كثيرة. يرجى المحاولة بعد ' . ceil($seconds / 60) . ' دقيقة.');
        }
        RateLimiter::hit($key, 60);

        try {
            DB::beginTransaction();

            // Sanitization
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'type' => 'required|in:'.implode(',', self::INVOICE_TYPES),
                'description' => 'nullable|string|max:1000',
                'subtotal' => 'required|numeric|min:0|max:99999999.99',
                'tax_amount' => 'nullable|numeric|min:0|max:99999999.99',
                'discount_amount' => 'nullable|numeric|min:0|max:99999999.99',
                'due_date' => 'nullable|date|after_or_equal:today',
                'notes' => 'nullable|string|max:1000',
            ]);

            // Additional sanitization
            $validated['type'] = strip_tags(trim($validated['type']));
            $validated['description'] = isset($validated['description'])
                ? strip_tags(trim((string) $validated['description']))
                : '';
            $validated['notes'] = ($validated['notes'] ?? null) ? strip_tags(trim((string) $validated['notes'])) : null;

            // DB constraint: invoices.description is NOT NULL in this project.
            // Provide a safe default when the admin leaves it empty.
            if ($validated['description'] === '') {
                $validated['description'] = match ($validated['type']) {
                    'course' => 'فاتورة كورس',
                    'subscription' => 'فاتورة اشتراك',
                    'membership' => 'فاتورة عضوية',
                    'learning_path' => 'فاتورة مسار تعليمي',
                    default => 'فاتورة',
                };
            }

            $total = $validated['subtotal']
                + ($validated['tax_amount'] ?? 0)
                - ($validated['discount_amount'] ?? 0);

            // Mass Assignment Protection - استخدام fillable فقط
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateUniqueInvoiceNumber(),
                'user_id' => (int) $validated['user_id'],
                'type' => $validated['type'],
                'description' => $validated['description'],
                'subtotal' => (float) $validated['subtotal'],
                'tax_amount' => (float) ($validated['tax_amount'] ?? 0),
                'discount_amount' => (float) ($validated['discount_amount'] ?? 0),
                'total_amount' => (float) $total,
                'status' => 'pending',
                'due_date' => ! empty($validated['due_date'])
                    ? date('Y-m-d', strtotime((string) $validated['due_date']))
                    : now()->addDays(30)->format('Y-m-d'),
                'notes' => $validated['notes'] ?? null,
            ]);

            $this->logInvoiceActivity('created', $invoice, 'تم إنشاء فاتورة جديدة: '.$invoice->invoice_number, $request);

            DB::commit();

            return redirect()->route('admin.invoices.index')
                ->with('success', 'تم إنشاء الفاتورة بنجاح');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in InvoiceController@store', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return back()->with('error', 'حدث خطأ أثناء إنشاء الفاتورة')->withInput();
        }
    }

    /**
     * عرض تفاصيل الفاتورة
     * محمي من: XSS
     */
    public function show(Invoice $invoice)
    {
        try {
            $with = ['user'];
            if (Schema::hasTable('payments')) {
                $with[] = 'payments';
            }
            if (Schema::hasTable('transactions') && Schema::hasColumn('transactions', 'invoice_id')) {
                $with[] = 'transactions';
            }
            if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'invoice_id')) {
                $with[] = 'order';
            }
            if (Schema::hasTable('expenses') && Schema::hasColumn('expenses', 'invoice_id')) {
                $with[] = 'expense';
            }

            $invoice->load($with);

            return view('admin.invoices.show', compact('invoice'));
        } catch (\Throwable $e) {
            Log::error('Error in InvoiceController@show', [
                'invoice_id' => $invoice->id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            abort(500, 'حدث خطأ أثناء تحميل الصفحة');
        }
    }

    public function edit(Invoice $invoice)
    {
        try {
            $users = User::query()
                ->where('role', 'student')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'phone']);

            return view('admin.invoices.edit', compact('invoice', 'users'));
        } catch (\Throwable $e) {
            Log::error('Error in InvoiceController@edit', [
                'invoice_id' => $invoice->id,
                'message' => $e->getMessage(),
            ]);
            abort(500, 'حدث خطأ أثناء تحميل صفحة التعديل');
        }
    }

    /**
     * تحديث فاتورة
     * محمي من: XSS, SQL Injection, Mass Assignment, Brute Force
     */
    public function update(Request $request, Invoice $invoice)
    {
        // Rate Limiting
        $key = 'update-invoice:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', 'لقد قمت بمحاولات كثيرة. يرجى المحاولة بعد ' . ceil($seconds / 60) . ' دقيقة.');
        }
        RateLimiter::hit($key, 60);

        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'type' => 'required|in:'.implode(',', self::INVOICE_TYPES),
                'description' => 'nullable|string|max:1000',
                'subtotal' => 'required|numeric|min:0|max:99999999.99',
                'tax_amount' => 'nullable|numeric|min:0|max:99999999.99',
                'discount_amount' => 'nullable|numeric|min:0|max:99999999.99',
                'status' => 'required|in:pending,paid,overdue,cancelled',
                'due_date' => 'nullable|date',
                'notes' => 'nullable|string|max:1000',
            ]);

            // Additional sanitization
            $validated['type'] = strip_tags(trim($validated['type']));
            $validated['description'] = isset($validated['description'])
                ? strip_tags(trim((string) $validated['description']))
                : '';
            $validated['status'] = strip_tags(trim($validated['status']));
            $validated['notes'] = ($validated['notes'] ?? null) ? strip_tags(trim((string) $validated['notes'])) : null;

            if ($validated['description'] === '') {
                $validated['description'] = match ($validated['type']) {
                    'course' => 'فاتورة كورس',
                    'subscription' => 'فاتورة اشتراك',
                    'membership' => 'فاتورة عضوية',
                    'learning_path' => 'فاتورة مسار تعليمي',
                    default => 'فاتورة',
                };
            }

            $total = $validated['subtotal']
                + ($validated['tax_amount'] ?? 0)
                - ($validated['discount_amount'] ?? 0);

            // Mass Assignment Protection
            $invoice->update([
                'user_id' => (int) $validated['user_id'],
                'type' => $validated['type'],
                'description' => $validated['description'],
                'subtotal' => (float) $validated['subtotal'],
                'tax_amount' => (float) ($validated['tax_amount'] ?? 0),
                'discount_amount' => (float) ($validated['discount_amount'] ?? 0),
                'total_amount' => (float) $total,
                'status' => $validated['status'],
                'due_date' => ! empty($validated['due_date'])
                    ? date('Y-m-d', strtotime((string) $validated['due_date']))
                    : null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $this->logInvoiceActivity('updated', $invoice, 'تم تحديث فاتورة: '.$invoice->invoice_number, $request);

            DB::commit();

            return redirect()->route('admin.invoices.index')
                ->with('success', 'تم تحديث الفاتورة بنجاح');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in InvoiceController@update', [
                'invoice_id' => $invoice->id,
                'message' => $e->getMessage(),
            ]);
            return back()->with('error', 'حدث خطأ أثناء تحديث الفاتورة')->withInput();
        }
    }

    /**
     * حذف فاتورة
     * محمي من: Brute Force
     */
    public function destroy(Invoice $invoice)
    {
        // Rate Limiting
        $key = 'delete-invoice:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', 'لقد قمت بمحاولات كثيرة. يرجى المحاولة بعد ' . ceil($seconds / 60) . ' دقيقة.');
        }
        RateLimiter::hit($key, 300);

        try {
            DB::beginTransaction();

            $invoiceNumber = $invoice->invoice_number;
            $invoiceId = $invoice->id;
            $invoice->delete();

            $this->logInvoiceActivity(
                'deleted',
                null,
                'تم حذف فاتورة: '.$invoiceNumber,
                request(),
                $invoiceId
            );

            DB::commit();

            return redirect()->route('admin.invoices.index')
                ->with('success', 'تم حذف الفاتورة بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in InvoiceController@destroy', [
                'message' => $e->getMessage(),
            ]);
            return back()->with('error', 'حدث خطأ أثناء حذف الفاتورة');
        }
    }

    private function logInvoiceActivity(
        string $action,
        ?Invoice $invoice,
        string $description,
        $request,
        ?int $modelId = null
    ): void {
        if (! Schema::hasTable('activity_logs')) {
            return;
        }

        try {
            $payload = [
                'user_id' => Auth::id(),
                'action' => $action,
                'model_type' => Invoice::class,
                'model_id' => $modelId ?? $invoice?->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ];

            if (Schema::hasColumn('activity_logs', 'description')) {
                $payload['description'] = $description;
            }

            ActivityLog::create($payload);
        } catch (\Throwable $e) {
            Log::warning('Invoice activity log skipped', [
                'action' => $action,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
