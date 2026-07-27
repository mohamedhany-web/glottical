@extends('layouts.admin')

@section('title', 'تعديل مجموعة · '.$group->name)
@section('page_title', 'تعديل المجموعة')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
@endphp

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المبيعات · CRM · المجموعات</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">{{ $group->name }}</h2>
            <p class="mt-1 text-sm text-muted">
                قائد الفريق: {{ $group->teamLeader?->name ?? '—' }}
                · {{ $group->members->where('is_active', true)->count() }} عضو
                · {{ $group->leads_count }} عميل محتمل
            </p>
        </div>
        <a href="{{ route('admin.crm.groups.index') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
            <i class="fas fa-arrow-right text-xs"></i>
            العودة للقائمة
        </a>
    </section>

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft xl:col-span-1">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">بيانات المجموعة</h3>
            </div>
            <form method="POST" action="{{ route('admin.crm.groups.update', $group) }}" class="space-y-5 p-4 sm:p-5">
                @csrf
                @method('PUT')
                <div>
                    <label class="{{ $labelClass }}" for="name">اسم المجموعة <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $group->name) }}" required class="{{ $fieldClass }}">
                    @error('name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="team_leader_id">قائد الفريق</label>
                    <select name="team_leader_id" id="team_leader_id" class="{{ $fieldClass }}">
                        <option value="">— اختياري —</option>
                        @foreach($leaders as $l)
                            <option value="{{ $l->id }}" @selected((string) old('team_leader_id', $group->team_leader_id) === (string) $l->id)>{{ $l->name }}</option>
                        @endforeach
                    </select>
                    @error('team_leader_id')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-ink">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $group->is_active)) class="rounded border-line text-accent focus:ring-accent/20">
                    المجموعة نشطة
                </label>
                <button type="submit" class="btn-press inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                    <i class="fas fa-save text-xs"></i>
                    حفظ التعديلات
                </button>
            </form>
        </article>

        <div class="space-y-5 xl:col-span-2">
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">إضافة عضو</h3>
                    <p class="mt-0.5 text-xs text-muted">أضف موظفي التسويق والمبيعات لربط العملاء بالمجموعة</p>
                </div>
                <form method="POST" action="{{ route('admin.crm.groups.members.store', $group) }}" class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-3 sm:items-end sm:p-5">
                    @csrf
                    <div>
                        <label class="{{ $labelClass }}" for="user_id">الموظف</label>
                        <select name="user_id" id="user_id" required class="{{ $fieldClass }}">
                            <option value="">اختر موظفاً</option>
                            <optgroup label="تسويق">
                                @foreach($marketingUsers as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="مبيعات">
                                @foreach($salesUsers as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label class="{{ $labelClass }}" for="role">الدور</label>
                        <select name="role" id="role" class="{{ $fieldClass }}">
                            <option value="marketing">تسويق</option>
                            <option value="sales">مبيعات</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-press inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                        <i class="fas fa-user-plus text-xs"></i>
                        إضافة
                    </button>
                </form>
            </article>

            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">أعضاء المجموعة</h3>
                </div>
                <div class="divide-y divide-line">
                    @forelse($group->members->where('is_active', true) as $m)
                        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-4 sm:px-5">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-sm font-semibold text-accent">
                                    {{ mb_substr($m->user?->name ?? '?', 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-ink">{{ $m->user?->name ?? '—' }}</p>
                                    <p class="text-xs text-muted">{{ $m->role === 'marketing' ? 'تسويق' : ($m->role === 'sales' ? 'مبيعات' : $m->role) }}</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.crm.groups.members.destroy', [$group, $m]) }}" onsubmit="return confirm('إزالة هذا العضو من المجموعة؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-press inline-flex h-8 items-center rounded-lg border border-line px-3 text-xs font-medium text-rose-600 hover:border-rose-300 hover:bg-rose-50">
                                    إزالة
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="px-4 py-12 text-center text-sm text-muted sm:px-5">
                            لا يوجد أعضاء بعد — أضف موظفين من النموذج أعلاه.
                        </div>
                    @endforelse
                </div>
            </article>
        </div>
    </div>
</div>
@endsection
