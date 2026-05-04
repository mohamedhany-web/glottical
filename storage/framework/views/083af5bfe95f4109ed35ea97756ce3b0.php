<?php $__env->startSection('title', $assignment->title); ?>
<?php $__env->startSection('header', $assignment->title); ?>

<?php $__env->startSection('content'); ?>
<?php
    $courseTitle = $assignment->course->title ?? 'كورس';
    $instrFiles = is_array($assignment->resource_attachments) ? $assignment->resource_attachments : [];
?>
<div class="w-full max-w-full space-y-5 sm:space-y-6">
    
    <div class="ins-stat-card bg-white dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700/80 rounded-xl shadow-sm overflow-hidden">
        <div class="px-4 py-4 sm:px-6 sm:py-5 bg-gradient-to-l from-[#f2f4ff] via-white to-white dark:from-[#1e2b5a] dark:via-[#1e293b] dark:to-[#1e293b] border-b border-gray-200/80 dark:border-gray-700/80">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="min-w-0 flex-1">
                    <nav class="flex flex-wrap items-center gap-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-3">
                        <a href="<?php echo e(route('student.assignments.index')); ?>" class="inline-flex items-center gap-1.5 font-semibold text-[#283593] dark:text-indigo-300 hover:text-[#FB5607] dark:hover:text-orange-300 transition-colors">
                            <i class="fas fa-arrow-right text-[10px] sm:text-xs"></i>
                            واجباتي
                        </a>
                        <span class="text-gray-300 dark:text-gray-600" aria-hidden="true">/</span>
                        <span class="truncate text-gray-600 dark:text-gray-300"><?php echo e($courseTitle); ?></span>
                    </nav>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-gray-100 font-heading leading-tight"><?php echo e($assignment->title); ?></h1>
                    <?php if($assignment->lesson): ?>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-book-open ml-1 text-[#283593] dark:text-indigo-400"></i>
                            <?php echo e($assignment->lesson->title ?? ''); ?>

                        </p>
                    <?php endif; ?>
                </div>
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <?php if($assignment->due_date): ?>
                        <div class="inline-flex items-center gap-2 rounded-xl border border-amber-200 dark:border-amber-800/60 bg-amber-50 dark:bg-amber-900/20 px-3 py-2 text-xs sm:text-sm">
                            <i class="fas fa-calendar-alt text-amber-600 dark:text-amber-400"></i>
                            <div>
                                <span class="block font-bold text-amber-900 dark:text-amber-200">آخر موعد</span>
                                <span class="text-amber-800 dark:text-amber-300/90"><?php echo e($assignment->due_date->timezone(config('app.timezone'))->format('Y-m-d H:i')); ?></span>
                                <?php if($assignment->allow_late_submission): ?>
                                    <span class="block text-[11px] text-emerald-700 dark:text-emerald-400 mt-0.5">يُقبل التسليم المتأخر</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 px-3 py-2 text-xs sm:text-sm">
                        <i class="fas fa-star text-[#FB5607]"></i>
                        <span class="text-gray-700 dark:text-gray-300">الدرجة العظمى: <strong class="text-gray-900 dark:text-white"><?php echo e($assignment->max_score); ?></strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 sm:gap-6 w-full">
        
        <div class="xl:col-span-7 2xl:col-span-8 space-y-5 sm:space-y-6 min-w-0">
            <div class="bg-white dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700/80 rounded-xl shadow-sm overflow-hidden">
                <div class="px-4 py-3 sm:px-6 border-b border-gray-200 dark:border-gray-700 bg-sky-50/60 dark:bg-sky-950/20">
                    <h2 class="text-base font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-[#eef2ff] dark:bg-indigo-900/50 text-[#283593] dark:text-indigo-300 flex items-center justify-center text-sm">
                            <i class="fas fa-file-alt"></i>
                        </span>
                        وصف الواجب
                    </h2>
                </div>
                <div class="p-4 sm:p-6 space-y-4">
                    <?php if($assignment->description): ?>
                        <div class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed"><?php echo nl2br(e($assignment->description)); ?></div>
                    <?php else: ?>
                        <p class="text-sm text-gray-500 dark:text-gray-400">لا يوجد وصف إضافي.</p>
                    <?php endif; ?>
                    <?php if($assignment->instructions): ?>
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 p-4 sm:p-5">
                            <p class="text-xs font-bold text-slate-600 dark:text-slate-400 mb-2 uppercase tracking-wide">التعليمات</p>
                            <div class="text-sm text-slate-800 dark:text-slate-200 whitespace-pre-wrap leading-relaxed"><?php echo e($assignment->instructions); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if(count($instrFiles) > 0): ?>
                <div class="bg-white dark:bg-gray-800/80 border border-sky-200/80 dark:border-sky-800/50 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-4 py-3 sm:px-6 border-b border-sky-100 dark:border-sky-900/40 bg-sky-50/80 dark:bg-sky-950/25">
                        <h2 class="text-base font-bold text-sky-900 dark:text-sky-200 flex items-center gap-2">
                            <i class="fas fa-paperclip text-sky-600 dark:text-sky-400"></i>
                            ملفات من المدرب
                        </h2>
                    </div>
                    <div class="p-4 sm:p-6">
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php $__currentLoopData = $instrFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $p = is_array($att) ? ($att['path'] ?? '') : '';
                                    $url = $p ? (\App\Services\AssignmentFileStorage::publicUrl($p)) : null;
                                    $label = is_array($att) ? ($att['original_name'] ?? basename($p)) : '';
                                    $mime = is_array($att) ? ($att['mime'] ?? '') : '';
                                    $isImg = $mime && str_starts_with((string) $mime, 'image/');
                                ?>
                                <?php if($url): ?>
                                    <li class="text-sm rounded-xl border border-sky-100 dark:border-sky-900/40 overflow-hidden bg-sky-50/30 dark:bg-slate-900/30">
                                        <?php if($isImg): ?>
                                            <a href="<?php echo e($url); ?>" target="_blank" rel="noopener" class="block">
                                                <img src="<?php echo e($url); ?>" alt="<?php echo e($label); ?>" class="w-full max-h-56 object-cover">
                                            </a>
                                            <div class="p-3">
                                                <a href="<?php echo e($url); ?>" target="_blank" rel="noopener" class="text-sky-700 dark:text-sky-300 font-semibold hover:underline text-xs"><?php echo e($label); ?></a>
                                            </div>
                                        <?php else: ?>
                                            <a href="<?php echo e($url); ?>" target="_blank" rel="noopener" class="flex items-center gap-3 p-4 hover:bg-sky-50/80 dark:hover:bg-sky-950/30 transition-colors">
                                                <span class="w-10 h-10 rounded-lg bg-white dark:bg-gray-800 border border-sky-200 dark:border-sky-800 flex items-center justify-center text-sky-600 dark:text-sky-400 shrink-0">
                                                    <i class="fas fa-file-download"></i>
                                                </span>
                                                <span class="font-semibold text-sky-800 dark:text-sky-200 min-w-0 break-words"><?php echo e($label); ?></span>
                                            </a>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="xl:col-span-5 2xl:col-span-4 space-y-5 sm:space-y-6 min-w-0">
            <?php if($submission): ?>
                <div class="bg-white dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700/80 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-4 py-3 sm:px-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900/40">
                        <h2 class="text-base font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                            <i class="fas fa-user-check text-[#283593] dark:text-indigo-400"></i>
                            تسليمك
                        </h2>
                    </div>
                    <div class="p-4 sm:p-5 space-y-3">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            الحالة:
                            <?php if($submission->status === 'submitted'): ?>
                                <span class="font-semibold text-sky-700 dark:text-sky-300">قيد التصحيح</span>
                            <?php elseif($submission->status === 'graded'): ?>
                                <span class="font-semibold text-emerald-700 dark:text-emerald-400">مُقيَّم</span>
                                <?php if($submission->score !== null): ?>
                                    <span class="text-gray-700 dark:text-gray-300"> — الدرجة: <?php echo e($submission->score); ?> / <?php echo e($assignment->max_score); ?></span>
                                <?php endif; ?>
                            <?php elseif($submission->status === 'returned'): ?>
                                <span class="font-semibold text-violet-700 dark:text-violet-300">مُعاد للتعديل</span>
                            <?php endif; ?>
                        </p>
                        <?php if($submission->submitted_at): ?>
                            <p class="text-xs text-gray-500 dark:text-gray-400">آخر إرسال: <?php echo e($submission->submitted_at->timezone(config('app.timezone'))->format('Y-m-d H:i')); ?></p>
                        <?php endif; ?>
                        <?php if($submission->content): ?>
                            <div class="rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 p-4 text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap max-h-64 overflow-y-auto"><?php echo e($submission->content); ?></div>
                        <?php endif; ?>
                        <?php if(is_array($submission->attachments) && count($submission->attachments)): ?>
                            <div>
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">المرفقات</p>
                                <ul class="space-y-1.5">
                                    <?php $__currentLoopData = $submission->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $p = is_array($att) ? ($att['path'] ?? null) : null;
                                            $name = is_array($att) ? ($att['original_name'] ?? basename((string) $p)) : '';
                                            $fileUrl = $p ? \App\Services\AssignmentFileStorage::publicUrl($p) : null;
                                        ?>
                                        <?php if($fileUrl): ?>
                                            <li>
                                                <a href="<?php echo e($fileUrl); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-[#283593] dark:text-indigo-300 hover:text-[#FB5607] dark:hover:text-orange-300 text-sm font-medium">
                                                    <i class="fas fa-paperclip text-xs"></i><?php echo e($name); ?>

                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <?php if($submission->feedback): ?>
                            <div class="rounded-xl border border-amber-200 dark:border-amber-800/50 bg-amber-50 dark:bg-amber-900/15 p-4">
                                <p class="text-xs font-bold text-amber-800 dark:text-amber-200 mb-1">ملاحظات المُصحّح</p>
                                <p class="text-sm text-amber-950 dark:text-amber-100 whitespace-pre-wrap"><?php echo e($submission->feedback); ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if(!empty($canDeleteSubmission)): ?>
                            <form action="<?php echo e(route('student.assignments.submission.destroy', $assignment)); ?>" method="post" class="pt-2 border-t border-gray-200 dark:border-gray-600" onsubmit="return confirm('سيتم حذف التسليم بالكامل ومرفقاته من التخزين. هل أنت متأكد؟');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-rose-200 dark:border-rose-800/60 bg-rose-50 dark:bg-rose-950/30 text-rose-800 dark:text-rose-200 px-4 py-2.5 text-sm font-bold hover:bg-rose-100 dark:hover:bg-rose-950/50 transition-colors">
                                    <i class="fas fa-trash-alt"></i>
                                    حذف التسليم (قبل انتهاء الموعد)
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($canSubmit): ?>
                <div class="bg-white dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700/80 rounded-xl shadow-sm overflow-hidden ring-1 ring-[#283593]/5 dark:ring-indigo-500/10">
                    <div class="px-4 py-3 sm:px-5 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-l from-[#fff7ed] to-white dark:from-[#431407]/30 dark:to-[#1e293b]">
                        <h2 class="text-base font-bold text-gray-900 dark:text-gray-100">
                            <?php if($submission && $submission->status === 'returned'): ?>
                                إعادة إرسال التسليم
                            <?php else: ?>
                                تسليم الواجب
                            <?php endif; ?>
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">تسليم واحد فقط بعد الإرسال. يمكنك حذف التسليم قبل انتهاء الموعد لإرسال نسخة جديدة.</p>
                        <?php if(!empty($directUploadToCloud)): ?>
                            <p class="text-[11px] text-emerald-700 dark:text-emerald-400 mt-2 flex items-start gap-1.5">
                                <i class="fas fa-cloud-upload-alt mt-0.5"></i>
                                <span>المرفقات تُرفع مباشرة إلى التخزين السحابي (R2/S3) دون المرور بحجم حد PHP.</span>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="p-4 sm:p-5">
                        <form id="assignment-submit-form" action="<?php echo e(route('student.assignments.submit', $assignment)); ?>" method="post" enctype="multipart/form-data" class="space-y-4">
                            <?php echo csrf_field(); ?>
                            <div id="assignment-direct-tokens"></div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">النص</label>
                                <textarea name="content" rows="8" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[#283593]/30 focus:border-[#283593] dark:focus:border-indigo-500 transition-shadow"><?php echo e(old('content', ($submission && $submission->status === 'returned') ? ($submission->content ?? '') : '')); ?></textarea>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">اختياري إذا أرفقت ملفات.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">مرفقات</label>
                                <?php if(!empty($directUploadToCloud)): ?>
                                    <div class="space-y-3">
                                        <input type="file" id="mx-assignment-direct-picker" multiple accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png,.gif,.webp" class="block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#f2f4ff] dark:file:bg-indigo-900/50 file:text-[#283593] dark:file:text-indigo-200 hover:file:bg-[#e8ecff] dark:hover:file:bg-indigo-900/70 cursor-pointer">
                                        <p id="mx-assignment-upload-status" class="text-xs text-gray-500 dark:text-gray-400 hidden"></p>
                                        <ul id="mx-assignment-remote-list" class="space-y-2 text-sm"></ul>
                                    </div>
                                <?php endif; ?>
                                <div id="mx-assignment-classic-files-wrap" class="<?php echo e(!empty($directUploadToCloud) ? 'hidden' : ''); ?> <?php echo e(!empty($directUploadToCloud) ? '' : 'mt-0'); ?> space-y-2">
                                    <?php if(!empty($directUploadToCloud)): ?>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">أو عند تعذّر الرفع السحابي:</p>
                                    <?php endif; ?>
                                    <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png,.gif,.webp" class="block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#f2f4ff] dark:file:bg-indigo-900/50 file:text-[#283593] dark:file:text-indigo-200 hover:file:bg-[#e8ecff] dark:hover:file:bg-indigo-900/70 cursor-pointer">
                                </div>
                                <?php if(!empty($directUploadToCloud)): ?>
                                    <button type="button" id="mx-assignment-toggle-classic" class="mt-2 text-xs font-semibold text-[#283593] dark:text-indigo-300 hover:underline">
                                        رفع عبر الخادم (بديل)
                                    </button>
                                <?php endif; ?>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">حتى 10 ملفات، 40 ميجابايت لكل ملف — PDF، Word، صور، أرشيف.
                                    <?php if($submission && $submission->status === 'returned'): ?>
                                        <span class="font-medium text-violet-700 dark:text-violet-300">المرفقات السابقة تبقى ما لم تحذفها من التسليم السابق عبر «حذف التسليم».</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-l from-[#283593] to-[#3949ab] hover:from-[#1F2A7A] hover:to-[#283593] text-white px-6 py-3 rounded-xl font-bold text-sm shadow-md shadow-[#283593]/20 transition-all">
                                <i class="fas fa-paper-plane"></i>
                                إرسال التسليم
                            </button>
                        </form>
                    </div>
                </div>
            <?php elseif($submitBlockReason): ?>
                <div class="rounded-xl border border-amber-200 dark:border-amber-800/60 bg-amber-50 dark:bg-amber-900/20 text-amber-900 dark:text-amber-200 px-4 py-4 text-sm font-medium flex gap-3 items-start">
                    <i class="fas fa-info-circle mt-0.5 text-amber-600 dark:text-amber-400 shrink-0"></i>
                    <span><?php echo e($submitBlockReason); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php if(!empty($directUploadToCloud)): ?>
<?php $__env->startPush('scripts'); ?>
<script>
(function() {
    var cfg = {
        presignUrl: <?php echo json_encode(route('student.assignments.submission.presign-upload', $assignment), 512) ?>,
        completeUrl: <?php echo json_encode(route('student.assignments.submission.complete-upload', $assignment), 512) ?>,
        abandonUrl: <?php echo json_encode(route('student.assignments.submission.abandon-upload', $assignment), 512) ?>,
        csrf: <?php echo json_encode(csrf_token(), 15, 512) ?>,
        maxFiles: 10,
        maxBytes: 40960 * 1024
    };
    var form = document.getElementById('assignment-submit-form');
    var tokenBox = document.getElementById('assignment-direct-tokens');
    var picker = document.getElementById('mx-assignment-direct-picker');
    var listEl = document.getElementById('mx-assignment-remote-list');
    var statusEl = document.getElementById('mx-assignment-upload-status');
    var classicWrap = document.getElementById('mx-assignment-classic-files-wrap');
    var toggleClassic = document.getElementById('mx-assignment-toggle-classic');
    if (!form || !tokenBox || !picker || !listEl) return;

    var remoteItems = [];

    function setStatus(msg, isErr) {
        if (!statusEl) return;
        if (!msg) {
            statusEl.classList.add('hidden');
            statusEl.textContent = '';
            return;
        }
        statusEl.classList.remove('hidden');
        statusEl.textContent = msg;
        statusEl.classList.toggle('text-rose-600', !!isErr);
        statusEl.classList.toggle('dark:text-rose-400', !!isErr);
        statusEl.classList.toggle('text-gray-500', !isErr);
    }

    function syncHiddenTokens() {
        tokenBox.innerHTML = '';
        remoteItems.forEach(function(item) {
            if (!item.file_token) return;
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'direct_file_tokens[]';
            inp.value = item.file_token;
            tokenBox.appendChild(inp);
        });
    }

    function totalAttachmentSlots() {
        var classicInput = classicWrap ? classicWrap.querySelector('input[type="file"]') : null;
        var nClassic = 0;
        if (classicInput && classicInput.files) nClassic = classicInput.files.length;
        return remoteItems.length + nClassic;
    }

    function putBlobToPresignedUrl(url, blob, contentType, extraHeaders) {
        return new Promise(function(resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open('PUT', url, true);
            xhr.timeout = 0;
            if (contentType) xhr.setRequestHeader('Content-Type', contentType);
            if (extraHeaders && typeof extraHeaders === 'object') {
                Object.keys(extraHeaders).forEach(function(k) {
                    try { xhr.setRequestHeader(k, extraHeaders[k]); } catch (e) {}
                });
            }
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) resolve();
                else reject(new Error('HTTP ' + xhr.status));
            };
            xhr.onerror = function() { reject(new Error('network')); };
            xhr.send(blob);
        });
    }

    function renderList() {
        listEl.innerHTML = '';
        remoteItems.forEach(function(item, idx) {
            var li = document.createElement('li');
            li.className = 'flex items-center justify-between gap-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 px-3 py-2';
            var label = document.createElement('span');
            label.className = 'truncate text-gray-800 dark:text-gray-200';
            label.textContent = item.name + (item.uploading ? ' — جاري الرفع…' : '');
            li.appendChild(label);
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'shrink-0 text-rose-600 dark:text-rose-400 text-xs font-bold hover:underline disabled:opacity-50';
            btn.textContent = 'إزالة';
            btn.disabled = !!item.uploading;
            btn.addEventListener('click', function() {
                if (item.uploading) return;
                if (item.file_token) {
                    fetch(cfg.abandonUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': cfg.csrf,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ file_token: item.file_token })
                    }).catch(function() {});
                }
                remoteItems.splice(idx, 1);
                syncHiddenTokens();
                renderList();
            });
            li.appendChild(btn);
            listEl.appendChild(li);
        });
        syncHiddenTokens();
    }

    async function uploadFile(file) {
        if (file.size > cfg.maxBytes) {
            setStatus('الملف «' + file.name + '» يتجاوز 40 ميجابايت.', true);
            return;
        }
        if (totalAttachmentSlots() >= cfg.maxFiles) {
            setStatus('الحد 10 ملفات في التسليم الواحد.', true);
            return;
        }
        var entry = { name: file.name, file_token: null, uploading: true };
        remoteItems.push(entry);
        renderList();
        setStatus('جاري رفع «' + file.name + '»…', false);
        try {
            var presignRes = await fetch(cfg.presignUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': cfg.csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    content_type: file.type || 'application/octet-stream',
                    original_name: file.name
                })
            });
            var presignData = {};
            try { presignData = await presignRes.json(); } catch (e) { presignData = {}; }
            if (!presignRes.ok || !presignData.direct_upload || !presignData.upload_url || !presignData.upload_token) {
                var msg = (presignData && presignData.message) ? presignData.message : 'تعذر تجهيز الرفع.';
                throw new Error(msg);
            }
            await putBlobToPresignedUrl(
                presignData.upload_url,
                file,
                presignData.content_type || file.type || 'application/octet-stream',
                presignData.headers || {}
            );
            var completeRes = await fetch(cfg.completeUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': cfg.csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    upload_token: presignData.upload_token,
                    original_name: file.name
                })
            });
            var completeData = {};
            try { completeData = await completeRes.json(); } catch (e2) { completeData = {}; }
            if (!completeRes.ok || !completeData.file_token) {
                throw new Error((completeData && completeData.message) ? completeData.message : 'فشل تأكيد الملف.');
            }
            entry.uploading = false;
            entry.file_token = completeData.file_token;
            setStatus('', false);
        } catch (err) {
            var ix = remoteItems.indexOf(entry);
            if (ix >= 0) remoteItems.splice(ix, 1);
            setStatus((err && err.message) ? err.message : 'فشل الرفع.', true);
        }
        renderList();
        picker.value = '';
    }

    picker.addEventListener('change', function() {
        var files = picker.files;
        if (!files || !files.length) return;
        setStatus('', false);
        var chain = Promise.resolve();
        for (var i = 0; i < files.length; i++) {
            (function(f) {
                chain = chain.then(function() { return uploadFile(f); });
            })(files[i]);
        }
    });

    if (toggleClassic && classicWrap) {
        toggleClassic.addEventListener('click', function() {
            classicWrap.classList.toggle('hidden');
        });
    }

    form.addEventListener('submit', function(e) {
        if (remoteItems.some(function(x) { return x.uploading; })) {
            e.preventDefault();
            setStatus('انتظر انتهاء رفع كل الملفات.', true);
            return;
        }
        if (totalAttachmentSlots() > cfg.maxFiles) {
            e.preventDefault();
            setStatus('لا يمكن تجاوز 10 ملفات.', true);
        }
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\assignments\show.blade.php ENDPATH**/ ?>