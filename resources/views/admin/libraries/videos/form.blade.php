@extends('layouts.admin')

@section('title', $mode === 'create' ? 'إضافة فيديو للمكتبة' : 'تعديل فيديو')
@section('page_title', $mode === 'create' ? 'إضافة فيديو للمكتبة' : 'تعديل فيديو')

@section('content')
@php
    $field = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $label = 'mb-1.5 block text-xs font-medium text-muted';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted"><a href="{{ route('admin.libraries.videos.index') }}" class="hover:text-accent">مكتبة الفيديوهات</a></p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">{{ $mode === 'create' ? 'فيديو جديد' : 'تعديل فيديو' }}</h2>
            <p class="mt-1 text-sm text-muted">رابط خارجي أو رفع مباشر إلى Cloudflare (بدون حد مساحة عبر الخادم).</p>
        </div>
        <a href="{{ route('admin.libraries.videos.index') }}" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-4 text-sm">رجوع</a>
    </section>

    @if($errors->any())
        <div class="rounded-xl border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">{{ $errors->first() }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" id="library-video-form"
          action="{{ $mode === 'create' ? route('admin.libraries.videos.store') : route('admin.libraries.videos.update', $video) }}"
          class="space-y-5">
        @csrf
        @if($mode === 'edit') @method('PUT') @endif

        <input type="hidden" name="file_path" id="file_path" value="{{ old('file_path', $video->file_path) }}">
        <input type="hidden" name="storage_disk" id="storage_disk" value="{{ old('storage_disk', $video->storage_disk ?: ($uploadDisk ?? 'r2')) }}">
        <input type="hidden" name="file_size" id="file_size" value="{{ old('file_size', $video->file_size ?? 0) }}">
        <input type="hidden" name="mime_type" id="mime_type" value="{{ old('mime_type', $video->mime_type) }}">

        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft space-y-4">
            <div>
                <label class="{{ $label }}">عنوان الفيديو *</label>
                <input type="text" name="title" required value="{{ old('title', $video->title) }}" class="{{ $field }}" placeholder="مثال: مقدمة الوحدة الأولى">
            </div>
            <div>
                <label class="{{ $label }}">الوصف</label>
                <textarea name="description" rows="3" class="{{ $field }} py-3" placeholder="اختياري">{{ old('description', $video->description) }}</textarea>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="{{ $label }}">تصنيف المحتوى</label>
                    <select name="content_theme" class="{{ $field }}">
                        @foreach(\App\Support\FamilyLibraryThemes::labels('ar') as $key => $themeLabel)
                            <option value="{{ $key }}" @selected(old('content_theme', $video->content_theme ?: 'general') === $key)>{{ $themeLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">اسم المسلسل / السلسلة</label>
                    <input type="text" name="series_title" value="{{ old('series_title', $video->series_title) }}" class="{{ $field }}" placeholder="مثال: قصص الأنبياء للصغار">
                </div>
                <div>
                    <label class="{{ $label }}">الفئة العمرية</label>
                    <input type="text" name="age_label" value="{{ old('age_label', $video->age_label) }}" class="{{ $field }}" placeholder="مثال: 4–8 سنوات">
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="{{ $label }}">مجلد المكتبة</label>
                    <select name="library_folder_id" class="{{ $field }}">
                        <option value="">بدون مجلد (عام)</option>
                        @foreach(($folders ?? []) as $folder)
                            <option value="{{ $folder->id }}" @selected((string) old('library_folder_id', $video->library_folder_id) === (string) $folder->id)>
                                {{ $folder->name_ar }}@if($folder->name_en) — {{ $folder->name_en }}@endif
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-muted"><a href="{{ route('admin.libraries.folders.index') }}" class="text-accent hover:underline">إدارة المجلدات</a></p>
                </div>
                <div>
                    <label class="{{ $label }}">الترتيب</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $video->sort_order ?? 0) }}" class="{{ $field }}">
                </div>
            </div>

            <div class="rounded-xl border border-dashed border-line bg-canvas-muted/40 p-4 space-y-3">
                <h3 class="text-sm font-semibold text-ink">① رابط خارجي</h3>
                <input type="url" name="external_url" id="external_url" value="{{ old('external_url', $video->external_url) }}" class="{{ $field }}" placeholder="https://youtube.com/… أو Vimeo / Bunny">
                <p class="text-xs text-muted">يُشغَّل داخل المنصة. يمكن تركه فارغاً إذا رفعت ملفاً.</p>
            </div>

            <div class="rounded-xl border border-dashed border-line bg-canvas-muted/40 p-4 space-y-3">
                <h3 class="text-sm font-semibold text-ink">② رفع ملف إلى Cloudflare</h3>
                <p class="text-xs text-muted">الرفع مباشر من المتصفح إلى R2 — شريط تقدم وبدون حد مساحة عبر السيرفر. قرص: <strong>{{ $uploadDisk ?? 'r2' }}</strong></p>
                <input type="file" id="video_file" accept="video/*,.mp4,.webm,.mov,.mkv,.m4v,.avi" class="block w-full text-sm">
                <div id="upload-progress-wrap" class="hidden">
                    <div class="flex items-center justify-between text-xs text-muted mb-1">
                        <span id="upload-status">جاري الرفع…</span>
                        <span id="upload-percent">0%</span>
                    </div>
                    <div class="h-2 rounded-full bg-line overflow-hidden">
                        <div id="upload-bar" class="h-full w-0 bg-accent transition-all duration-150"></div>
                    </div>
                </div>
                <div id="upload-result" class="text-xs text-success hidden"></div>
                @if($mode === 'edit' && $video->file_path)
                    <label class="inline-flex items-center gap-2 text-sm text-muted">
                        <input type="checkbox" name="clear_file" value="1"> حذف الملف الحالي من التخزين
                    </label>
                    <p class="text-xs text-muted">الحالي: {{ $video->file_path }} ({{ $video->file_size_for_humans }})</p>
                @endif
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="{{ $label }}">المدة (ثوانٍ) اختياري</label>
                    <input type="number" name="duration_seconds" min="0" value="{{ old('duration_seconds', $video->duration_seconds ?? 0) }}" class="{{ $field }}">
                </div>
                <div class="flex items-end pb-2">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $video->is_published ?? true))>
                        منشور في مكتبة الطلاب
                    </label>
                </div>
            </div>
        </article>

        <button type="submit" id="save-btn" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-semibold text-white">
            <i class="fas fa-save text-xs"></i> حفظ
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var fileInput = document.getElementById('video_file');
    var progressWrap = document.getElementById('upload-progress-wrap');
    var progressBar = document.getElementById('upload-bar');
    var progressPct = document.getElementById('upload-percent');
    var progressStatus = document.getElementById('upload-status');
    var uploadResult = document.getElementById('upload-result');
    var saveBtn = document.getElementById('save-btn');
    var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        || document.querySelector('input[name="_token"]')?.value;

    function setProgress(pct, status) {
        progressWrap.classList.remove('hidden');
        progressBar.style.width = pct + '%';
        progressPct.textContent = Math.round(pct) + '%';
        if (status) progressStatus.textContent = status;
    }

    function putFile(url, file, contentType, extraHeaders, onPercent) {
        return new Promise(function (resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open('PUT', url, true);
            xhr.setRequestHeader('Content-Type', contentType);
            if (extraHeaders) {
                Object.keys(extraHeaders).forEach(function (k) {
                    if (k.toLowerCase() === 'content-type') return;
                    try { xhr.setRequestHeader(k, extraHeaders[k]); } catch (e) {}
                });
            }
            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable && onPercent) onPercent((e.loaded / e.total) * 100);
            };
            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 300) resolve();
                else reject(new Error('فشل رفع الملف (HTTP ' + xhr.status + ')'));
            };
            xhr.onerror = function () { reject(new Error('خطأ شبكة أثناء الرفع')); };
            xhr.send(file);
        });
    }

    if (!fileInput) return;

    fileInput.addEventListener('change', async function () {
        var file = fileInput.files && fileInput.files[0];
        if (!file) return;

        uploadResult.classList.add('hidden');
        saveBtn.disabled = true;
        setProgress(0, 'تجهيز رابط Cloudflare…');

        try {
            var presignRes = await fetch(@json(route('admin.libraries.videos.presign')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    content_type: file.type || 'video/mp4',
                    filename: file.name
                })
            });
            var presignData = await presignRes.json();
            if (!presignRes.ok || !presignData.direct_upload || !presignData.upload_url) {
                throw new Error(presignData.message || 'تعذر تجهيز الرفع المباشر');
            }

            setProgress(1, 'جاري الرفع إلى Cloudflare…');
            await putFile(
                presignData.upload_url,
                file,
                presignData.content_type || file.type || 'video/mp4',
                presignData.headers || {},
                function (pct) { setProgress(pct, 'جاري الرفع إلى Cloudflare…'); }
            );

            setProgress(99, 'تأكيد الملف…');
            var completeRes = await fetch(@json(route('admin.libraries.videos.complete')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ upload_token: presignData.upload_token })
            });
            var completeData = await completeRes.json();
            if (!completeRes.ok || !completeData.ok) {
                throw new Error(completeData.message || 'فشل تأكيد الرفع');
            }

            document.getElementById('file_path').value = completeData.file_path;
            document.getElementById('storage_disk').value = completeData.storage_disk;
            document.getElementById('file_size').value = completeData.file_size;
            document.getElementById('mime_type').value = completeData.mime_type || '';
            // امسح الرابط الخارجي إن رُفع ملف (اختياري — نتركه لو المستخدم يبغى الاثنين)
            setProgress(100, 'اكتمل الرفع');
            uploadResult.textContent = 'تم الرفع بنجاح (' + (completeData.file_size_human || '') + '). احفظ النموذج.';
            uploadResult.classList.remove('hidden');
            uploadResult.classList.remove('text-danger');
            uploadResult.classList.add('text-success');
        } catch (err) {
            setProgress(0, 'فشل الرفع');
            uploadResult.textContent = err.message || String(err);
            uploadResult.classList.remove('hidden');
            uploadResult.classList.remove('text-success');
            uploadResult.classList.add('text-danger');
            document.getElementById('file_path').value = @json(old('file_path', $video->file_path));
        } finally {
            saveBtn.disabled = false;
        }
    });
})();
</script>
@endpush
