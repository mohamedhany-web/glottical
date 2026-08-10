@extends('layouts.admin')

@section('title', 'منح باقة يدوياً')
@section('page_title', 'منح باقة يدوياً')

@section('content')
@php
    $field = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $area = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $label = 'mb-1.5 block text-xs font-medium text-muted';
    $selectedUserId = (int) ($selectedUserId ?? 0);
    $selectedPackageId = (int) ($selectedPackageId ?? 0);
    $placementUrl = $placementUrl ?? null;
@endphp

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">الباقات والأسعار · اشتراك يدوي</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">منح باقة للطالب</h2>
            <p class="mt-1 text-sm text-muted">اختر الطالب والباقة ليتم تنزيل الرصيد فوراً على حسابه بدون دفع</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.service-packages.index') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-box-open text-xs"></i>
                الباقات
            </a>
            <a href="{{ route('admin.student-entitlements.index') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-wallet text-xs"></i>
                الأرصدة
            </a>
        </div>
    </section>

    @if(session('error'))
        <div class="rounded-xl border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.service-packages.grant.store') }}" class="space-y-5" id="manualPackageGrantForm">
        @csrf

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">1) الطالب</h3>
                <p class="mt-0.5 text-xs text-muted">ابحث بالاسم أو البريد أو الجوال أو رقم الدخول</p>
            </div>
            <div class="p-4 sm:p-5">
                <label class="{{ $label }}" for="studentSearch">بحث عن طالب</label>
                <input type="search" id="studentSearch" autocomplete="off"
                       placeholder="بحث بالاسم أو البريد أو الجوال أو ID…"
                       class="{{ $field }} mb-2"
                       aria-label="بحث عن طالب">
                <label class="{{ $label }}" for="userSelect">الطالب *</label>
                <select name="user_id" id="userSelect" required class="w-full rounded-xl border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20" size="8">
                    <option value="">اختر طالباً…</option>
                    @foreach($students as $student)
                        @php
                            $searchHaystack = mb_strtolower(
                                trim($student->id.' '.$student->name.' '.($student->email ?? '').' '.($student->phone ?? '')),
                                'UTF-8'
                            );
                        @endphp
                        <option value="{{ $student->id }}"
                                data-search="{{ e($searchHaystack) }}"
                                @selected((string) old('user_id', $selectedUserId) === (string) $student->id)>
                            #{{ $student->id }} — {{ $student->name }} — {{ $student->email }}@if($student->phone) · {{ $student->phone }}@endif
                        </option>
                    @endforeach
                </select>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">2) الباقة</h3>
                <p class="mt-0.5 text-xs text-muted">اختر باقة نشطة — الرصيد يُحسب من إعدادات الباقة تلقائياً</p>
            </div>
            <div class="p-4 sm:p-5 space-y-4">
                <div>
                    <label class="{{ $label }}" for="packageSearch">بحث عن باقة</label>
                    <input type="search" id="packageSearch" autocomplete="off"
                           placeholder="اسم الباقة…"
                           class="{{ $field }} mb-2">
                    <label class="{{ $label }}" for="packageSelect">الباقة *</label>
                    <select name="service_package_id" id="packageSelect" required class="w-full rounded-xl border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20" size="8">
                        <option value="">اختر باقة…</option>
                        @foreach($packages as $package)
                            @php
                                $unitsPreview = $package->isCommercialPlan()
                                    ? $package->computedUnitsForTerm()
                                    : (int) $package->units_count;
                                if ($package->isPremier()) {
                                    $g = max(1, (int) $package->weekly_group_sessions * 4 * max(1, (int) ($package->term_months ?: 1)));
                                    $p = max(1, (int) $package->weekly_private_sessions * 4 * max(1, (int) ($package->term_months ?: 1)));
                                    $unitsLabel = "جماعي {$g} + خاص {$p}";
                                } else {
                                    $unitsLabel = $unitsPreview.' حصة';
                                }
                                $searchHaystack = mb_strtolower(trim($package->name.' '.($package->badge ?? '').' '.($package->scope ?? '')), 'UTF-8');
                            @endphp
                            <option value="{{ $package->id }}"
                                    data-search="{{ e($searchHaystack) }}"
                                    data-units="{{ $package->isPremier() ? '' : $unitsPreview }}"
                                    data-scope="{{ $package->label() }}"
                                    data-plan="{{ $package->plan_type ? ($package->planLabel().' · '.$package->termLabel()) : '' }}"
                                    data-units-label="{{ $unitsLabel }}"
                                    data-validity="{{ $package->validityLabel() }}"
                                    @selected((string) old('service_package_id', $selectedPackageId) === (string) $package->id)>
                                {{ $package->name }}
                                @if($package->badge) · {{ $package->badge }}@endif
                                — {{ $unitsLabel }}
                                — {{ $package->formattedPrice() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="packagePreview" class="hidden rounded-xl border border-accent/20 bg-accent/5 px-4 py-3 text-sm text-ink">
                    <div class="font-semibold text-accent" id="previewName">—</div>
                    <div class="mt-1 grid gap-1 text-xs text-muted sm:grid-cols-2">
                        <div>النطاق: <span id="previewScope" class="font-semibold text-ink">—</span></div>
                        <div>الخطة: <span id="previewPlan" class="font-semibold text-ink">—</span></div>
                        <div>الحصص: <span id="previewUnits" class="font-semibold text-ink">—</span></div>
                        <div>الصلاحية: <span id="previewValidity" class="font-semibold text-ink">—</span></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="{{ $label }}" for="unitsOverride">تجاوز عدد الحصص (اختياري)</label>
                        <input type="number" name="units_override" id="unitsOverride" min="1" max="500"
                               value="{{ old('units_override') }}"
                               placeholder="اتركه فارغاً لاستخدام حصص الباقة"
                               class="{{ $field }}">
                        <p class="mt-1 text-[11px] text-muted">لباقة Premier التجاوز لا يُطبَّق — تُنزَّل حصص الجماعي والخاص معاً حسب الباقة</p>
                    </div>
                    <div>
                        <label class="{{ $label }}" for="notesInput">ملاحظة</label>
                        <textarea name="notes" id="notesInput" rows="3" class="{{ $area }}" placeholder="سبب المنح اليدوي…">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </article>

        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-line bg-surface px-4 py-4 shadow-soft sm:px-5">
            <label class="inline-flex items-center gap-2 text-sm text-ink">
                <input type="checkbox" name="go_placement" value="1" class="rounded border-line text-accent focus:ring-accent/30" @checked(old('go_placement'))>
                بعد المنح افتح صفحة التسكين
            </label>
            <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-semibold text-white shadow-soft transition hover:brightness-105">
                <i class="fas fa-download text-xs"></i>
                تنزيل الباقة للطالب
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function () {
    function bindSearch(inputId, selectId) {
        var input = document.getElementById(inputId);
        var select = document.getElementById(selectId);
        if (!input || !select) return;
        input.addEventListener('input', function () {
            var q = (input.value || '').trim().toLowerCase();
            Array.prototype.forEach.call(select.options, function (opt, idx) {
                if (idx === 0) { opt.hidden = false; return; }
                var hay = (opt.getAttribute('data-search') || opt.textContent || '').toLowerCase();
                opt.hidden = q !== '' && hay.indexOf(q) === -1;
            });
        });
    }

    bindSearch('studentSearch', 'userSelect');
    bindSearch('packageSearch', 'packageSelect');

    var packageSelect = document.getElementById('packageSelect');
    var preview = document.getElementById('packagePreview');
    function refreshPreview() {
        var opt = packageSelect && packageSelect.options[packageSelect.selectedIndex];
        if (!opt || !opt.value) {
            preview.classList.add('hidden');
            return;
        }
        preview.classList.remove('hidden');
        document.getElementById('previewName').textContent = (opt.textContent || '').split('—')[0].trim();
        document.getElementById('previewScope').textContent = opt.getAttribute('data-scope') || '—';
        document.getElementById('previewPlan').textContent = opt.getAttribute('data-plan') || '—';
        document.getElementById('previewUnits').textContent = opt.getAttribute('data-units-label') || '—';
        document.getElementById('previewValidity').textContent = opt.getAttribute('data-validity') || '—';
        var units = opt.getAttribute('data-units');
        var unitsInput = document.getElementById('unitsOverride');
        if (unitsInput && !unitsInput.value && units) {
            unitsInput.placeholder = 'افتراضي الباقة: ' + units;
        }
    }
    if (packageSelect) {
        packageSelect.addEventListener('change', refreshPreview);
        refreshPreview();
    }
})();
</script>
@endpush
@endsection
