@extends('layouts.admin')

@section('title', 'تعديل مجموعة الفريق')
@section('header', 'تعديل مجموعة الفريق')

@section('content')
<div class="w-full space-y-6">
    @include('partials.crm-admin-nav')

    {{-- هيدر الصفحة (عرض الصفحة كاملاً) --}}
    <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 dark:text-slate-400 mb-2">
            <a href="{{ route('admin.crm.dashboard') }}" class="hover:text-indigo-600 transition-colors">Glottical CRM</a>
            <span class="mx-2">/</span>
            <a href="{{ route('admin.crm.groups.index') }}" class="hover:text-indigo-600 transition-colors">مجموعات الفريق</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ $group->name }}</span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-300 flex items-center justify-center shrink-0">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $group->name }}</h1>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5">
                        قائد الفريق: <strong>{{ $group->teamLeader?->name ?? '—' }}</strong>
                        — {{ $group->members->where('is_active', true)->count() }} عضو
                        — {{ $group->leads_count }} عميل محتمل
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.crm.groups.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-semibold transition-colors shrink-0">
                <i class="fas fa-arrow-right"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm dark:bg-emerald-950/40 dark:border-emerald-800 dark:text-emerald-100">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- بيانات المجموعة --}}
        <div class="xl:col-span-1">
            <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <form method="POST" action="{{ route('admin.crm.groups.update', $group) }}">
                    @csrf
                    @method('PUT')
                    <div class="p-6 sm:p-8 space-y-6">
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 border-b border-slate-200 dark:border-slate-700 pb-2">بيانات المجموعة</h2>

                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">اسم المجموعة <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $group->name) }}" required
                                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-800 dark:text-slate-100">
                            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="team_leader_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">قائد الفريق</label>
                            <select name="team_leader_id" id="team_leader_id"
                                    class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-800 dark:text-slate-100">
                                <option value="">— اختياري —</option>
                                @foreach($leaders as $l)
                                    <option value="{{ $l->id }}" @selected((string) old('team_leader_id', $group->team_leader_id) === (string) $l->id)>{{ $l->name }}</option>
                                @endforeach
                            </select>
                            @error('team_leader_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700 dark:text-slate-300">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $group->is_active)) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            المجموعة نشطة
                        </label>

                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-colors">
                            <i class="fas fa-save"></i>
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- أعضاء الفريق --}}
        <div class="xl:col-span-2 space-y-6">
            <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 space-y-6">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 border-b border-slate-200 dark:border-slate-700 pb-2">إضافة عضو للفريق</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">أضف موظفي التسويق والمبيعات. عند تعيين عميل محتمل يمكن ربطه بهذه المجموعة لاحتساب عمولة قائد الفريق.</p>
                    </div>

                    <form method="POST" action="{{ route('admin.crm.groups.members.store', $group) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @csrf
                        <div class="sm:col-span-1">
                            <label for="user_id" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">الموظف</label>
                            <select name="user_id" id="user_id" required
                                    class="w-full px-3 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100">
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
                        <div class="sm:col-span-1">
                            <label for="role" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">الدور في الفريق</label>
                            <select name="role" id="role"
                                    class="w-full px-3 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100">
                                <option value="marketing">تسويق</option>
                                <option value="sales">مبيعات</option>
                            </select>
                        </div>
                        <div class="sm:col-span-1 flex items-end">
                            <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold transition-colors">
                                <i class="fas fa-user-plus ml-1"></i> إضافة
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">أعضاء المجموعة</h2>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($group->members->where('is_active', true) as $m)
                        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold shrink-0">
                                    {{ mb_substr($m->user?->name ?? '?', 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-800 dark:text-slate-100 truncate">{{ $m->user?->name ?? '—' }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $m->role === 'marketing' ? 'تسويق' : ($m->role === 'sales' ? 'مبيعات' : $m->role) }}
                                    </p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.crm.groups.members.destroy', [$group, $m]) }}" onsubmit="return confirm('إزالة هذا العضو من المجموعة؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-700 text-xs font-bold px-3 py-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors">
                                    إزالة
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 text-sm">
                            لا يوجد أعضاء بعد — أضف موظفي التسويق والمبيعات من النموذج أعلاه.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
