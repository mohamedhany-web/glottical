@extends('layouts.app')

@section('title', 'مكتبة الماتريال')
@section('page_title', 'مكتبة الماتريال')

@section('content')
<div class="space-y-4">
    <div>
        <h2 class="text-xl font-black text-[#1A2744]">📚 مكتبة الماتريال</h2>
        <p class="text-sm text-[#6B7A99] mt-1">ملفات ودروس مرتبطة باشتراكاتك.</p>
    </div>
    <div class="overflow-hidden rounded-2xl border border-[#E8EEF8] bg-white">
        <ul class="divide-y divide-[#E8EEF8]">
            @forelse($materials as $material)
                <li class="px-4 py-3 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-bold text-[#1A2744] truncate">{{ $material->title ?: $material->file_name }}</p>
                        <p class="text-xs text-[#6B7A99] truncate">{{ $material->lecture?->title }}</p>
                    </div>
                    @if($material->file_path)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($material->file_path) }}" class="text-sm font-bold text-[#0B3D91] hover:underline" target="_blank">فتح</a>
                    @endif
                </li>
            @empty
                <li class="px-4 py-10 text-center text-sm text-[#6B7A99]">لا توجد مواد ظاهرة حالياً.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
