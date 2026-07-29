@extends('layouts.admin')

@section('title', 'تعديل برنامج الإحالات - ' . config('app.name'))
@section('page_title', 'تعديل برنامج الإحالات')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $checkClass = 'size-4 rounded border-line text-accent focus:ring-accent/20';
@endphp

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">التسويق · الإحالات</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">تعديل: {{ $referralProgram->name }}</h2>
            <p class="mt-1 max-w-2xl text-sm text-muted">تحديث قواعد الخصم والمكافآت</p>
        </div>
        <a href="{{ route('admin.referral-programs.index') }}"
           class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
            <i class="fas fa-arrow-right text-xs"></i>
            رجوع للقائمة
        </a>
    </section>

    <form action="{{ route('admin.referral-programs.update', $referralProgram) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <div class="flex items-center gap-3">
                    <span class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                        <i class="fas fa-info-circle text-sm"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-semibold text-ink">البيانات الأساسية</h3>
                        <p class="mt-0.5 text-xs text-muted">اسم البرنامج والوصف</p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-5 p-4 sm:p-5">
                <div>
                    <label for="name" class="{{ $labelClass }}">اسم البرنامج <span class="text-rose-600">*</span></label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $referralProgram->name) }}" class="{{ $fieldClass }}">
                    @error('name')
                        <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="description" class="{{ $labelClass }}">الوصف</label>
                    <textarea name="description" id="description" rows="3" class="{{ $areaClass }}">{{ old('description', $referralProgram->description) }}</textarea>
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <div class="flex items-center gap-3">
                    <span class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                        <i class="fas fa-tag text-sm"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-semibold text-ink">خصم المحال</h3>
                        <p class="mt-0.5 text-xs text-muted">نوع وقيمة الخصم للمستخدم المحال</p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-5 p-4 sm:grid-cols-2 sm:p-5">
                <div>
                    <label for="discount_type" class="{{ $labelClass }}">نوع الخصم للمحال <span class="text-rose-600">*</span></label>
                    <select name="discount_type" id="discount_type" required class="{{ $fieldClass }}">
                        <option value="percentage" {{ old('discount_type', $referralProgram->discount_type) == 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                        <option value="fixed" {{ old('discount_type', $referralProgram->discount_type) == 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                    </select>
                </div>
                <div>
                    <label for="discount_value" class="{{ $labelClass }}">قيمة الخصم <span class="text-rose-600">*</span></label>
                    <input type="number" name="discount_value" id="discount_value" step="0.01" min="0" required value="{{ old('discount_value', $referralProgram->discount_value) }}" class="{{ $fieldClass }}">
                </div>
                <div>
                    <label for="maximum_discount" class="{{ $labelClass }}">الحد الأقصى للخصم</label>
                    <input type="number" name="maximum_discount" id="maximum_discount" step="0.01" min="0" value="{{ old('maximum_discount', $referralProgram->maximum_discount) }}" class="{{ $fieldClass }}">
                </div>
                <div>
                    <label for="minimum_order_amount" class="{{ $labelClass }}">الحد الأدنى لمبلغ الطلب</label>
                    <input type="number" name="minimum_order_amount" id="minimum_order_amount" step="0.01" min="0" value="{{ old('minimum_order_amount', $referralProgram->minimum_order_amount) }}" class="{{ $fieldClass }}">
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <div class="flex items-center gap-3">
                    <span class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                        <i class="fas fa-gift text-sm"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-semibold text-ink">مكافأة المحيل</h3>
                        <p class="mt-0.5 text-xs text-muted">نوع وقيمة مكافأة المُحيل</p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-5 p-4 sm:grid-cols-2 sm:p-5">
                <div>
                    <label for="referrer_reward_type" class="{{ $labelClass }}">نوع المكافأة <span class="text-rose-600">*</span></label>
                    <select name="referrer_reward_type" id="referrer_reward_type" required class="{{ $fieldClass }}">
                        <option value="fixed" {{ old('referrer_reward_type', $referralProgram->referrer_reward_type) == 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                        <option value="percentage" {{ old('referrer_reward_type', $referralProgram->referrer_reward_type) == 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                        <option value="points" {{ old('referrer_reward_type', $referralProgram->referrer_reward_type) == 'points' ? 'selected' : '' }}>نقاط</option>
                    </select>
                </div>
                <div>
                    <label for="referrer_reward_value" class="{{ $labelClass }}">قيمة المكافأة</label>
                    <input type="number" name="referrer_reward_value" id="referrer_reward_value" step="0.01" min="0" value="{{ old('referrer_reward_value', $referralProgram->referrer_reward_value) }}" class="{{ $fieldClass }}">
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <div class="flex items-center gap-3">
                    <span class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                        <i class="fas fa-cog text-sm"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-semibold text-ink">الإعدادات</h3>
                        <p class="mt-0.5 text-xs text-muted">مدة الصلاحية وحدود الاستخدام</p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-5 p-4 sm:grid-cols-2 sm:p-5">
                <div>
                    <label for="discount_valid_days" class="{{ $labelClass }}">مدة صلاحية الخصم (بالأيام) <span class="text-rose-600">*</span></label>
                    <input type="number" name="discount_valid_days" id="discount_valid_days" min="1" required value="{{ old('discount_valid_days', $referralProgram->discount_valid_days) }}" class="{{ $fieldClass }}">
                </div>
                <div>
                    <label for="max_discount_uses_per_referred" class="{{ $labelClass }}">الحد الأقصى لاستخدام الخصم للمحال <span class="text-rose-600">*</span></label>
                    <input type="number" name="max_discount_uses_per_referred" id="max_discount_uses_per_referred" min="1" required value="{{ old('max_discount_uses_per_referred', $referralProgram->max_discount_uses_per_referred) }}" class="{{ $fieldClass }}">
                </div>
                <div>
                    <label for="max_referrals_per_user" class="{{ $labelClass }}">الحد الأقصى للإحالات لكل مستخدم</label>
                    <input type="number" name="max_referrals_per_user" id="max_referrals_per_user" min="1" value="{{ old('max_referrals_per_user', $referralProgram->max_referrals_per_user) }}" class="{{ $fieldClass }}">
                    <p class="mt-1 text-xs text-muted">اتركه فارغاً للسماح بإحالات غير محدودة</p>
                </div>
                <div>
                    <label for="referral_code_valid_days" class="{{ $labelClass }}">صلاحية رابط الإحالة (أيام)</label>
                    <input type="number" name="referral_code_valid_days" id="referral_code_valid_days" min="1" value="{{ old('referral_code_valid_days', $referralProgram->referral_code_valid_days) }}" class="{{ $fieldClass }}">
                    <p class="mt-1 text-xs text-muted">اختياري — للتوثيق؛ التسجيل يعتمد على كود المحيل</p>
                </div>
                <div class="flex items-center gap-3 sm:col-span-2">
                    <input type="checkbox" name="allow_self_referral" id="allow_self_referral" value="1" {{ old('allow_self_referral', $referralProgram->allow_self_referral) ? 'checked' : '' }} class="{{ $checkClass }}">
                    <label for="allow_self_referral" class="text-sm font-medium text-ink">السماح بالإحالة الذاتية</label>
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <div class="flex items-center gap-3">
                    <span class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                        <i class="fas fa-calendar text-sm"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-semibold text-ink">الفترة الزمنية</h3>
                        <p class="mt-0.5 text-xs text-muted">تاريخ البدء والانتهاء</p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-5 p-4 sm:grid-cols-2 sm:p-5">
                <div>
                    <label for="starts_at" class="{{ $labelClass }}">تاريخ البدء</label>
                    <input type="date" name="starts_at" id="starts_at" value="{{ old('starts_at', $referralProgram->starts_at?->format('Y-m-d')) }}" class="{{ $fieldClass }}">
                </div>
                <div>
                    <label for="expires_at" class="{{ $labelClass }}">تاريخ الانتهاء</label>
                    <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at', $referralProgram->expires_at?->format('Y-m-d')) }}" class="{{ $fieldClass }}">
                </div>
            </div>
        </article>

        <article class="rounded-2xl border border-line bg-accent-soft p-4 shadow-soft sm:p-5">
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default', $referralProgram->is_default) ? 'checked' : '' }} class="{{ $checkClass }}">
                <label for="is_default" class="text-sm font-medium text-ink">برنامج <strong>افتراضي</strong> لإحالات التسجيل الجديدة</label>
            </div>
        </article>

        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-4 shadow-soft">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $referralProgram->is_active) ? 'checked' : '' }} class="{{ $checkClass }}">
            <label for="is_active" class="text-sm font-medium text-ink">تفعيل البرنامج</label>
        </div>

        <div class="flex flex-wrap justify-end gap-3">
            <a href="{{ route('admin.referral-programs.index') }}"
               class="btn-press inline-flex h-11 items-center rounded-xl border border-line bg-surface px-6 text-sm font-medium text-ink transition hover:bg-accent-soft hover:text-accent">
                إلغاء
            </a>
            <button type="submit"
                    class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-6 text-sm font-medium text-white hover:bg-[#0d4f4a]">
                <i class="fas fa-save text-xs"></i>
                حفظ التعديلات
            </button>
        </div>
    </form>

    @if(!$referralProgram->is_default)
        <form action="{{ route('admin.referral-programs.set-default', $referralProgram) }}" method="POST" class="rounded-2xl border border-line bg-surface px-4 py-3 shadow-soft" onsubmit="return confirm('تعيين هذا البرنامج كافتراضي؟');">
            @csrf
            <button type="submit" class="text-sm font-semibold text-accent hover:underline disabled:opacity-40" {{ !$referralProgram->is_active || !$referralProgram->isValid() ? 'disabled' : '' }}>تعيين كافتراضي بضغطة واحدة</button>
        </form>
    @endif
</div>
@endsection
