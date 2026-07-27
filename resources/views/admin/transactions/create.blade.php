@extends('layouts.admin')

@section('title', 'إضافة معاملة جديدة')
@section('page_title', 'إضافة معاملة جديدة')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
@endphp

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المحاسبة · المعاملات</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">إضافة معاملة جديدة</h2>
            <p class="mt-1 text-sm text-muted">تسجيل معاملة مالية جديدة في النظام</p>
        </div>
        <a href="{{ route('admin.transactions.index') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
            <i class="fas fa-arrow-right text-xs"></i>
            رجوع للقائمة
        </a>
    </section>

    <form action="{{ route('admin.transactions.store') }}" method="POST" class="space-y-5">
        @csrf

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">بيانات المعاملة</h3>
                <p class="mt-0.5 text-xs text-muted">العميل، النوع، المبلغ، والحالة</p>
            </div>
            <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5">
                <div class="sm:col-span-2">
                    <label class="{{ $labelClass }}" for="transaction-client-search">العميل <span class="text-danger">*</span></label>
                    <label for="transaction-client-search" class="sr-only">بحث عن عميل بالاسم أو البريد</label>
                    <input type="search" id="transaction-client-search" autocomplete="off" placeholder="بحث بالاسم أو البريد أو الجوال…"
                           class="{{ $fieldClass }} mb-2">
                    <select id="transaction-user-id" name="user_id" required class="{{ $fieldClass }}">
                        <option value="">اختر العميل</option>
                        @foreach($users as $user)
                        @php
                            $searchHaystack = mb_strtolower(
                                trim($user->name.' '.($user->email ?? '').' '.($user->phone ?? '')),
                                'UTF-8'
                            );
                        @endphp
                        <option value="{{ $user->id }}" data-search="{{ e($searchHaystack) }}">{{ $user->name }} — {{ $user->email }} @if($user->phone) · {{ $user->phone }} @endif</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="{{ $labelClass }}" for="type">النوع <span class="text-danger">*</span></label>
                    <select id="type" name="type" required class="{{ $fieldClass }}">
                        <option value="deposit">إيداع</option>
                        <option value="withdrawal">سحب</option>
                        <option value="payment">دفع</option>
                        <option value="refund">استرداد</option>
                        <option value="commission">عمولة</option>
                    </select>
                </div>

                <div>
                    <label class="{{ $labelClass }}" for="amount">المبلغ <span class="text-danger">*</span></label>
                    <input id="amount" type="number" name="amount" step="0.01" min="0" required value="{{ old('amount') }}" class="{{ $fieldClass }}">
                </div>

                <div>
                    <label class="{{ $labelClass }}" for="status">الحالة <span class="text-danger">*</span></label>
                    <select id="status" name="status" required class="{{ $fieldClass }}">
                        <option value="pending">معلقة</option>
                        <option value="completed" selected>مكتملة</option>
                        <option value="failed">فاشلة</option>
                        <option value="cancelled">ملغاة</option>
                    </select>
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">الوصف</h3>
                <p class="mt-0.5 text-xs text-muted">تفاصيل إضافية اختيارية</p>
            </div>
            <div class="p-4 sm:p-5">
                <label class="{{ $labelClass }}" for="description">الوصف</label>
                <textarea id="description" name="description" rows="3" class="{{ $areaClass }}">{{ old('description') }}</textarea>
            </div>
            <div class="flex flex-wrap gap-3 border-t border-line px-4 py-4 sm:px-5">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-6 text-sm font-medium text-white">
                    <i class="fas fa-save text-xs"></i>
                    إنشاء المعاملة
                </button>
                <a href="{{ route('admin.transactions.index') }}" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-6 text-sm font-medium text-ink hover:bg-canvas">
                    إلغاء
                </a>
            </div>
        </article>
    </form>
</div>

@push('scripts')
<script>
(function () {
    var searchInput = document.getElementById('transaction-client-search');
    var select = document.getElementById('transaction-user-id');
    if (!searchInput || !select) return;
    var options = Array.prototype.slice.call(select.querySelectorAll('option'));
    function applyFilter() {
        var q = (searchInput.value || '').trim().toLowerCase();
        options.forEach(function (opt) {
            if (!opt.value) {
                opt.hidden = false;
                return;
            }
            if (opt.selected) {
                opt.hidden = false;
                return;
            }
            var hay = (opt.getAttribute('data-search') || '').toLowerCase();
            opt.hidden = q.length > 0 && hay.indexOf(q) === -1;
        });
    }
    searchInput.addEventListener('input', applyFilter);
    searchInput.addEventListener('search', applyFilter);
    select.addEventListener('change', applyFilter);
})();
</script>
@endpush
@endsection
