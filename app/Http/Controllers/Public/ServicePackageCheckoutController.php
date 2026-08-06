<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\ServicePackage;
use App\Models\ServicePackagePricingRule;
use App\Models\Wallet;
use App\Services\CustomServicePackagePricingService;
use App\Services\StudentEntitlementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ServicePackageCheckoutController extends Controller
{
    public function index(Request $request): View
    {
        $yearId = $request->filled('year') ? (int) $request->query('year') : null;
        $subjectId = $request->filled('subject') ? (int) $request->query('subject') : null;

        $selectedYear = null;
        $selectedSubject = null;
        if ($yearId && Schema::hasTable('academic_years')) {
            $selectedYear = AcademicYear::query()->active()->find($yearId);
            $yearId = $selectedYear?->id;
        }
        if ($subjectId && Schema::hasTable('academic_subjects')) {
            $selectedSubject = AcademicSubject::query()->active()->find($subjectId);
            $subjectId = $selectedSubject?->id;
            if ($selectedSubject?->academic_year_id && ! $yearId) {
                $yearId = (int) $selectedSubject->academic_year_id;
                $selectedYear = AcademicYear::query()->find($yearId);
            }
        }

        $commercialPackages = ServicePackage::query()
            ->commercial()
            ->forSchoolProgram($yearId, $subjectId)
            ->with(['academicYear:id,name,slug', 'academicSubject:id,name'])
            ->ordered()
            ->get();

        $planMatrix = ServicePackage::commercialCatalogMatrix($commercialPackages);

        $years = Schema::hasTable('academic_years')
            ? AcademicYear::query()->active()->ordered()->get(['id', 'name', 'slug', 'level_number'])
            : collect();

        $wallets = Wallet::query()->where('is_active', true)->orderBy('name')->get();

        return view('public.service-packages', [
            'planMatrix' => $planMatrix,
            'packages' => $commercialPackages,
            'wallets' => $wallets,
            'years' => $years,
            'selectedYear' => $selectedYear,
            'selectedSubject' => $selectedSubject,
            'yearId' => $yearId,
            'subjectId' => $subjectId,
            'pricingRules' => collect(),
        ]);
    }

    public function checkout(ServicePackage $servicePackage): View|RedirectResponse
    {
        abort_unless($servicePackage->is_active && ! $servicePackage->tutoring_group_id, 404);

        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'سجّل الدخول لشراء الباقة.');
        }

        $servicePackage->load(['academicYear:id,name', 'academicSubject:id,name']);
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
        return redirect()
            ->route('public.service-packages.index')
            ->with('error', 'الباقات التجارية (School / Private / Premier) هي العرض الحالي. اختر مدة الاشتراك من الصفحة.');
    }
}
