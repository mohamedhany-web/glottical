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
                @endif

                <div class="md:col-span-2">
                    <label class="{{ $label }}" for="slotSelect">الموعد المتاح *</label>
                    <select name="scheduled_at" id="slotSelect" required class="{{ $field }}" disabled>
                        <option value="">اختر المعلم/المجموعة لتحميل المواعيد…</option>
                    </select>
                    <p id="slotsHint" class="mt-2 text-xs text-muted">المواعيد تُسحب من جدول توافر المعلم فقط.</p>
                </div>
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
  var packageStatus = document.getElementById('packageStatus');
  var slotsHint = document.getElementById('slotsHint');
  var submitBtn = document.getElementById('submitBtn');
  var preselectedEntitlement = @json((string) old('student_service_entitlement_id', $selectedEntitlementId ?: ''));

  function bindSearch(input, select) {
    if (!input || !select) return;
    var options = Array.prototype.slice.call(select.querySelectorAll('option'));
    function applyFilter() {
      var q = (input.value || '').trim().toLowerCase();
      options.forEach(function (opt) {
        if (!opt.value) {
          opt.hidden = false;
          return;
        }
        if (opt.selected) {
          opt.hidden = false;
          return;
        }
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

  function refreshSubmit() {
    var okStudent = !!studentSelect.value;
    var okEnt = !!entitlementSelect.value;
    var okSlot = !!slotSelect.value;
    var okTeacherOrGroup = mode === 'private'
      ? !!instructorSelect.value
      : !!(groupSelect && groupSelect.value);
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
        if (preselectedEntitlement) {
          entitlementSelect.value = preselectedEntitlement;
        }
        refreshSubmit();
      })
      .catch(function () {
        packageStatus.textContent = 'حدث خطأ أثناء التحقق من الباقة';
        packageStatus.className = 'mt-3 rounded-xl border border-danger/30 bg-danger/5 px-3 py-2.5 text-xs text-danger';
        refreshSubmit();
      });
  }

  function loadSlots() {
    slotSelect.disabled = true;
    setOptions(slotSelect, [], 'جارٍ تحميل المواعيد المتاحة…');
    slotsHint.textContent = 'المواعيد تُسحب من جدول توافر المعلم فقط.';
    slotsHint.className = 'mt-2 text-xs text-muted';
    refreshSubmit();

    var url = slotsUrl + '?mode=' + encodeURIComponent(mode);
    if (mode === 'private') {
      if (!instructorSelect.value) {
        setOptions(slotSelect, [], 'اختر معلماً أولاً…');
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
          slotsHint.textContent = data.message || '';
          refreshSubmit();
          return;
        }
        var slots = data.slots || [];
        if (!slots.length) {
          setOptions(slotSelect, [], 'لا مواعيد متاحة');
          slotsHint.textContent = data.empty_hint || 'لا مواعيد';
          slotsHint.className = 'mt-2 text-xs text-amber-700';
          refreshSubmit();
          return;
        }
        setOptions(slotSelect, slots.map(function (s) {
          return { value: s.starts_at, label: s.label || s.starts_at };
        }), 'اختر موعداً متاحاً…');
        slotSelect.disabled = false;
        slotsHint.textContent = 'تم العثور على ' + slots.length + ' موعد متاح · مدة الحصة ' + (data.duration_minutes || '') + ' دقيقة';
        slotsHint.className = 'mt-2 text-xs text-emerald-700';
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
  slotSelect.addEventListener('change', refreshSubmit);

  if (studentSelect.value) loadStudentContext();
  if (mode === 'private' && instructorSelect && instructorSelect.value) loadSlots();
  if (mode === 'group' && groupSelect && groupSelect.value) loadSlots();
  refreshSubmit();
})();
</script>
@endpush
