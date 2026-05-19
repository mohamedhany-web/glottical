@extends('layouts.app')

@section('title', $course->title . ' - ' . __('student.my_courses'))
@section('header', $course->title)


@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <!-- العودة -->
        <div class="mb-4">
            <a href="{{ route('my-courses.index') }}" class="inline-flex items-center text-sky-600 hover:text-sky-700 text-sm font-medium">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة إلى كورساتي
            </a>
        </div>

        <!-- معلومات الكورس - عرض كامل -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6 overflow-hidden">
            <div class="flex flex-col lg:flex-row">
                <!-- صورة الكورس -->
                <div class="lg:w-2/5 h-52 lg:h-72 bg-sky-100 flex items-center justify-center relative overflow-hidden flex-shrink-0">
                    @if($course->thumbnail)
                        <img src="{{ storage_asset($course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="text-sky-600 text-center">
                            <i class="fas fa-graduation-cap text-4xl"></i>
                            <p class="text-sm font-medium mt-2 text-sky-700">{{ $course->academicSubject->name ?? 'كورس' }}</p>
                        </div>
                    @endif
                    <div class="absolute top-3 left-3 bg-white rounded-lg px-3 py-1.5 shadow-sm border border-gray-100">
                        <span class="text-sm font-bold text-sky-600">{{ $progress }}%</span>
                    </div>
                </div>

                <!-- تفاصيل الكورس -->
                <div class="lg:flex-1 p-5 sm:p-6 lg:p-8">
                    <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                        <div class="min-w-0 flex-1">
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2 leading-tight">{{ $course->title }}</h1>
                            <p class="text-sm text-gray-500">
                                {{ $course->academicYear->name ?? '—' }} · {{ $course->academicSubject->name ?? '—' }} · {{ $course->teacher->name ?? '—' }}
                            </p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-500 text-white">
                            <i class="fas fa-check-circle"></i> مفعل
                        </span>
                    </div>

                    @if($course->description)
                        <p class="text-sm text-gray-600 mb-4 leading-relaxed line-clamp-2">{{ Str::limit($course->description, 180) }}</p>
                    @endif

                    <!-- التقدم والإحصائيات -->
                    <div class="flex flex-wrap items-center gap-4 sm:gap-6 mb-5">
                        <div class="flex-1 min-w-[200px]">
                            <div class="flex items-center justify-between text-sm mb-1.5">
                                <span class="font-medium text-gray-600">التقدم</span>
                                <span class="font-bold text-sky-600">{{ $progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div class="h-full bg-sky-500 rounded-full transition-all duration-500" style="width: {{ min($progress, 100) }}%;"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $progress }}% مكتمل</p>
                        </div>
                        <div class="flex gap-4">
                            <div class="text-center px-4 py-2 bg-amber-50 rounded-lg border border-amber-100">
                                <span class="text-lg font-bold text-amber-600 block"><i class="fas fa-star text-amber-500 ml-1"></i>{{ number_format((float)($coursePoints ?? 0), 0) }}</span>
                                <span class="text-xs text-gray-600">نقاط</span>
                            </div>
                            <div class="text-center px-4 py-2 bg-emerald-50 rounded-lg border border-emerald-100">
                                <span class="text-lg font-bold text-emerald-600 block">{{ $completedLessons }}</span>
                                <span class="text-xs text-gray-600">مكتمل</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('my-courses.learn', $course) }}" 
                       class="inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-6 py-3 rounded-lg font-semibold text-sm transition-colors">
                        <i class="fas fa-play"></i>
                        ابدأ التعلم
                    </a>
                </div>
            </div>
        </div>

        <!-- نظرة عامة -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6 overflow-hidden">
            <div class="px-4 py-4 border-b border-gray-200 bg-sky-50/50">
                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-info-circle text-sky-600"></i>
                    نظرة عامة
                </h2>
            </div>
            <div class="px-3 py-5 sm:px-6 sm:py-6 lg:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                        <h3 class="text-base font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-align-right text-sky-500"></i>
                            وصف الكورس
                        </h3>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $course->description ?? 'لا يوجد وصف متاح' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                        <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-list-ul text-sky-500"></i>
                            معلومات الكورس
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between py-2.5 px-3 bg-white rounded-lg border border-gray-100">
                                <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-layer-group text-sky-500 w-4"></i> المستوى</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $course->level ?? '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2.5 px-3 bg-white rounded-lg border border-gray-100">
                                <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-clock text-sky-500 w-4"></i> المدة</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $course->duration_hours }} ساعة</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
