@extends('layouts.employee')

@section('title', 'عميل محتمل #'.$lead->id)
@section('header', 'CRM — '.$lead->name)

@section('content')
<div class="space-y-6 max-w-3xl">
  @include('partials.crm-employee-nav', ['role' => $role])

  @if(session('success'))<div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>@endif
  @if($errors->any())<div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">{{ $errors->first() }}</div>@endif

  <div class="rounded-2xl border bg-white p-6 space-y-3">
    <div class="flex justify-between gap-3"><h2 class="text-xl font-black">{{ $lead->name }}</h2><span class="text-sm font-bold bg-gray-100 px-2 py-1 rounded">{{ $lead->status_label }}</span></div>
    <p class="text-sm text-gray-600">مالك التسويق: <strong>{{ $lead->marketingOwner?->name ?? '—' }}</strong> (لا يتغيّر)</p>
    @if($lead->assignedTo)<p class="text-sm">موظف المبيعات: <strong>{{ $lead->assignedTo->name }}</strong></p>@endif
    @if($lead->email || $lead->phone)<p class="text-sm text-gray-600">{{ $lead->email }} @if($lead->phone) — {{ $lead->phone }}@endif</p>@endif
    @if($lead->interestedCourse)<p class="text-sm">كورس الاهتمام: <strong>{{ $lead->interestedCourse->title }}</strong></p>@endif
    @if($lead->notes)<div class="text-sm whitespace-pre-wrap bg-gray-50 rounded-lg p-3">{{ $lead->notes }}</div>@endif
  </div>

  @include('partials.crm-pipeline-strip', ['pipelineLead' => $lead])

  @if(!empty($canSubmitToSales))
  <form method="POST" action="{{ route('employee.crm.leads.submit-to-sales', $lead) }}" class="rounded-2xl border border-teal-200 bg-teal-50 p-6 space-y-3">
    @csrf
    <h3 class="font-bold text-teal-900">إرسال للمبيعات</h3>
    <p class="text-sm text-teal-800">أرحّل بيانات هذا العميل لصندوق المبيعات. ستبقى مالكاً للعمولة ({{ number_format((float) config('crm.commission_rates.marketing', 5), 1) }}٪) إذا اشترك.</p>
    <textarea name="note" rows="2" class="w-full rounded-lg border px-3 py-2" placeholder="ملاحظة للمندوب (اختياري)"></textarea>
    @error('submit')<p class="text-rose-600 text-xs">{{ $message }}</p>@enderror
    <button class="px-4 py-2 rounded-xl bg-teal-600 text-white font-bold text-sm">إرسال لصندوق المبيعات</button>
  </form>
  @elseif($role === 'marketing' && $lead->submitted_to_sales_at && ! $lead->assigned_to)
    <div class="rounded-xl bg-amber-50 border border-amber-200 text-amber-900 px-4 py-3 text-sm">تم الإرسال للمبيعات في {{ $lead->submitted_to_sales_at->format('Y-m-d H:i') }} — بانتظار استلام مندوب.</div>
  @elseif($role === 'marketing' && $lead->assignedTo)
    <div class="rounded-xl bg-sky-50 border border-sky-200 text-sky-900 px-4 py-3 text-sm">عند المبيعات: <strong>{{ $lead->assignedTo->name }}</strong> — تابع التقدم من الحالة أدناه.</div>
  @endif

  @if(!empty($canClaimFromMarketing))
  <form method="POST" action="{{ route('employee.crm.marketing-inbox.claim', $lead) }}" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 space-y-2">
    @csrf
    <h3 class="font-bold text-emerald-900">استلام من المسوق</h3>
    <p class="text-sm text-emerald-800">المسوق: <strong>{{ $lead->marketingOwner?->name ?? '—' }}</strong> — استلم العميل لبدء المتابعة في الـ Pipeline.</p>
    <button class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-sm">استلام العميل</button>
  </form>
  @endif

  @if($role === 'team_leader')
    <div class="rounded-xl bg-sky-50 border border-sky-200 text-sky-900 px-4 py-3 text-sm flex flex-wrap justify-between gap-2 items-center">
      <span>يمكنك متابعة الفريق وتعيين العملاء وتحديث حالات البيع وإضافة ملاحظات.</span>
      @if(\App\Services\Crm\CrmAccessService::canViewTeamPerformance(auth()->user()))
        <a href="{{ route('employee.crm.team.index') }}" class="font-bold text-sky-700 underline">عرض أداء الأعضاء</a>
      @endif
    </div>
  @endif

  @if(!empty($canAssign) && !$lead->isClosed())
    @if($salesUsers->isNotEmpty())
    <form method="POST" action="{{ route('employee.crm.leads.assign', $lead) }}" class="rounded-2xl border border-sky-200 bg-sky-50 p-6 space-y-3">
      @csrf
      <h3 class="font-bold text-sky-900">تعيين لموظف مبيعات</h3>
      <select name="assigned_to" class="w-full rounded-lg border px-3 py-2" required>
        <option value="">اختر موظف المبيعات</option>
        @foreach($salesUsers as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
      </select>
      @error('assigned_to')<p class="text-rose-600 text-xs">{{ $message }}</p>@enderror
      <button class="px-4 py-2 rounded-xl bg-sky-600 text-white font-bold text-sm">تعيين العميل</button>
    </form>
    @else
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-950 space-y-2">
      <p class="font-bold">لا يوجد موظفو مبيعات في فريقك حالياً</p>
      <p>لإظهار زر التحويل للسيلز، أضف عضواً بدور <strong>مبيعات</strong> من صفحة الفريق، أو اطلب من الإدارة تعيين أعضاء لمجموعتك.</p>
      @if(\App\Services\Crm\CrmAccessService::canManageTeam(auth()->user()))
        <a href="{{ route('employee.crm.team.index') }}" class="inline-flex font-bold text-amber-800 underline">إدارة أعضاء الفريق</a>
      @endif
    </div>
    @endif
  @endif

  @if(\App\Services\Crm\CrmAccessService::canUseMessages(auth()->user()))
  <a href="{{ route('employee.crm.messages.lead', $lead) }}" class="inline-flex items-center gap-2 text-sm text-cyan-700 font-bold">محادثة الفريق حول هذا العميل</a>
  @endif

  @if($canEdit)
  <form method="POST" action="{{ route('employee.crm.leads.update', $lead) }}" class="rounded-2xl border bg-white p-6 space-y-3">
    @csrf @method('PUT')
    <h3 class="font-bold">تعديل البيانات</h3>
    <input name="name" value="{{ $lead->name }}" class="w-full rounded-lg border px-3 py-2" required>
    <input name="email" value="{{ $lead->email }}" class="w-full rounded-lg border px-3 py-2" placeholder="البريد">
    <input name="phone" value="{{ $lead->phone }}" class="w-full rounded-lg border px-3 py-2" placeholder="الهاتف">
    <select name="source" class="w-full rounded-lg border px-3 py-2">
      @foreach(\App\Models\SalesLead::sourceLabels() as $k=>$l)<option value="{{ $k }}" @selected($lead->source===$k)>{{ $l }}</option>@endforeach
    </select>
    <button class="px-4 py-2 rounded-xl bg-sky-600 text-white font-bold text-sm">حفظ التعديلات</button>
  </form>
  @endif

  @if(!empty($canTransition) && !empty($nextStatuses) && !$lead->isClosed())
  <form method="POST" action="{{ route('employee.crm.leads.transition', $lead) }}" class="rounded-2xl border bg-white p-6 space-y-3">
    @csrf
    <h3 class="font-bold">تحديث الحالة</h3>
    <p class="text-xs text-gray-500">يمكنك الانتقال إلى أي مرحلة لاحقة في مسار البيع (أو إغلاق كخاسر). تأكيد الدفع والتسجيل تتم من المالية/الإدارة.</p>
    <select name="status" class="w-full rounded-lg border px-3 py-2" required>
      <option value="">اختر الحالة</option>
      @foreach($nextStatuses as $st)
        <option value="{{ $st }}">{{ \App\Models\SalesLead::statusLabels()[$st] ?? $st }}</option>
      @endforeach
    </select>
    <textarea name="note" rows="2" class="w-full rounded-lg border px-3 py-2" placeholder="ملاحظة عن هذه الخطوة"></textarea>
    <button class="px-4 py-2 rounded-xl bg-violet-600 text-white font-bold text-sm">تحديث الحالة</button>
  </form>
  @endif

  @if($canSeePayment && $lead->status === \App\Models\SalesLead::STATUS_PAYMENT_PENDING)
  <form method="POST" action="{{ route('employee.crm.leads.confirm-payment', $lead) }}" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6" onsubmit="return confirm('تأكيد استلام المبلغ لهذا العميل؟')">
    @csrf
    <h3 class="font-bold text-emerald-900 mb-2">تأكيد الدفع</h3>
    <p class="text-sm text-emerald-800 mb-3">بعد التأكيد يُفعَّل الكورس وتُحسب العمولات تلقائياً.</p>
    <button class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-sm">تأكيد الدفع</button>
  </form>
  @endif

  @if($canAddNotes)
  <form method="POST" action="{{ route('employee.crm.leads.note', $lead) }}" class="rounded-2xl border bg-white p-6 space-y-3">
    @csrf
    <h3 class="font-bold">إضافة ملاحظة</h3>
    <textarea name="note" rows="2" class="w-full rounded-lg border px-3 py-2" placeholder="دوّن ما دار في المكالمة أو الرسالة" required></textarea>
    <button class="px-4 py-2 rounded-xl bg-slate-700 text-white font-bold text-sm">إضافة ملاحظة</button>
  </form>
  @endif

  @if($lead->auditLogs->isNotEmpty())
  <div class="rounded-2xl border bg-white p-6">
    <h3 class="font-bold mb-3">سجل المتابعة</h3>
    <div class="space-y-2 max-h-64 overflow-y-auto text-xs">
      @foreach($lead->auditLogs as $log)
        <div class="border-b pb-2">
          <span class="font-bold">{{ $log->actionLabel() }}</span>
          <span class="text-slate-500"> — {{ $log->user?->name }} — {{ $log->created_at?->format('Y-m-d H:i') }}</span>
        </div>
      @endforeach
    </div>
  </div>
  @endif
</div>
@endsection
