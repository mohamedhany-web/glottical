<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupCohort;
use App\Models\TutoringGroupPackage;
use App\Models\Wallet;
use App\Services\TutoringGroupCheckoutService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use InvalidArgumentException;

class TutoringCheckoutController extends Controller
{
    public function show(Request $request, string $slug): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'))->with('info', 'سجّل الدخول لإتمام الاشتراك والدفع.');
        }

        $group = TutoringGroup::query()->active()->where('slug', $slug)->firstOrFail();
        $package = null;
        $cohort = null;

        if ($request->filled('package')) {
            $package = TutoringGroupPackage::query()
                ->where('tutoring_group_id', $group->id)
                ->where('id', $request->integer('package'))
                ->active()
                ->firstOrFail();
        }

        if ($request->filled('cohort')) {
            $cohort = TutoringGroupCohort::query()
                ->where('tutoring_group_id', $group->id)
                ->where('id', $request->integer('cohort'))
                ->firstOrFail();
        }

        if (! $package && ! $cohort) {
            return redirect()->route('public.groups.show', $group->slug)
                ->with('error', 'اختر باقة أو دفعة أولاً.');
        }

        $wallets = Wallet::where('is_active', true)
            ->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer'])
            ->orderBy('type')
            ->get();

        $amount = $package
            ? (float) $package->price
            : (float) ($group->price ?? 0);

        return view('public.tutoring-checkout', [
            'group' => $group,
            'package' => $package,
            'cohort' => $cohort,
            'wallets' => $wallets,
            'amount' => $amount,
            'startsAt' => $request->input('starts_at'),
        ]);
    }

    public function store(Request $request, string $slug): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $group = TutoringGroup::query()->active()->where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'package_id' => ['nullable', 'integer', 'exists:tutoring_group_packages,id'],
            'cohort_id' => ['nullable', 'integer', 'exists:tutoring_group_cohorts,id'],
            'starts_at' => ['nullable', 'date'],
            'payment_method' => ['required', 'in:online,wallet_transfer,admin_review'],
            'wallet_id' => ['nullable', 'integer', 'exists:wallets,id'],
        ]);

        try {
            if (! empty($data['package_id'])) {
                $package = TutoringGroupPackage::query()
                    ->where('tutoring_group_id', $group->id)
                    ->where('id', $data['package_id'])
                    ->active()
                    ->firstOrFail();

                $order = TutoringGroupCheckoutService::createPackageOrder(
                    Auth::user(),
                    $group,
                    $package,
                    $data['payment_method'] === 'admin_review' ? 'manual' : $data['payment_method'],
                    $data['wallet_id'] ?? null
                );

                if (! empty($data['starts_at'])) {
                    $order->update([
                        'notes' => trim(($order->notes ?? '').' | starts_at='.Carbon::parse($data['starts_at'])->toIso8601String()),
                    ]);
                }
            } elseif (! empty($data['cohort_id'])) {
                $cohort = TutoringGroupCohort::query()
                    ->where('tutoring_group_id', $group->id)
                    ->where('id', $data['cohort_id'])
                    ->firstOrFail();

                $startsAt = ! empty($data['starts_at']) ? Carbon::parse($data['starts_at']) : null;

                $order = TutoringGroupCheckoutService::createCohortOrder(
                    Auth::user(),
                    $group,
                    $cohort,
                    $data['payment_method'] === 'admin_review' ? 'manual' : $data['payment_method'],
                    $data['wallet_id'] ?? null,
                    $startsAt
                );
            } else {
                return back()->withErrors(['payment_method' => 'اختر باقة أو دفعة.']);
            }
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['payment_method' => $e->getMessage()]);
        }

        // Zero amount or admin review: leave pending for admin; online: try course checkout pattern via order page
        if ((float) $order->amount <= 0 || $data['payment_method'] === 'admin_review') {
            if ((float) $order->amount <= 0) {
                $order->update(['status' => Order::STATUS_APPROVED, 'approved_at' => now()]);
                TutoringGroupCheckoutService::fulfillApprovedOrder(
                    $order->fresh(),
                    ! empty($data['starts_at']) ? Carbon::parse($data['starts_at']) : null
                );

                return redirect()
                    ->route('student.tutoring-subscriptions.index')
                    ->with('success', 'تم تفعيل الاشتراك.');
            }

            return redirect()
                ->route('orders.index')
                ->with('success', 'تم إنشاء الطلب وبانتظار مراجعة الإدارة.');
        }

        // For online payment, redirect to student orders — admin/gateway approval will fulfill
        return redirect()
            ->route('orders.index')
            ->with('success', 'تم إنشاء طلب الاشتراك #'.$order->id.' — أكمل الدفع أو انتظر التأكيد.');
    }
}
