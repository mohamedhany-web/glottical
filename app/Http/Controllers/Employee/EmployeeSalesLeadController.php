<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Order;
use App\Models\SalesLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EmployeeSalesLeadController extends Controller
{
    private function gate(): void
    {
        $u = Auth::user();
        abort_unless($u && $u->isEmployee() && $u->employeeCan('sales_desk'), 403);
    }

    /** @return array<int, string> */
    private static function statusValues(): array
    {
        return [
            SalesLead::STATUS_NEW,
            SalesLead::STATUS_CONTACTED,
            SalesLead::STATUS_QUALIFIED,
            SalesLead::STATUS_CONVERTED,
            SalesLead::STATUS_LOST,
        ];
    }

    /** @return array<int, string> */
    private static function sourceValues(): array
    {
        return [
            SalesLead::SOURCE_WEBSITE,
            SalesLead::SOURCE_PHONE,
            SalesLead::SOURCE_SOCIAL,
            SalesLead::SOURCE_REFERRAL,
            SalesLead::SOURCE_EVENT,
            SalesLead::SOURCE_WALK_IN,
            SalesLead::SOURCE_OTHER,
        ];
    }

    public function index(Request $request)
    {
        $this->gate();

        if (\App\Services\Crm\CrmAccessService::canAccessCrm(Auth::user())) {
            return redirect()->route('employee.crm.leads.index', $request->query());
        }

        $query = SalesLead::query()
            ->with(['assignedTo:id,name', 'creator:id,name', 'interestedCourse:id,title']);

        if ($request->boolean('mine')) {
            $query->where('assigned_to', Auth::id());
        }

        if ($request->filled('status')) {
            $s = (string) $request->input('status');
            if (in_array($s, self::statusValues(), true)) {
                $query->where('status', $s);
            }
        }

        if ($request->filled('source')) {
            $src = (string) $request->input('source');
            if (in_array($src, self::sourceValues(), true)) {
                $query->where('source', $src);
            }
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            if ($search !== '') {
                $like = '%'.$search.'%';
                $query->where(function ($q) use ($like, $search) {
                    $q->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like)
                        ->orWhere('company', 'like', $like);
                    if (ctype_digit($search)) {
                        $q->orWhere('id', (int) $search);
                    }
                });
            }
        }

        $leads = $query->latest()->paginate(20)->withQueryString();

        return view('employee.sales.leads.index', compact('leads'));
    }

    public function create()
    {
        $this->gate();

        $courses = AdvancedCourse::query()->orderBy('title')->get(['id', 'title']);

        return view('employee.sales.leads.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $this->gate();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:64',
            'company' => 'nullable|string|max:255',
            'source' => ['required', Rule::in(self::sourceValues())],
            'status' => ['nullable', Rule::in([SalesLead::STATUS_NEW, SalesLead::STATUS_CONTACTED, SalesLead::STATUS_QUALIFIED])],
            'notes' => 'nullable|string|max:10000',
            'interested_advanced_course_id' => 'nullable|exists:advanced_courses,id',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['assigned_to'] = Auth::id();
        $validated['status'] = $validated['status'] ?? SalesLead::STATUS_NEW;

        SalesLead::create($validated);

        return redirect()->route('employee.sales.leads.index')->with('success', 'تم إضافة العميل المحتمل.');
    }

    public function show(SalesLead $salesLead)
    {
        $this->gate();

        $salesLead->load([
            'assignedTo:id,name,email',
            'creator:id,name',
            'linkedUser:id,name,email',
            'convertedOrder:id,user_id,amount,status,advanced_course_id',
            'convertedOrder.course:id,title',
            'interestedCourse:id,title',
        ]);

        return view('employee.sales.leads.show', compact('salesLead'));
    }

    public function edit(SalesLead $salesLead)
    {
        $this->gate();
        abort_if($salesLead->isConverted() || $salesLead->isLost(), 403, 'لا يمكن تعديل عميل محوّل أو خاسر.');

        $courses = AdvancedCourse::query()->orderBy('title')->get(['id', 'title']);

        return view('employee.sales.leads.edit', compact('salesLead', 'courses'));
    }

    public function update(Request $request, SalesLead $salesLead)
    {
        $this->gate();
        abort_if($salesLead->isConverted() || $salesLead->isLost(), 403, 'لا يمكن تعديل عميل محوّل أو خاسر.');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:64',
            'company' => 'nullable|string|max:255',
            'source' => ['required', Rule::in(self::sourceValues())],
            'status' => ['required', Rule::in([SalesLead::STATUS_NEW, SalesLead::STATUS_CONTACTED, SalesLead::STATUS_QUALIFIED])],
            'notes' => 'nullable|string|max:10000',
            'interested_advanced_course_id' => 'nullable|exists:advanced_courses,id',
        ]);

        $salesLead->update($validated);

        return redirect()->route('employee.sales.leads.show', $salesLead)->with('success', 'تم حفظ التعديلات.');
    }

    public function assignToMe(SalesLead $salesLead)
    {
        $this->gate();

        if (\App\Services\Crm\CrmAccessService::canAccessCrm(Auth::user())) {
            return redirect()
                ->route('employee.crm.leads.show', $salesLead)
                ->with('error', 'تعيين العملاء المحتملين في CRM يتم من قائد الفريق أو التسويق حسب صلاحياتك.');
        }

        if ($salesLead->isConverted() || $salesLead->isLost()) {
            return back()->with('error', 'هذا السجل مغلق ولا يمكن تعيينه.');
        }

        if ($salesLead->assigned_to && (int) $salesLead->assigned_to !== (int) Auth::id()) {
            return back()->with('error', 'هذا العميل مسند بالفعل إلى مندوب آخر.');
        }

        $salesLead->update(['assigned_to' => Auth::id()]);

        return back()->with('success', 'تم تعيين العميل المحتمل لك.');
    }

    public function convert(Request $request, SalesLead $salesLead)
    {
        $this->gate();
        abort_if($salesLead->isConverted() || $salesLead->isLost(), 422, 'هذا السجل مغلق.');

        $validated = $request->validate([
            'mode' => ['required', Rule::in(['order', 'user', 'manual'])],
            'order_id' => ['required_if:mode,order', 'nullable', 'integer', 'exists:orders,id'],
            'user_id' => ['required_if:mode,user', 'nullable', 'integer', 'exists:users,id'],
            'conversion_note' => 'nullable|string|max:2000',
        ]);

        $note = trim((string) ($validated['conversion_note'] ?? ''));
        if ($note !== '') {
            $prefix = '['.now()->toDateTimeString().'] تحويل: ';
            $salesLead->notes = trim(($salesLead->notes ?? '')."\n\n".$prefix.$note);
        }

        $updates = [
            'status' => SalesLead::STATUS_CONVERTED,
            'converted_at' => now(),
            'lost_reason' => null,
        ];

        if ($validated['mode'] === 'order') {
            $order = Order::query()->findOrFail((int) $validated['order_id']);
            $updates['converted_order_id'] = $order->id;
            $updates['linked_user_id'] = $order->user_id;
        } elseif ($validated['mode'] === 'user') {
            $updates['linked_user_id'] = (int) $validated['user_id'];
            $updates['converted_order_id'] = null;
        } else {
            $updates['converted_order_id'] = null;
            $updates['linked_user_id'] = null;
        }

        $salesLead->fill($updates);
        $salesLead->save();

        return back()->with('success', 'تم تسجيل التحويل كعميل فعلي.');
    }

    public function markLost(Request $request, SalesLead $salesLead)
    {
        $this->gate();
        abort_if($salesLead->isConverted() || $salesLead->isLost(), 422, 'هذا السجل مغلق.');

        $validated = $request->validate([
            'lost_reason' => 'required|string|max:5000',
        ]);

        $salesLead->update([
            'status' => SalesLead::STATUS_LOST,
            'lost_reason' => $validated['lost_reason'],
            'converted_at' => null,
            'converted_order_id' => null,
        ]);

        return back()->with('success', 'تم تسجيل الخسارة.');
    }
}
