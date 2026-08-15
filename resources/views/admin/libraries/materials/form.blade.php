@extends('layouts.admin')

@section('title', $mode === 'create' ? 'رفع ماتريال' : 'تعديل ماتريال')
@section('page_title', $mode === 'create' ? 'رفع ماتريال' : 'تعديل ماتريال')

@section('content')
@php
    $field = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $label = 'mb-1.5 block text-xs font-medium text-muted';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted"><a href="{{ route('admin.libraries.materials.index') }}" class="hover:text-accent">مكتبة الماتريال</a></p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">{{ $mode === 'create' ? 'رفع ملف جديد' : 'تعديل ملف' }}</h2>
        </div>
        <a href="{{ route('admin.libraries.materials.index') }}" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-4 text-sm">رجوع</a>
    </section>

    @if($errors->any())
        <div class="rounded-xl border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" enctype="multipart/form-data" id="library-material-form"
          action="{{ $mode === 'create' ? route('admin.libraries.materials.store') : route('admin.libraries.materials.update', $material) }}"
          class="space-y-5">
        @csrf
        @if($mode === 'edit') @method('PUT') @endif
        <input type="hidden" name="upload_token" id="upload_token" value="">

        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="{{ $label }}">المحاضرة (اختياري إن اخترت مجلداً)</label>
                    <select name="lecture_id" class="{{ $field }}">
                        <option value="">بدون محاضرة — فولدر فقط</option>
                        @foreach($lectures as $lecture)
                            <option value="{{ $lecture->id }}" @selected((string) old('lecture_id', $material->lecture_id) === (string) $lecture->id)>
                                #{{ $lecture->id }} — {{ $lecture->title }} @if($lecture->course) ({{ $lecture->course->title }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">مجلد الماتريال (معلم×سنة)</label>
                    <select name="library_folder_id" class="{{ $field }}">
                        <option value="">بدون مجلد</option>
                        @foreach(($folders ?? []) as $folder)
                            <option value="{{ $folder->id }}" @selected((string) old('library_folder_id', $material->library_folder_id) === (string) $folder->id)>
                                {{ $folder->name_ar }}@if($folder->name_en) / {{ $folder->name_en }}@endif
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-muted">يجب اختيار محاضرة أو مجلد على الأقل.</p>
                </div>
            </div>
            <div>
                <label class="{{ $label }}">عنوان العرض</label>
                <input type="text" name="title" value="{{ old('title', $material->title) }}" class="{{ $field }}" placeholder="اختياري — افتراضياً اسم الملف">
            </div>
            <div>
                <label class="{{ $label }}">وصف مختصر للطالب</label>
                <textarea name="description" rows="2" class="{{ $field }} py-3" placeholder="مثال: قصة تفاعلية آمنة داخل المنصة">{{ old('description', $material->description) }}</textarea>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="{{ $label }}">تصنيف المحتوى</label>
                    <select name="content_theme" class="{{ $field }}">
                        @foreach(($themes ?? \App\Support\FamilyLibraryThemes::labels('ar')) as $key => $labelTheme)
                            <option value="{{ $key }}" @selected(old('content_theme', $material->content_theme ?: 'general') === $key)>{{ $labelTheme }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-muted">كتب PDF · بوربوينت · HTML · ألعاب تعليمية — ليبقى الطفل داخل المنصة.</p>
                </div>
                <div>
                    <label class="{{ $label }}">تجربة الطالب</label>
                    <select name="experience_mode" class="{{ $field }}">
                        <option value="download" @selected(old('experience_mode', $material->experience_mode ?: 'download') === 'download')>تحميل / فتح ملف</option>
                        <option value="view" @selected(old('experience_mode', $material->experience_mode) === 'view')>عرض داخل المنصة (HTML)</option>
                        <option value="play" @selected(old('experience_mode', $material->experience_mode) === 'play')>لعب داخل المنصة (لعبة)</option>
                    </select>
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="{{ $label }}">ترتيب</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $material->sort_order ?? 0) }}" class="{{ $field }}">
                </div>
                <div class="flex items-end pb-2">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_visible_to_student" value="1" @checked(old('is_visible_to_student', $material->is_visible_to_student ?? true))>
                        ظاهر للطلاب في المكتبة
                    </label>
                </div>
            </div>
            <div>
                <label class="{{ $label }}">الملف {{ $mode === 'create' ? '*' : '(اتركه فارغاً للإبقاء على الحالي)' }}</label>
                @if(!empty($canDirectUpload))
                    <input type="file" id="material_file" class="block w-full text-sm"
                           accept="{{ \App\Support\FamilyLibraryThemes::materialAcceptAttr() }}">
                    <div id="upload-progress-wrap" class="mt-3 hidden">
                        <div class="mb-1 flex items-center justify-between text-xs text-muted">
                            <span id="upload-status">جاري الرفع…</span>
                            <span id="upload-percent">0%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-line">
                            <div id="upload-bar" class="h-full w-0 bg-accent transition-all duration-150"></div>
                        </div>
                    </div>
                    <div id="upload-result" class="mt-2 hidden text-xs"></div>
                    <p class="mt-1 text-xs text-muted">
                        الرفع مباشر من المتصفح إلى Cloudflare R2 — يظهر شريط التقدم ولن يتجمّد حفظ الصفحة.
                        الحد 50 ميجابايت. PDF وPPT وHTML وZIP والصور والصوت.
                    </p>
                    <details class="mt-3">
                        <summary class="cursor-pointer text-xs text-muted">إذا فشل الرفع المباشر: ارفع عبر الخادم</summary>
                        <input type="file" name="file" id="material_file_server" class="mt-2 block w-full text-sm"
                               accept="{{ \App\Support\FamilyLibraryThemes::materialAcceptAttr() }}">
                        <p class="mt-1 text-xs text-muted">أبطأ وقد يتوقف على الملفات الكبيرة بسبب حدود PHP.</p>
                    </details>
                @else
                    <input type="file" name="file" id="material_file" @if($mode === 'create') required @endif class="block w-full text-sm"
                           accept="{{ \App\Support\FamilyLibraryThemes::materialAcceptAttr() }}">
                    <p class="mt-1 text-xs text-muted">
                        يشمل PDF وPPT وHTML والألعاب (ZIP) والصور والصوت.
                        التخزين:
                        <strong>{{ ($storageDisk ?? 'r2') === 'r2' ? 'Cloudflare R2' : ($storageDisk ?? 'local') }}</strong>
                    </p>
                @endif
                @if($mode === 'edit' && $material->file_name)
                    <p class="mt-1 text-xs text-muted">الحالي: {{ $material->file_name }}@if($material->storage_disk) ({{ $material->storage_disk }})@endif</p>
                @endif
            </div>
        </article>

        <button type="submit" id="save-btn" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-semibold text-white">
            <i class="fas fa-save text-xs"></i> حفظ
        </button>
    </form>
</div>
@endsection

@if(!empty($canDirectUpload))
@push('scripts')
<script>
(function () {
    var fileInput = document.getElementById('material_file');
    var tokenInput = document.getElementById('upload_token');
    var progressWrap = document.getElementById('upload-progress-wrap');
    var progressBar = document.getElementById('upload-bar');
    var progressPct = document.getElementById('upload-percent');
    var progressStatus = document.getElementById('upload-status');
    var uploadResult = document.getElementById('upload-result');
    var saveBtn = document.getElementById('save-btn');
    var form = document.getElementById('library-material-form');
    var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        || document.querySelector('input[name="_token"]')?.value;
    var isCreate = @json($mode === 'create');
    var maxBytes = {{ (int) \App\Services\LectureMaterialStorage::maxBytes() }};

    function setProgress(pct, status) {
        progressWrap.classList.remove('hidden');
        progressBar.style.width = pct + '%';
        progressPct.textContent = Math.round(pct) + '%';
        if (status) progressStatus.textContent = status;
    }

    function showResult(ok, text) {
        uploadResult.textContent = text;
        uploadResult.classList.remove('hidden', 'text-success', 'text-danger');
        uploadResult.classList.add(ok ? 'text-success' : 'text-danger');
    }

    function putFile(url, file, contentType, extraHeaders, onPercent) {
        return new Promise(function (resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open('PUT', url, true);
            xhr.timeout = 8 * 60 * 1000;
            xhr.setRequestHeader('Content-Type', contentType);
            if (extraHeaders) {
                Object.keys(extraHeaders).forEach(function (k) {
                    if (String(k).toLowerCase() === 'content-type') return;
                    var val = extraHeaders[k];
                    if (Array.isArray(val)) val = val[0];
                    try { xhr.setRequestHeader(k, val); } catch (e) {}
                });
            }
            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable && onPercent) onPercent((e.loaded / e.total) * 100);
            };
            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 300) resolve();
                else reject(new Error('فشل رفع الملف إلى Cloudflare (HTTP ' + xhr.status + '). إن تكرر: راجع CORS على الـ bucket.'));
            };
            xhr.onerror = function () { reject(new Error('خطأ شبكة أثناء الرفع إلى Cloudflare. غالباً CORS على R2 غير مضبوط لنطاق الموقع.')); };
            xhr.ontimeout = function () { reject(new Error('انتهت مهلة الرفع. تحقق من الاتصال ثم أعد المحاولة.')); };
            xhr.send(file);
        });
    }

    if (!fileInput) return;

    fileInput.addEventListener('change', async function () {
        var file = fileInput.files && fileInput.files[0];
        tokenInput.value = '';
        if (!file) return;
        if (file.size > maxBytes) {
            showResult(false, 'الملف أكبر من 50 ميجابايت.');
            fileInput.value = '';
            return;
        }

        saveBtn.disabled = true;
        setProgress(0, 'تجهيز رابط Cloudflare…');
        uploadResult.classList.add('hidden');

        try {
            var presignRes = await fetch(@json(route('admin.libraries.materials.presign')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    content_type: file.type || '',
                    filename: file.name,
                    file_size: file.size
                })
            });
            var presignData = {};
            try { presignData = await presignRes.json(); } catch (e) { presignData = {}; }
            if (!presignRes.ok || !presignData.direct_upload || !presignData.upload_url || !presignData.upload_token) {
                throw new Error(presignData.message || 'تعذر تجهيز الرفع المباشر');
            }

            setProgress(1, 'جاري الرفع إلى Cloudflare…');
            await putFile(
                presignData.upload_url,
                file,
                presignData.content_type || file.type || 'application/octet-stream',
                presignData.headers || {},
                function (pct) { setProgress(pct, 'جاري الرفع إلى Cloudflare…'); }
            );

            setProgress(99, 'تأكيد الملف…');
            var completeRes = await fetch(@json(route('admin.libraries.materials.complete')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ upload_token: presignData.upload_token })
            });
            var completeData = {};
            try { completeData = await completeRes.json(); } catch (e) { completeData = {}; }
            if (!completeRes.ok || !completeData.ok) {
                throw new Error(completeData.message || 'فشل تأكيد الرفع');
            }

            tokenInput.value = presignData.upload_token;
            setProgress(100, 'اكتمل الرفع');
            showResult(true, 'تم الرفع بنجاح. اضغط حفظ لتسجيل الملف في المكتبة.');
        } catch (err) {
            tokenInput.value = '';
            setProgress(0, 'فشل الرفع');
            showResult(false, err.message || String(err));
        } finally {
            saveBtn.disabled = false;
        }
    });

    var serverFile = document.getElementById('material_file_server');
    if (form) {
        form.addEventListener('submit', function (e) {
            var hasServerFile = serverFile && serverFile.files && serverFile.files[0];
            if (isCreate && !tokenInput.value && !hasServerFile) {
                e.preventDefault();
                showResult(false, 'اختر الملف وانتظر اكتمال الرفع إلى Cloudflare ثم اضغط حفظ.');
            }
        });
    }
})();
</script>
@endpush
@endif
