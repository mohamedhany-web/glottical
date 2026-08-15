@if(!empty($canUseOfficeViewer) && !empty($embedUrl))
    <div class="aspect-[1410/900] w-full min-h-[480px] rounded-xl border border-slate-200 bg-white overflow-hidden shadow-inner">
        <iframe title="عرض الشريحة"
                src="{{ $embedUrl }}"
                class="w-full h-full min-h-[480px]"
                allowfullscreen></iframe>
    </div>
    <p class="text-xs text-slate-500 mt-3 leading-relaxed">
        إذا لم يظهر العرض، تأكد أن الملف متاح عبر رابط <strong>HTTPS</strong> عام (مثل بيئة الإنتاج).
    </p>
@else
    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-4 text-amber-900">
        <p class="text-sm font-bold mb-1">لا يمكن فتح العرض التفاعلي في البيئة الحالية</p>
        <p class="text-xs leading-relaxed">
            قد يكون السبب أن رابط مصدر الملف غير قابل للفتح من عارض Microsoft من هذه البيئة (مثلاً: رابط غير عام أو بروتوكول/دومين غير مدعوم).
        </p>
        @if(!empty($publicUrl))
            <a href="{{ $publicUrl }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 mt-3 px-3 py-2 rounded-lg bg-amber-600 text-white text-xs font-semibold hover:bg-amber-700">
                <i class="fas fa-external-link-alt"></i> فتح رابط الملف مباشرة
            </a>
        @endif
    </div>
@endif
