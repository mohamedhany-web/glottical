@extends('layouts.admin')

@section('title', __('admin.courses_management'))
@section('header', __('admin.courses_management'))

@section('content')
<div class="w-full max-w-full px-4 py-6 space-y-6">
    {{-- هيدر الصفحة — يتوافق مع التصميم الحالي --}}
    <div class="section-card">
        <div class="section-card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-slate-500 dark:text-slate-400 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-sky-600 dark:hover:text-sky-400">{{ __('admin.dashboard') }}</a>
                    <span class="mx-2">/</span>
                    <span class="text-slate-700 dark:text-slate-300">{{ __('admin.courses_management') }}</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">{{ __('admin.courses_management') }}</h1>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                    إدارة وتنظيم الكورسات التدريبية في المنصة
                </p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('admin.advanced-courses.create') }}"
                   class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors shadow-sm">
                    <i class="fas fa-plus"></i>
                    إضافة كورس جديد
                </a>
            </div>
        </div>
    </div>

    {{-- الفلاتر — section-card --}}
    <div class="section-card">
        <div class="section-card-header">
            <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">البحث والتصفية</h2>
        </div>
        <div class="p-6">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">البحث</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           placeholder="البحث في عناوين الكورسات..."
                           class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors">
                </div>
                <div>
                    <label for="course_category_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">مسار الكورس</label>
                    <select name="course_category_id" id="course_category_id" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors">
                        <option value="">جميع المسارات</option>
                        @foreach($courseCategoryOptions as $cc)
                            <option value="{{ $cc->id }}" {{ (string) request('course_category_id') === (string) $cc->id ? 'selected' : '' }}>{{ $cc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="delivery_type" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">نوع التعلّم</label>
                    <select name="delivery_type" id="delivery_type" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors">
                        <option value="">الكل</option>
                        <option value="group" {{ request('delivery_type') == 'group' ? 'selected' : '' }}>جماعي</option>
                        <option value="one_to_one" {{ request('delivery_type') == 'one_to_one' ? 'selected' : '' }}>فردي 1:1</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">الحالة</label>
                    <select name="status" id="status" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors">
                        <option value="">جميع الحالات</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>معطل</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- قائمة الكورسات — بطاقات بتصميم section-card و stat-card --}}
    @if($courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($courses as $course)
                <div class="section-card flex flex-col overflow-hidden">
                    <div class="section-card-header flex items-start justify-between gap-3">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 truncate flex-1 min-w-0">{{ $course->title }}</h3>
                        <div class="flex flex-col items-end gap-1 flex-shrink-0">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $course->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-600 dark:text-slate-300' }}">
                                {{ $course->is_active ? 'نشط' : 'معطل' }}
                            </span>
                            @php $isSolo = ($course->delivery_type ?? 'group') === 'one_to_one'; @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $isSolo ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200' : 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-200' }}">
                                {{ $isSolo ? 'فردي 1:1' : 'جماعي' }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 flex-1">
                        @if($course->description)
                            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4 line-clamp-2">{{ Str::limit($course->description, 120) }}</p>
                        @endif

                        <div class="space-y-2">
                            <div class="flex items-center text-sm text-slate-600 dark:text-slate-400">
                                <i class="fas fa-chalkboard-teacher text-slate-400 dark:text-slate-500 w-5 ml-2 flex-shrink-0"></i>
                                <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $course->instructor?->name ?? '—' }}</span>
                            </div>
                            @if($course->category)
                                <div class="flex items-center text-sm text-slate-600 dark:text-slate-400">
                                    <i class="fas fa-tag text-slate-400 dark:text-slate-500 w-5 ml-2 flex-shrink-0"></i>
                                    <span class="text-slate-700 dark:text-slate-300">{{ $course->category }}</span>
                                </div>
                            @endif
                            <div class="flex items-center text-sm text-slate-600 dark:text-slate-400">
                                @if(!$course->is_free && $course->effectivePurchasePrice() > 0)
                                    <i class="fas fa-money-bill-wave text-slate-400 dark:text-slate-500 w-5 ml-2 flex-shrink-0"></i>
                                    <span class="text-slate-700 dark:text-slate-300 font-medium flex flex-col tabular-nums">
                                        @if($course->hasPromotionalPrice())
                                            <span class="text-xs text-slate-400 line-through">{{ number_format($course->listPriceAmount()) }} ج.م</span>
                                        @endif
                                        <span>{{ number_format($course->effectivePurchasePrice()) }} ج.م</span>
                                    </span>
                                @else
                                    <i class="fas fa-gift text-emerald-500 w-5 ml-2 flex-shrink-0"></i>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold">مجاني</span>
                                @endif
                            </div>
                            <div class="flex items-center text-sm text-slate-500 dark:text-slate-500">
                                <i class="fas fa-clock text-slate-400 w-5 ml-2 flex-shrink-0"></i>
                                <span>{{ $course->created_at->format('Y-m-d') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-700/30 border-t border-slate-100 dark:border-slate-600">
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="p-2 bg-white dark:bg-slate-700 rounded-xl border border-slate-100 dark:border-slate-600">
                                <div class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ $course->lessons_count ?? 0 }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">درس</div>
                            </div>
                            <div class="p-2 bg-white dark:bg-slate-700 rounded-xl border border-slate-100 dark:border-slate-600">
                                <div class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ $course->enrollments_count ?? 0 }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">معلم</div>
                            </div>
                            <div class="p-2 bg-white dark:bg-slate-700 rounded-xl border border-slate-100 dark:border-slate-600">
                                <div class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ $course->orders_count ?? 0 }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">طلب</div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-600 space-y-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('admin.advanced-courses.show', $course) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-sky-50 dark:bg-sky-900/30 hover:bg-sky-100 dark:hover:bg-sky-900/50 text-sky-700 dark:text-sky-300 text-xs font-semibold rounded-xl transition-colors">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                            <a href="{{ route('admin.courses.lessons.index', $course) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-50 dark:bg-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 text-xs font-semibold rounded-xl transition-colors">
                                <i class="fas fa-play-circle"></i> الدروس
                            </a>
                            <a href="{{ route('admin.courses.lessons.create', $course) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-teal-50 dark:bg-teal-900/30 hover:bg-teal-100 dark:hover:bg-teal-900/50 text-teal-700 dark:text-teal-300 text-xs font-semibold rounded-xl transition-colors">
                                <i class="fas fa-plus"></i> درس
                            </a>
                            <a href="{{ route('admin.advanced-courses.orders', $course) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-50 dark:bg-amber-900/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 text-amber-700 dark:text-amber-300 text-xs font-semibold rounded-xl transition-colors">
                                <i class="fas fa-shopping-cart"></i> الطلبات
                            </a>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100 dark:border-slate-600">
                            <button type="button" onclick="toggleCourseStatus({{ $course->id }})" class="inline-flex items-center gap-1.5 px-3 py-2 {{ $course->is_active ? 'bg-red-50 dark:bg-red-900/20 hover:bg-red-100 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800' : 'bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' }} text-xs font-semibold rounded-xl transition-colors">
                                <i class="fas {{ $course->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                {{ $course->is_active ? 'إيقاف' : 'تفعيل' }}
                            </button>
                            <button type="button" onclick="toggleCourseFeatured({{ $course->id }})" class="inline-flex items-center gap-1.5 px-3 py-2 {{ $course->is_featured ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300' : 'bg-slate-100 dark:bg-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-500' }} text-xs font-semibold rounded-xl transition-colors">
                                <i class="fas fa-star"></i>
                                {{ $course->is_featured ? 'إلغاء الترشيح' : 'ترشيح' }}
                            </button>
                            <a href="{{ route('admin.advanced-courses.edit', $course) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-100 dark:bg-slate-600 hover:bg-slate-200 dark:hover:bg-slate-500 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-xl transition-colors">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form method="POST" action="{{ route('admin.advanced-courses.destroy', $course) }}" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الكورس؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800 text-xs font-semibold rounded-xl transition-colors">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex justify-center mt-6">
            {{ $courses->appends(request()->query())->links() }}
        </div>
    @else
        <div class="section-card p-12 text-center">
            <div class="w-20 h-20 rounded-2xl bg-sky-100 dark:bg-sky-900/40 text-sky-600 dark:text-sky-400 flex items-center justify-center text-4xl mx-auto mb-4">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">لا توجد كورسات</h3>
            <p class="text-slate-600 dark:text-slate-400 mb-6">لم يتم العثور على أي كورسات تطابق معايير البحث. يمكنك إضافة كورس جديد.</p>
            <a href="{{ route('admin.advanced-courses.create') }}"
               class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                إضافة أول كورس
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
function toggleCourseStatus(courseId) {
    if (confirm('هل تريد تغيير حالة هذا الكورس؟')) {
        fetch(`/admin/advanced-courses/${courseId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) location.reload();
            else alert('حدث خطأ في تغيير حالة الكورس');
        })
        .catch(() => alert('حدث خطأ في تغيير حالة الكورس'));
    }
}

function toggleCourseFeatured(courseId) {
    if (confirm('هل تريد تغيير حالة ترشيح هذا الكورس؟')) {
        fetch(`/admin/advanced-courses/${courseId}/toggle-featured`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) location.reload();
            else alert('حدث خطأ في تغيير حالة الترشيح');
        })
        .catch(() => alert('حدث خطأ في تغيير حالة الترشيح'));
    }
}
</script>
@endpush
@endsection
