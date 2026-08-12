@extends('layouts.admin')

@section('title', 'تسكين جديد')
@section('page_title', 'تسكين طالب')

@section('content')
@php
    $field = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $area = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $label = 'mb-1.5 block text-xs font-medium text-muted';
    $mode = $mode ?? 'private';
@endphp

<div class="space-y-5" id="placementWizard"
     data-context-url="{{ route('admin.placement.student-context') }}"
     data-slots-url="{{ route('admin.placement.slots') }}"
     data-grant-url="{{ $grantUrl }}"
     data-mode="{{ $mode }}">

    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">الطلاب والخدمات · التسكين</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">تسكين طالب مع معلم</h2>
            <p class="mt-1 text-sm text-muted">يتحقق من الباقة وتوافر وقت المعلم قبل تثبيت الموعد</p>
        </div>
        <a href="{{ route('admin.placement.index') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
            <i class="fas fa-arrow-right text-xs"></i>
            رجوع للوحة التسكين
        </a>
    </section>

    @include('admin.partials.workflow-guide', [
        'title' => 'معالج التسكين',
        'body' => 'للحصص الفردية يُفضّل التثبيت الشهري (موعدان أسبوعياً) بدل حجز حصة بحصة — أخف على الطالب والمعلم.',
        'steps' => [
            'اختر الطالب والباقة (الرصيد يجب أن يكفي عدد الحصص).',
            'للفردي: ثبّت شهرياً، أو احجز عدة مواعيد، أو حصة واحدة.',
            'اختر معلماً من جدول توافره فقط.',
            'راجع الملخص وثبّت التسكين.',
        ],
    ])

    @if(session('error'))
        <div class="rounded-xl border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">{{ $errors->first() }}</div>
    @endif

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.placement.create', array_filter(['mode' => 'private', 'student_id' => $selectedStudentId ?: null])) }}"
           class="btn-press inline-flex h-9 items-center gap-2 rounded-xl px-4 text-sm font-medium {{ $mode === 'private' ? 'bg-accent text-white' : 'border border-line bg-surface text-ink-soft hover:border-accent/30 hover:text-accent' }}">
            <i class="fas fa-user text-xs"></i> فردي 1:1
        </a>
        <a href="{{ route('admin.placement.create', array_filter(['mode' => 'group', 'student_id' => $selectedStudentId ?: null])) }}"
           class="btn-press inline-flex h-9 items-center gap-2 rounded-xl px-4 text-sm font-medium {{ $mode === 'group' ? 'bg-accent text-white' : 'border border-line bg-surface text-ink-soft hover:border-accent/30 hover:text-accent' }}">
            <i class="fas fa-users text-xs"></i> مجموعات
        </a>
    </div>

    <form method="POST" action="{{ route('admin.placement.store') }}" class="space-y-5" id="placementForm">
        @csrf
        <input type="hidden" name="mode" value="{{ $mode }}">

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">1) الطالب والباقة</h3>
                <p class="mt-0.5 text-xs text-muted">ابحث عن الطالب ثم اختر الرصيد القابل للحجز</p>
            </div>
            <div class="grid grid-cols-1 gap-5 p-4 sm:p-5 md:grid-cols-2">
                <div>
                    <label class="{{ $label }}" for="studentSearch">الطالب *</label>
                    <input type="search" id="studentSearch" autocomplete="off"
                           placeholder="بحث بالاسم أو البريد أو الجوال…"
                           class="{{ $field }} mb-2"
                           aria-label="بحث عن طالب">
                    <select name="student_id" id="studentSelect" required class="w-full rounded-xl border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20" size="8">
                        <option value="">اختر طالباً…</option>
                        @foreach($students as $student)
                            @php
                                $searchHaystack = mb_strtolower(
                                    trim($student->name.' '.($student->email ?? '').' '.($student->phone ?? '')),
                                    'UTF-8'
                                );
                            @endphp
                            <option value="{{ $student->id }}"
                                    data-search="{{ e($searchHaystack) }}"
                                    @selected((string) old('student_id', $selectedStudentId) === (string) $student->id)>
                                {{ $student->name }} — {{ $student->email }}@if($student->phone) · {{ $student->phone }}@endif
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1.5 text-[11px] text-muted">اكتب في البحث لتقليص القائمة ثم اختر الطالب</p>
                </div>

                <div>
                    <label class="{{ $label }}" for="entitlementSelect">الباقة / الرصيد *</label>
                    <select name="student_service_entitlement_id" id="entitlementSelect" required class="{{ $field }}" disabled>
                        <option value="">اختر الطالب أولاً…</option>
                    </select>
                    <div id="packageStatus" class="mt-3 rounded-xl border border-line bg-canvas/60 px-3 py-2.5 text-xs text-muted">
                        سيظهر هنا إن كان الطالب مشتركاً في باقة أم لا.
                    </div>
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">
                    @if($mode === 'private')
                        2) المعلم والموعد
                    @else
                        2) المجموعة والموعد
                    @endif
                </h3>
                <p class="mt-0.5 text-xs text-muted">المواعيد تظهر فقط من جدول توافر المعلم</p>
            </div>
            <div class="grid grid-cols-1 gap-5 p-4 sm:p-5 md:grid-cols-2">
                @if($mode === 'private')
                    <div class="md:col-span-2">
                        <label class="{{ $label }}" for="instructorSearch">المعلم *</label>
                        <input type="search" id="instructorSearch" autocomplete="off"
                               placeholder="بحث عن معلم بالاسم أو البريد…"
                               class="{{ $field }} mb-2"
                               aria-label="بحث عن معلم">
                        <select name="instructor_id" id="instructorSelect" required class="{{ $field }}">
                            <option value="">اختر معلماً…</option>
                            @foreach($instructors as $instructor)
                                @php
                                    $iSearch = mb_strtolower(trim($instructor->name.' '.($instructor->email ?? '')), 'UTF-8');
                                @endphp
                                <option value="{{ $instructor->id }}"
                                        data-search="{{ e($iSearch) }}"
                                        @selected((string) old('instructor_id') === (string) $instructor->id)>
                                    {{ $instructor->name }} — {{ $instructor->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <p class="{{ $label }}">طريقة التثبيت *</p>
                        <div class="grid gap-2 sm:grid-cols-3">
                            <label class="flex cursor-pointer items-start gap-2 rounded-xl border border-line bg-canvas/50 p-3 has-[:checked]:border-accent has-[:checked]:bg-accent-soft/40">
                                <input type="radio" name="booking_style" value="monthly" class="mt-1" {{ old('booking_style', 'monthly') === 'monthly' ? 'checked' : '' }}>
                                <span>
                                    <span class="block text-sm font-semibold text-ink">تثبيت شهري</span>
                                    <span class="mt-0.5 block text-[11px] text-muted">موعدان أسبوعياً يتكرران 4 أسابيع</span>
                                </span>
                            </label>
                            <label class="flex cursor-pointer items-start gap-2 rounded-xl border border-line bg-canvas/50 p-3 has-[:checked]:border-accent has-[:checked]:bg-accent-soft/40">
                                <input type="radio" name="booking_style" value="multi" class="mt-1" {{ old('booking_style') === 'multi' ? 'checked' : '' }}>
                                <span>
                                    <span class="block text-sm font-semibold text-ink">عدة مواعيد</span>
                                    <span class="mt-0.5 block text-[11px] text-muted">اختر أكثر من حصة دفعة واحدة</span>
                                </span>
                            </label>
                            <label class="flex cursor-pointer items-start gap-2 rounded-xl border border-line bg-canvas/50 p-3 has-[:checked]:border-accent has-[:checked]:bg-accent-soft/40">
                                <input type="radio" name="booking_style" value="single" class="mt-1" {{ old('booking_style') === 'single' ? 'checked' : '' }}>
                                <span>
                                    <span class="block text-sm font-semibold text-ink">حصة واحدة</span>
                                    <span class="mt-0.5 block text-[11px] text-muted">للحالات الاستثنائية فقط</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div id="monthlyPanel" class="md:col-span-2 space-y-3">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="{{ $label }}" for="weeklySlot0">الموعد الأسبوعي 1 *</label>
                                <select name="weekly_slots[0][combo]" id="weeklySlot0" class="{{ $field }}" disabled>
                                    <option value="">اختر المعلم أولاً…</option>
                                </select>
                                <input type="hidden" name="weekly_slots[0][day_of_week]" id="weeklyDay0" value="{{ old('weekly_slots.0.day_of_week') }}">
                                <input type="hidden" name="weekly_slots[0][time]" id="weeklyTime0" value="{{ old('weekly_slots.0.time') }}">
                            </div>
                            <div>
                                <label class="{{ $label }}" for="weeklySlot1">الموعد الأسبوعي 2 (مستحسن)</label>
                                <select name="weekly_slots[1][combo]" id="weeklySlot1" class="{{ $field }}" disabled>
                                    <option value="">اختياري…</option>
                                </select>
                                <input type="hidden" name="weekly_slots[1][day_of_week]" id="weeklyDay1" value="{{ old('weekly_slots.1.day_of_week') }}">
                                <input type="hidden" name="weekly_slots[1][time]" id="weeklyTime1" value="{{ old('weekly_slots.1.time') }}">
                            </div>
                        </div>
                        <div class="max-w-xs">
                            <label class="{{ $label }}" for="weeksSelect">عدد الأسابيع</label>
                            <select name="weeks" id="weeksSelect" class="{{ $field }}">
                                @foreach([4, 3, 2, 5, 6, 8] as $w)
                                    <option value="{{ $w }}" @selected((string) old('weeks', 4) === (string) $w)>{{ $w }} أسابيع ≈ {{ $w * 2 }} حصة بموعدين</option>
                                @endforeach
                            </select>
                        </div>
                        <p id="monthlyHint" class="text-xs text-muted">سيُولَّد الجدول تلقائياً مثل الفصل ويُحجز من الرصيد دفعة واحدة.</p>
                    </div>

                    <div id="multiPanel" class="md:col-span-2 hidden">
                        <label class="{{ $label }}" for="multiSlotSelect">المواعيد المتاحة (متعدد) *</label>
                        <select name="scheduled_ats[]" id="multiSlotSelect" multiple size="8" class="w-full rounded-xl border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20" disabled>
                            <option value="" disabled>اختر المعلم لتحميل المواعيد…</option>
                        </select>
                        <p id="multiHint" class="mt-2 text-xs text-muted">اضغط Ctrl/Cmd لاختيار أكثر من موعد مع نفس المعلم.</p>
                    </div>

                    <div id="singlePanel" class="md:col-span-2 hidden">
                        <label class="{{ $label }}" for="slotSelect">الموعد المتاح *</label>
                        <select name="scheduled_at" id="slotSelect" class="{{ $field }}" disabled>
                            <option value="">اختر المعلم لتحميل المواعيد…</option>
                        </select>
                        <p id="slotsHint" class="mt-2 text-xs text-muted">المواعيد تُسحب من جدول توافر المعلم فقط.</p>
                    </div>
                @else
                    <div>
                        <label class="{{ $label }}" for="groupSearch">المجموعة *</label>
                        <input type="search" id="groupSearch" autocomplete="off"
                               placeholder="بحث باسم المجموعة أو المعلم…"
                               class="{{ $field }} mb-2"
                               aria-label="بحث عن مجموعة">
                        <select name="tutoring_group_id" id="groupSelect" required class="{{ $field }}">
                            <option value="">اختر مجموعة…</option>
                            @foreach($groups as $group)
                                @php
                                    $gSearch = mb_strtolower(trim($group->title.' '.($group->instructor?->name ?? '').' '.$group->type), 'UTF-8');
                                @endphp
                                <option value="{{ $group->id }}"
                                        data-instructor="{{ $group->instructor_id }}"
                                        data-type="{{ $group->type }}"
                                        data-search="{{ e($gSearch) }}"
                                        @selected((string) old('tutoring_group_id') === (string) $group->id)>
                                    {{ $group->title }} — {{ $group->typeLabel() }} — {{ $group->instructor?->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $label }}" for="instructorSelect">المعلم (من المجموعة أو بديل)</label>
                        <select name="instructor_id" id="instructorSelect" class="{{ $field }}">
                            <option value="">من المجموعة تلقائياً</option>
                            @foreach($instructors as $instructor)
                                <option value="{{ $instructor->id }}" @selected((string) old('instructor_id') === (string) $instructor->id)>
                                    {{ $instructor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="{{ $label }}" for="slotSelect">الموعد المتاح *</label>
                        <select name="scheduled_at" id="slotSelect" required class="{{ $field }}" disabled>
                            <option value="">اختر المجموعة لتحميل المواعيد…</option>
                        </select>
                        <p id="slotsHint" class="mt-2 text-xs text-muted">المواعيد تُسحب من جدول توافر المعلم فقط.</p>
                    </div>
                @endif
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">3) ملاحظات</h3>
                <p class="mt-0.5 text-xs text-muted">اختياري — للمراجعة الداخلية فقط</p>
            </div>
            <div class="p-4 sm:p-5">
                <label class="{{ $label }}" for="notes">ملاحظات داخلية</label>
                <textarea name="notes" id="notes" rows="3" class="{{ $area }}" placeholder="مثال: تسكين من مكالمة ولي الأمر…">{{ old('notes') }}</textarea>
            </div>
        </article>

        <div class="flex flex-wrap gap-2">
            <button type="submit" id="submitBtn" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white disabled:cursor-not-allowed disabled:opacity-50" disabled>
                <i class="fas fa-user-check text-xs"></i>
                تأكيد التسكين
            </button>
            <a href="{{ route('admin.placement.index') }}" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
  var root = document.getElementById('placementWizard');
  if (!root) return;

  var contextUrl = root.dataset.contextUrl;
  var slotsUrl = root.dataset.slotsUrl;
  var grantUrl = root.dataset.grantUrl || '';
  var mode = root.dataset.mode || 'private';

  var studentSelect = document.getElementById('studentSelect');
  var studentSearch = document.getElementById('studentSearch');
  var entitlementSelect = document.getElementById('entitlementSelect');
  var instructorSelect = document.getElementById('instructorSelect');
  var instructorSearch = document.getElementById('instructorSearch');
  var groupSelect = document.getElementById('groupSelect');
  var groupSearch = document.getElementById('groupSearch');
  var slotSelect = document.getElementById('slotSelect');
  var multiSlotSelect = document.getElementById('multiSlotSelect');
  var weeklySlot0 = document.getElementById('weeklySlot0');
  var weeklySlot1 = document.getElementById('weeklySlot1');
  var weeklyDay0 = document.getElementById('weeklyDay0');
  var weeklyTime0 = document.getElementById('weeklyTime0');
  var weeklyDay1 = document.getElementById('weeklyDay1');
  var weeklyTime1 = document.getElementById('weeklyTime1');
  var monthlyPanel = document.getElementById('monthlyPanel');
  var multiPanel = document.getElementById('multiPanel');
  var singlePanel = document.getElementById('singlePanel');
  var monthlyHint = document.getElementById('monthlyHint');
  var multiHint = document.getElementById('multiHint');
  var packageStatus = document.getElementById('packageStatus');
  var slotsHint = document.getElementById('slotsHint');
  var submitBtn = document.getElementById('submitBtn');
  var preselectedEntitlement = @json((string) old('student_service_entitlement_id', $selectedEntitlementId ?: ''));

  function bookingStyle() {
    var el = document.querySelector('input[name="booking_style"]:checked');
    return el ? el.value : 'single';
  }

  function bindSearch(input, select) {
    if (!input || !select) return;
    var options = Array.prototype.slice.call(select.querySelectorAll('option'));
    function applyFilter() {
      var q = (input.value || '').trim().toLowerCase();
      options.forEach(function (opt) {
        if (!opt.value) { opt.hidden = false; return; }
        if (opt.selected) { opt.hidden = false; return; }
        var hay = (opt.getAttribute('data-search') || opt.textContent || '').toLowerCase();
        opt.hidden = q.length > 0 && hay.indexOf(q) === -1;
      });
    }
    input.addEventListener('input', applyFilter);
    input.addEventListener('search', applyFilter);
    select.addEventListener('change', applyFilter);
  }

  bindSearch(studentSearch, studentSelect);
  bindSearch(instructorSearch, instructorSelect);
  bindSearch(groupSearch, groupSelect);

  function setOptions(select, options, placeholder) {
    if (!select) return;
    select.innerHTML = '';
    var ph = document.createElement('option');
    ph.value = '';
    ph.textContent = placeholder;
    select.appendChild(ph);
    options.forEach(function (opt) {
      var o = document.createElement('option');
      o.value = opt.value;
      o.textContent = opt.label;
      if (opt.disabled) o.disabled = true;
      select.appendChild(o);
    });
  }

  function syncWeeklyHidden(selectEl, dayEl, timeEl) {
    if (!selectEl || !dayEl || !timeEl) return;
    var v = selectEl.value || '';
    if (!v) { dayEl.value = ''; timeEl.value = ''; return; }
    var parts = v.split('|');
    dayEl.value = parts[0] || '';
    timeEl.value = parts[1] || '';
  }

  function syncPanels() {
    if (mode !== 'private') return;
    var style = bookingStyle();
    if (monthlyPanel) monthlyPanel.classList.toggle('hidden', style !== 'monthly');
    if (multiPanel) multiPanel.classList.toggle('hidden', style !== 'multi');
    if (singlePanel) singlePanel.classList.toggle('hidden', style !== 'single');
    if (slotSelect) slotSelect.required = style === 'single';
    if (weeklySlot0) weeklySlot0.required = style === 'monthly';
    refreshSubmit();
  }

  function refreshSubmit() {
    var okStudent = !!studentSelect.value;
    var okEnt = !!entitlementSelect.value;
    var okTeacherOrGroup = mode === 'private'
      ? !!instructorSelect.value
      : !!(groupSelect && groupSelect.value);
    var okSlot = false;
    if (mode === 'group') {
      okSlot = !!(slotSelect && slotSelect.value);
    } else {
      var style = bookingStyle();
      if (style === 'monthly') {
        okSlot = !!(weeklySlot0 && weeklySlot0.value);
      } else if (style === 'multi') {
        okSlot = !!(multiSlotSelect && multiSlotSelect.selectedOptions && multiSlotSelect.selectedOptions.length > 0);
      } else {
        okSlot = !!(slotSelect && slotSelect.value);
      }
    }
    submitBtn.disabled = !(okStudent && okEnt && okSlot && okTeacherOrGroup);
  }

  function loadStudentContext() {
    entitlementSelect.disabled = true;
    setOptions(entitlementSelect, [], 'جارٍ التحقق من الباقة…');
    packageStatus.textContent = 'جارٍ التحقق…';
    packageStatus.className = 'mt-3 rounded-xl border border-line bg-canvas/60 px-3 py-2.5 text-xs text-muted';
    refreshSubmit();

    if (!studentSelect.value) {
      setOptions(entitlementSelect, [], 'اختر الطالب أولاً…');
      packageStatus.textContent = 'سيظهر هنا إن كان الطالب مشتركاً في باقة أم لا.';
      refreshSubmit();
      return;
    }

    fetch(contextUrl + '?student_id=' + encodeURIComponent(studentSelect.value), {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (!data.ok) {
          packageStatus.textContent = data.message || 'تعذر تحميل رصيد الطالب';
          packageStatus.className = 'mt-3 rounded-xl border border-danger/30 bg-danger/5 px-3 py-2.5 text-xs text-danger';
          setOptions(entitlementSelect, [], 'لا رصيد');
          refreshSubmit();
          return;
        }

        var bookable = (data.bookable_entitlements || []).filter(function (e) {
          if (mode === 'private') return e.kind === 'private';
          return e.kind === 'group' || e.scope === 'global';
        });

        if (!data.has_package || bookable.length === 0) {
          packageStatus.innerHTML = 'الطالب <strong>غير مشترك</strong> في باقة مناسبة لهذا النوع من التسكين.'
            + (grantUrl ? ' <a class="font-semibold text-accent underline" href="' + grantUrl + (studentSelect.value ? '?user_id=' + studentSelect.value : '') + '">منح رصيد الآن</a>' : '');
          packageStatus.className = 'mt-3 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5 text-xs text-amber-800';
          setOptions(entitlementSelect, [], 'لا يوجد رصيد قابل للحجز');
          entitlementSelect.disabled = true;
          refreshSubmit();
          return;
        }

        packageStatus.textContent = 'مشترك · خاص: ' + (data.private_units || 0) + ' · مجموعات: ' + (data.group_units || 0);
        packageStatus.className = 'mt-3 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-xs text-emerald-800';

        setOptions(entitlementSelect, bookable.map(function (e) {
          return {
            value: String(e.id),
            label: (e.package || e.scope_label) + ' — ' + e.units_left + ' حصة'
              + (e.group_title ? ' — ' + e.group_title : '')
              + (e.expires_at ? ' — حتى ' + e.expires_at : '')
          };
        }), 'اختر الرصيد…');
        entitlementSelect.disabled = false;
        if (preselectedEntitlement) entitlementSelect.value = preselectedEntitlement;
        refreshSubmit();
      })
      .catch(function () {
        packageStatus.textContent = 'حدث خطأ أثناء التحقق من الباقة';
        packageStatus.className = 'mt-3 rounded-xl border border-danger/30 bg-danger/5 px-3 py-2.5 text-xs text-danger';
        refreshSubmit();
      });
  }

  function fillWeeklyFromWindows(windows) {
    var opts = (windows || []).map(function (w) {
      return { value: w.day_of_week + '|' + w.start_time, label: w.label || (w.day_label + ' · ' + w.start_time) };
    });
    setOptions(weeklySlot0, opts, 'اختر الموعد الأسبوعي…');
    setOptions(weeklySlot1, opts, 'اختياري — موعد ثانٍ…');
    if (weeklySlot0) weeklySlot0.disabled = opts.length === 0;
    if (weeklySlot1) weeklySlot1.disabled = opts.length === 0;
    if (monthlyHint) {
      monthlyHint.textContent = opts.length
        ? 'اختر موعدين من نوافذ المعلم — سيُولَّد الجدول لعدد الأسابيع المحدد.'
        : 'لا نوافذ أسبوعية لهذا المعلم. اضبط جدول توافر 1:1 أولاً.';
      monthlyHint.className = opts.length ? 'text-xs text-emerald-700' : 'text-xs text-amber-700';
    }
  }

  function loadSlots() {
    if (slotSelect) {
      slotSelect.disabled = true;
      setOptions(slotSelect, [], 'جارٍ تحميل المواعيد المتاحة…');
    }
    if (multiSlotSelect) {
      multiSlotSelect.disabled = true;
      multiSlotSelect.innerHTML = '';
    }
    if (slotsHint) {
      slotsHint.textContent = 'المواعيد تُسحب من جدول توافر المعلم فقط.';
      slotsHint.className = 'mt-2 text-xs text-muted';
    }
    refreshSubmit();

    var url = slotsUrl + '?mode=' + encodeURIComponent(mode);
    if (mode === 'private') {
      if (!instructorSelect.value) {
        setOptions(slotSelect, [], 'اختر معلماً أولاً…');
        setOptions(weeklySlot0, [], 'اختر معلماً أولاً…');
        setOptions(weeklySlot1, [], 'اختياري…');
        if (weeklySlot0) weeklySlot0.disabled = true;
        if (weeklySlot1) weeklySlot1.disabled = true;
        refreshSubmit();
        return;
      }
      url += '&instructor_id=' + encodeURIComponent(instructorSelect.value);
    } else {
      if (!groupSelect || !groupSelect.value) {
        setOptions(slotSelect, [], 'اختر مجموعة أولاً…');
        refreshSubmit();
        return;
      }
      url += '&tutoring_group_id=' + encodeURIComponent(groupSelect.value);
      var opt = groupSelect.options[groupSelect.selectedIndex];
      if (opt && opt.dataset.instructor && instructorSelect && !instructorSelect.value) {
        instructorSelect.value = opt.dataset.instructor;
      }
    }

    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (!data.ok) {
          setOptions(slotSelect, [], data.message || 'تعذر التحميل');
          if (slotsHint) slotsHint.textContent = data.message || '';
          refreshSubmit();
          return;
        }
        var slots = data.slots || [];
        if (mode === 'private') {
          var fromSlots = {};
          (slots || []).forEach(function (s) {
            var key = (s.day_of_week || '') + '|' + (s.time || '');
            if (s.day_of_week && s.time && !fromSlots[key]) {
              fromSlots[key] = {
                day_of_week: s.day_of_week,
                start_time: s.time,
                label: (data.day_labels && data.day_labels[s.day_of_week] ? data.day_labels[s.day_of_week] : ('يوم ' + s.day_of_week)) + ' · ' + s.time
              };
            }
          });
          var slotWindows = Object.keys(fromSlots).map(function (k) { return fromSlots[k]; });
          fillWeeklyFromWindows(slotWindows.length ? slotWindows : (data.weekly_windows || []));
          if (multiSlotSelect) {
            multiSlotSelect.innerHTML = '';
            slots.forEach(function (s) {
              var o = document.createElement('option');
              o.value = s.starts_at;
              o.textContent = s.label || s.starts_at;
              multiSlotSelect.appendChild(o);
            });
            multiSlotSelect.disabled = slots.length === 0;
            if (multiHint) {
              multiHint.textContent = slots.length
                ? 'اختر عدة مواعيد (Ctrl/Cmd) · ' + slots.length + ' متاح'
                : (data.empty_hint || 'لا مواعيد');
            }
          }
        }

        if (!slots.length) {
          setOptions(slotSelect, [], 'لا مواعيد متاحة');
          if (slotsHint) {
            slotsHint.textContent = data.empty_hint || 'لا مواعيد';
            slotsHint.className = 'mt-2 text-xs text-amber-700';
          }
          refreshSubmit();
          return;
        }
        setOptions(slotSelect, slots.map(function (s) {
          return { value: s.starts_at, label: s.label || s.starts_at };
        }), 'اختر موعداً متاحاً…');
        if (slotSelect) slotSelect.disabled = false;
        if (slotsHint) {
          slotsHint.textContent = 'تم العثور على ' + slots.length + ' موعد متاح · مدة الحصة ' + (data.duration_minutes || '') + ' دقيقة';
          slotsHint.className = 'mt-2 text-xs text-emerald-700';
        }
        refreshSubmit();
      })
      .catch(function () {
        setOptions(slotSelect, [], 'خطأ في التحميل');
        refreshSubmit();
      });
  }

  studentSelect.addEventListener('change', loadStudentContext);
  entitlementSelect.addEventListener('change', refreshSubmit);
  if (instructorSelect) instructorSelect.addEventListener('change', function () {
    if (mode === 'private') loadSlots();
    else refreshSubmit();
  });
  if (groupSelect) groupSelect.addEventListener('change', loadSlots);
  if (slotSelect) slotSelect.addEventListener('change', refreshSubmit);
  if (multiSlotSelect) multiSlotSelect.addEventListener('change', refreshSubmit);
  if (weeklySlot0) weeklySlot0.addEventListener('change', function () {
    syncWeeklyHidden(weeklySlot0, weeklyDay0, weeklyTime0);
    refreshSubmit();
  });
  if (weeklySlot1) weeklySlot1.addEventListener('change', function () {
    syncWeeklyHidden(weeklySlot1, weeklyDay1, weeklyTime1);
    refreshSubmit();
  });
  document.querySelectorAll('input[name="booking_style"]').forEach(function (el) {
    el.addEventListener('change', syncPanels);
  });

  if (studentSelect.value) loadStudentContext();
  if (mode === 'private' && instructorSelect && instructorSelect.value) loadSlots();
  if (mode === 'group' && groupSelect && groupSelect.value) loadSlots();
  syncPanels();
  refreshSubmit();
})();
</script>
@endpush
