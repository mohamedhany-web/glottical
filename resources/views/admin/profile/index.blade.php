@extends('layouts.admin')

@section('title', 'الملف الشخصي - لوحة الإدارة')
@section('page_title', 'الملف الشخصي')
@section('header', 'الملف الشخصي')

@section('content')
@php
    $roleLabels = [
        'admin' => 'إداري',
        'super_admin' => 'مدير عام',
    ];
    $roleLabel = $roleLabels[$user->role] ?? 'إداري';
    $memberSince = $user->created_at ? $user->created_at->copy()->locale('ar')->translatedFormat('d F Y') : '—';
    $lastLogin = $user->last_login_at ? $user->last_login_at->copy()->locale('ar')->diffForHumans() : '—';
    $inputClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-2 block text-sm font-medium text-ink';
@endphp

<div class="space-y-5">
    @if(session('recovery_codes'))
        <div class="rounded-2xl border border-amber-200/80 bg-[#fbf7ef] p-5 shadow-soft">
            <h3 class="mb-1 flex items-center gap-2 text-sm font-semibold text-ink">
                <span class="inline-flex size-9 items-center justify-center rounded-xl bg-metal/15 text-metal"><i class="fas fa-key text-sm"></i></span>
                رموز الاسترداد — احفظها في مكان آمن
            </h3>
            <p class="mb-4 text-sm leading-7 text-muted">استخدم أحد هذه الرموز للدخول إذا لم يكن معك جهاز المصادقة. كل رمز يُستخدم مرة واحدة فقط.</p>
            <div class="grid grid-cols-2 gap-2 font-mono text-sm sm:grid-cols-4">
                @foreach(session('recovery_codes') as $code)
                    <span class="rounded-xl border border-line bg-surface px-3 py-2 text-ink">{{ $code }}</span>
                @endforeach
            </div>
            @php session()->forget('recovery_codes'); @endphp
        </div>
    @endif

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-success/20 bg-[#eef8f2] px-4 py-3 text-sm font-medium text-success shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-success text-white"><i class="fas fa-check text-sm"></i></span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Profile summary --}}
    <section class="overflow-hidden rounded-2xl border border-line bg-ink text-white shadow-soft">
        <div class="relative px-5 py-6 sm:px-6 sm:py-8">
            <div class="pointer-events-none absolute inset-0 opacity-50" style="background: radial-gradient(ellipse at 15% 0%, rgba(15,92,87,0.45), transparent 50%), radial-gradient(ellipse at 95% 100%, rgba(176,141,87,0.2), transparent 40%);"></div>
            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-col items-center gap-4 sm:flex-row sm:items-center">
                    <div class="flex size-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-accent text-3xl font-semibold text-white ring-2 ring-white/15 sm:size-28 sm:text-4xl">
                        @if($user->profile_image)
                            <img src="{{ $user->profile_image_url }}" alt="" class="size-full object-cover" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                            <span class="hidden">{{ mb_substr($user->name, 0, 1) }}</span>
                        @else
                            <span>{{ mb_substr($user->name, 0, 1) }}</span>
                        @endif
                    </div>
                    <div class="min-w-0 text-center sm:text-start">
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-white/10 px-2.5 py-1 text-xs font-medium text-metal">
                            <i class="fas fa-user-shield text-[10px]"></i>
                            {{ $roleLabel }}
                        </span>
                        <h2 class="mt-2 text-2xl font-semibold tracking-tight">{{ $user->name }}</h2>
                        <p class="mt-1 text-sm text-white/65">إدارة بياناتك وإعدادات حسابك الشخصي</p>
                        <div class="mt-3 flex flex-wrap justify-center gap-2 sm:justify-start">
                            @if($user->phone)
                                <span class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-1.5 text-sm text-white/85">
                                    <i class="fas fa-phone text-metal text-xs"></i>{{ $user->phone }}
                                </span>
                            @endif
                            @if($user->email)
                                <span class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-1.5 text-sm text-white/85">
                                    <i class="fas fa-envelope text-metal text-xs"></i>{{ $user->email }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 lg:max-w-lg">
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-center">
                        <p class="text-[11px] text-white/50">تاريخ الانضمام</p>
                        <p class="mt-1 text-sm font-semibold">{{ $memberSince }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-center">
                        <p class="text-[11px] text-white/50">نوع الحساب</p>
                        <p class="mt-1 text-sm font-semibold">{{ $roleLabel }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-center">
                        <p class="text-[11px] text-white/50">آخر تسجيل دخول</p>
                        <p class="mt-1 text-sm font-semibold">{{ $lastLogin }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="space-y-5">
            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <div class="mb-4 flex items-center gap-3">
                    <span class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent"><i class="fas fa-info-circle text-sm"></i></span>
                    <h3 class="text-base font-semibold text-ink">معلومات الحساب</h3>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-line bg-canvas px-3 py-2.5">
                        <span class="text-muted">رقم العضوية</span>
                        <span class="font-semibold tabular-nums text-ink">#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-line bg-canvas px-3 py-2.5">
                        <span class="text-muted">نوع الحساب</span>
                        <span class="rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-semibold text-accent">{{ $roleLabel }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-line bg-canvas px-3 py-2.5">
                        <span class="text-muted">الحالة</span>
                        <span class="inline-flex items-center gap-2 text-xs font-semibold {{ $user->is_active ? 'text-success' : 'text-danger' }}">
                            <span class="size-2 rounded-full {{ $user->is_active ? 'bg-success' : 'bg-danger' }}"></span>
                            {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                </div>
            </article>

            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <div class="mb-4 flex items-center gap-3">
                    <span class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent"><i class="fas fa-shield-alt text-sm"></i></span>
                    <h3 class="text-base font-semibold text-ink">المصادقة الثنائية</h3>
                </div>
                @if($user->hasTwoFactorEnabled())
                    <p class="mb-4 text-sm leading-7 text-muted">مفعّلة — يتم طلب رمز التحقق عند كل تسجيل دخول.</p>
                    <form action="{{ route('two-factor.disable') }}" method="POST" class="space-y-3" onsubmit="return confirm('هل تريد تعطيل المصادقة الثنائية؟ ستحتاج إدخال كلمة المرور.');">
                        @csrf
                        <input type="password" name="password" required placeholder="كلمة المرور للتأكيد" class="{{ $inputClass }}">
                        @error('password')
                            <p class="text-xs font-medium text-danger">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="inline-flex h-10 w-full items-center justify-center rounded-xl border border-danger/30 bg-[#fdf2f1] text-sm font-medium text-danger transition hover:bg-[#fbe8e6]">
                            تعطيل المصادقة الثنائية
                        </button>
                    </form>
                @else
                    <p class="mb-4 text-sm leading-7 text-muted">تفعيل المصادقة الثنائية يزيد أمان دخولك للمنصة.</p>
                    <a href="{{ route('two-factor.setup') }}" class="btn-press inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent text-sm font-medium text-white transition hover:bg-[#0d4f4a]">
                        <i class="fas fa-mobile-alt"></i>
                        تفعيل المصادقة الثنائية
                    </a>
                @endif
            </article>
        </div>

        <div class="lg:col-span-2">
            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft sm:p-6">
                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-ink">تحديث البيانات الأساسية</h3>
                        <p class="mt-1 text-sm text-muted">راجع معلوماتك وحدّثها في أي وقت</p>
                    </div>
                    <span class="inline-flex w-fit items-center gap-2 rounded-xl border border-line bg-canvas px-3 py-1.5 text-xs font-medium text-muted">
                        <i class="fas fa-lock text-accent"></i>
                        بياناتك مشفرة وآمنة
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-5" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="{{ $labelClass }}" for="name">الاسم الكامل</label>
                            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required class="{{ $inputClass }}">
                            @error('name')
                                <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="{{ $labelClass }}" for="phone">رقم الهاتف</label>
                            <input id="phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}" required class="{{ $inputClass }}">
                            @error('phone')
                                <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="{{ $labelClass }}" for="email">البريد الإلكتروني <span class="font-normal text-muted">(اختياري)</span></label>
                            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" class="{{ $inputClass }}">
                            @error('email')
                                <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">صورة الملف الشخصي</label>
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <div class="flex size-28 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-dashed border-line bg-canvas">
                                @if($user->profile_image)
                                    <img src="{{ $user->profile_image_url }}" alt="" class="size-full object-cover" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                                    <i class="fas fa-camera hidden text-2xl text-muted"></i>
                                @else
                                    <i class="fas fa-camera text-2xl text-muted"></i>
                                @endif
                            </div>
                            <div class="flex-1">
                                <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-dashed border-line bg-canvas px-5 py-3 text-sm font-medium text-ink-soft transition hover:border-accent/40 hover:bg-accent-soft hover:text-accent">
                                    <i class="fas fa-upload"></i>
                                    <span>اختر صورة جديدة (PNG أو JPG)</span>
                                    <input type="file" name="profile_image" accept="image/*" class="hidden">
                                </label>
                                @error('profile_image')
                                    <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 rounded-2xl border border-dashed border-line bg-canvas p-5">
                        <div>
                            <h4 class="text-sm font-semibold text-ink">تغيير كلمة المرور</h4>
                            <p class="mt-1 text-xs text-muted">اترك الحقول فارغة إذا لم ترغب في التغيير</p>
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-muted" for="current_password">كلمة المرور الحالية</label>
                                <input id="current_password" type="password" name="current_password" class="{{ $inputClass }}">
                                @error('current_password')
                                    <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-muted" for="password">كلمة المرور الجديدة</label>
                                <input id="password" type="password" name="password" class="{{ $inputClass }}">
                                @error('password')
                                    <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-muted" for="password_confirmation">تأكيد كلمة المرور</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" class="{{ $inputClass }}">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-line pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <a href="{{ route('admin.dashboard') }}" class="order-2 inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-line bg-surface px-5 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent sm:order-1">
                            <i class="fas fa-arrow-right"></i>
                            رجوع للوحة التحكم
                        </a>
                        <button type="submit" class="btn-press order-1 inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-accent px-6 text-sm font-medium text-white transition hover:bg-[#0d4f4a] sm:order-2">
                            <i class="fas fa-save"></i>
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </article>
        </div>
    </div>
</div>
@endsection
