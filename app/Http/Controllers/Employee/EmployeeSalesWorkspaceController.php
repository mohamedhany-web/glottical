<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SalesLead;
use App\Models\SalesOrderNote;
use App\Services\Crm\CrmAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeSalesWorkspaceController extends Controller
{
    private function gate(): void
    {
        $u = Auth::user();
        abort_unless($u && $u->isEmployee() && $u->employeeCan('sales_desk'), 403);
    }

    public function desk()
    {
        $this->gate();
        $user = Auth::user();
        $userId = $user->id;
        $useCrm = CrmAccessService::canAccessCrm($user);

        $leadQuery = $useCrm
            ? CrmAccessService::leadsQueryFor($user)->open()
            : SalesLead::query()->open();

        $stats = [
            'pending' => Order::where('status', Order::STATUS_PENDING)->count(),
            'approved' => Order::where('status', Order::STATUS_APPROVED)->count(),
            'rejected' => Order::where('status', Order::STATUS_REJECTED)->count(),
            'revenue_month' => (float) Order::where('status', Order::STATUS_APPROVED)
                ->where('approved_at', '>=', now()->startOfMonth())
                ->sum('amount'),
            'mine_pending' => Order::where('status', Order::STATUS_PENDING)->where('sales_owner_id', $userId)->count(),
            'unassigned_pending' => Order::where('status', Order::STATUS_PENDING)->whereNull('sales_owner_id')->count(),
            'mine_won_month' => Order::where('status', Order::STATUS_APPROVED)
                ->where('sales_owner_id', $userId)
                ->where('approved_at', '>=', now()->startOfMonth())
                ->count(),
            'leads_open' => (clone $leadQuery)->count(),
            'leads_mine_open' => $useCrm
                ? (clone $leadQuery)->where('assigned_to', $userId)->count()
                : SalesLead::query()->open()->where('assigned_to', $userId)->count(),
        ];

        $recentOrders = Order::query()
            ->with(['user:id,name,email', 'course:id,title', 'salesOwner:id,name'])
            ->latest()
            ->take(12)
            ->get();

        $myOrders = Order::query()
            ->where('sales_owner_id', $userId)
            ->with(['user:id,name,email', 'course:id,title'])
            ->latest()
            ->take(8)
            ->get();

        return view('employee.sales.desk', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'myOrders' => $myOrders,
            'useCrm' => $useCrm,
            'leadsIndexRoute' => $useCrm ? 'employee.crm.leads.index' : 'employee.sales.leads.index',
        ]);
    }

    public function ordersIndex(Request $request)
    {
        $this->gate();

        $query = Order::query()
            ->with(['user:id,name,email,phone', 'course:id,title', 'salesOwner:id,name']);

        // طلباتي وبلا مندوب متنافيان — لا يمكن تطبيقهما معاً
        if ($request->boolean('mine') && ! $request->boolean('unassigned')) {
            $query->where('sales_owner_id', Auth::id());
        } elseif ($request->boolean('unassigned') && ! $request->boolean('mine')) {
            $query->whereNull('sales_owner_id');
        } elseif ($request->boolean('mine') && $request->boolean('unassigned')) {
            // لو اختُيرا معاً: أظهر طلباتي فقط (الأكثر فائدة)
            $query->where('sales_owner_id', Auth::id());
        }

        if ($request->filled('status')) {
            $s = $request->input('status');
            if (in_array($s, [Order::STATUS_PENDING, Order::STATUS_APPROVED, Order::STATUS_REJECTED], true)) {
                $query->where('status', $s);
            }
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })->orWhereHas('course', function ($cq) use ($search) {
                        $cq->where('title', 'like', "%{$search}%");
                    });
                    if (ctype_digit($search)) {
                        $q->orWhere('id', (int) $search);
                    }
                });
            }
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('employee.sales.orders-index', compact('orders'));
    }

    public function orderShow(Order $order)
    {
        $this->gate();

        $order->load([
            'user',
            'course',
            'salesOwner',
            'salesNotes.user',
        ]);

        return view('employee.sales.order-show', compact('order'));
    }

    public function storeNote(Request $request, Order $order)
    {
        $this->gate();

        $validated = $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        SalesOrderNote::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'body' => $validated['body'],
        ]);

        $order->update(['sales_contacted_at' => now()]);

        return back()->with('success', 'تم حفظ الملاحظة.');
    }

    public function claim(Order $order)
    {
        $this->gate();

        if ($order->sales_owner_id && (int) $order->sales_owner_id !== (int) Auth::id()) {
            return back()->with('error', 'هذا الطلب مسند بالفعل إلى مندوب آخر.');
        }

        $order->update([
            'sales_owner_id' => Auth::id(),
            'sales_contacted_at' => now(),
        ]);

        return back()->with('success', 'تم استلام الطلب كمسؤول مبيعات.');
    }
}
