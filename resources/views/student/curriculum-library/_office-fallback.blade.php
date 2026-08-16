@if(!empty($canUseOfficeViewer) && !empty($embedUrl))
    <div class="st-pres-office">
        <iframe title="عرض الشريحة"
                src="{{ $embedUrl }}"
                class="st-pres-office__frame"
                allowfullscreen></iframe>
    </div>
    <p class="st-pres-hint">
        إذا لم يظهر العرض، تأكد أن الملف متاح عبر رابط <strong>HTTPS</strong> عام (مثل بيئة الإنتاج).
    </p>
@else
    <div class="st-cl-empty">
        <p class="st-pres-empty__title">لا يمكن فتح العرض التفاعلي في البيئة الحالية</p>
        <p>
            قد يكون السبب أن رابط مصدر الملف غير قابل للفتح من عارض Microsoft من هذه البيئة (مثلاً: رابط غير عام أو بروتوكول/دومين غير مدعوم).
        </p>
        @if(!empty($publicUrl))
            <a href="{{ $publicUrl }}" target="_blank" rel="noopener" class="st-pill st-pill--solid" style="margin-top:10px">
                <i class="fas fa-external-link-alt" aria-hidden="true"></i> فتح رابط الملف مباشرة
            </a>
        @endif
    </div>
@endif
