<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ServicePackage;
use App\Models\ServicePackagePricingRule;
use App\Models\Wallet;
use App\Services\CustomServicePackagePricingService;
use App\Services\StudentEntitlementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServicePackageCheckoutController extends Controller
{
    public function index(): View
    {
        $packages = ServicePackage::query()
            ->publicCatalog()
            ->ordered()
            ->get();
        $pricingRules = ServicePackagePricingRule::query()->active()->ordered()->get();
        $wallets = Wallet::query()->where('is_active', true)->orderBy('name')->get();

        return view('public.service-packages', compact('packages', 'pricingRules', 'wallets'));
    }

    public function checkout(ServicePackage $servicePackage): View|RedirectResponse
    {
        abort_unless($servicePackage->is_active && ! $servicePackage->tutoring_group_id, 404);

        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'سجّل الدخول لشراء الباقة.');
        }

        $wallets = Wallet::query()->where('is_active', true)->orderBy('name')->get();

        return view('public.service-package-checkout', [
            'package' => $servicePackage,
            'wallets' => $wallets,
        ]);
    }

    public function store(Request $request, ServicePackage $servicePackage): RedirectResponse
    {
        abort_unless($servicePackage->is_active && ! $servicePackage->tutoring_group_id, 404);
        $user = $request->user();
        abort_unless($user, 403);

        $data = $request->validate([
            'payment_method' => ['required', 'in:online,wallet,bank_transfer,cash,other'],
            'wallet_id' => ['nullable', 'required_if:payment_method,bank_transfer,wallet', 'exists:wallets,id'],
        ]);

        $order = StudentEntitlementService::createOrder(
            $user,
            $servicePackage,
            $data['payment_method'],
            $data['wallet_id'] ?? null
        );

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'تم إنشاء طلب الباقة. بانتظار تأكيد الدفع.');
    }

    public function customQuote(Request $request)
    {
        $data = $request->validate([
            'pricing_rule_id' => ['required', 'integer', 'exists:service_package_pricing_rules,id'],
            'sessions' => ['required', 'integer', 'min:1', 'max:500'],
        ]);

        $rule = ServicePackagePricingRule::query()->active()->findOrFail($data['pricing_rule_id']);

        return response()->json(CustomServicePackagePricingService::calculate($rule, (int) $data['sessions']));
    }

    public function storeCustom(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'pricing_rule_id' => ['required', 'integer', 'exists:service_package_pricing_rules,id'],
            'sessions' => ['required', 'integer', 'min:1', 'max:500'],
            'payment_method' => ['required', 'in:online,wallet,bank_transfer,cash,other'],
            'wallet_id' => ['nullable', 'required_if:payment_method,bank_transfer,wallet', 'exists:wallets,id'],
        ]);

        $rule = ServicePackagePricingRule::query()->active()->findOrFail($data['pricing_rule_id']);
        CustomServicePackagePricingService::calculate($rule, (int) $data['sessions']);

        $order = StudentEntitlementService::createCustomOrder(
            $request->user(),
            $rule,
            (int) $data['sessions'],
            $data['payment_method'],
            $data['wallet_id'] ?? null,
        );

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'تم إنشاء طلب باقتك المخصصة بالدولار. بانتظار تأكيد الدفع.');
    }
}
