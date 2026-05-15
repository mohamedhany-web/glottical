@extends('layouts.admin')
@section('title', 'تعديل سلايدر')
@section('header', 'تعديل سلايدر الصفحة الرئيسية')
@section('content')
<div class="w-full">
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <h1 class="text-xl font-bold text-slate-900">تعديل السلايدر</h1>
            <p class="text-slate-500 text-sm mt-1">{{ $slide->resolvedTitle() }}</p>
        </div>
        <form action="{{ route('admin.homepage-sliders.update', $slide) }}" method="POST" enctype="multipart/form-data" class="p-5 sm:p-8">
            @csrf
            @method('PUT')
            @include('admin.homepage-sliders._form', ['slide' => $slide])
            <div class="flex flex-wrap gap-3 pt-6 mt-6 border-t border-slate-200">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-semibold">
                    <i class="fas fa-save"></i> حفظ التعديلات
                </button>
                <a href="{{ route('admin.homepage-sliders.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold">رجوع</a>
            </div>
        </form>
    </div>
</div>
@endsection
