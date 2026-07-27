@extends('layouts.admin')

@section('title', 'مجموعات CRM')
@section('page_title', 'مجموعات الفريق')

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المبيعات · CRM · المجموعات</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">مجموعات الفريق</h2>
            <p class="mt-1 text-sm text-muted">تنظيم فرق التسويق والمبيعات وقادة المجموعات</p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="{{ route('admin.crm.groups.create') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-plus text-xs"></i>
                مجموعة جديدة
            </a>
            <a href="{{ route('admin.crm.dashboard') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-chart-pie text-xs"></i>
                لوحة CRM
            </a>
        </div>
    </section>

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <section class="admin-kpi-grid grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-accent-soft text-accent">
                <i class="fas fa-users text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">عدد المجموعات</p>
            <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink">{{ number_format($groups->count()) }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-accent-soft text-accent">
                <i class="fas fa-user-check text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">نشطة</p>
            <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink">{{ number_format($groups->where('is_active', true)->count()) }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-canvas-muted text-muted">
                <i class="fas fa-address-book text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">إجمالي العملاء المرتبطين</p>
            <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink">{{ number_format($groups->sum('leads_count')) }}</p>
        </article>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">قائمة المجموعات</h3>
        </div>
        <div class="admin-table-wrap overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-line bg-canvas/60 text-xs text-muted">
                    <tr>
                        <th class="px-4 py-3 text-start font-medium">المجموعة</th>
                        <th class="px-4 py-3 text-start font-medium">قائد الفريق</th>
                        <th class="px-4 py-3 text-start font-medium">الأعضاء</th>
                        <th class="px-4 py-3 text-start font-medium">العملاء</th>
                        <th class="px-4 py-3 text-start font-medium">الحالة</th>
                        <th class="px-4 py-3 text-start font-medium">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($groups as $group)
                        <tr class="hover:bg-canvas/40">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-ink">{{ $group->name }}</p>
                            </td>
                            <td class="px-4 py-3 text-ink-soft">{{ $group->teamLeader?->name ?? '—' }}</td>
                            <td class="px-4 py-3 tabular-nums text-ink-soft">{{ $group->members->where('is_active', true)->count() }}</td>
                            <td class="px-4 py-3 tabular-nums text-ink-soft">{{ number_format($group->leads_count) }}</td>
                            <td class="px-4 py-3">
                                @if($group->is_active)
                                    <span class="inline-flex rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent">نشطة</span>
                                @else
                                    <span class="inline-flex rounded-lg bg-canvas-muted px-2.5 py-1 text-xs font-medium text-muted">موقوفة</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.crm.groups.edit', $group) }}" class="btn-press inline-flex h-8 items-center gap-1.5 rounded-lg border border-line px-3 text-xs font-medium text-ink hover:border-accent/30 hover:text-accent">
                                    <i class="fas fa-cog"></i>
                                    إدارة
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center">
                                <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-accent-soft text-accent">
                                    <i class="fas fa-users"></i>
                                </div>
                                <p class="text-sm font-medium text-ink">لا توجد مجموعات بعد</p>
                                <a href="{{ route('admin.crm.groups.create') }}" class="btn-press mt-4 inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                                    <i class="fas fa-plus text-xs"></i>
                                    إنشاء مجموعة
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>
</div>
@endsection
