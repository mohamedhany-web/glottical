<?php $__env->startSection('title', __('student.portfolio_marketing.create_title')); ?>
<?php $__env->startSection('header', __('student.portfolio_marketing.create_header')); ?>

<?php $__env->startSection('content'); ?>
<?php
    $maxMb = max(1, round((int) config('upload_limits.max_upload_kb') / 1024, 1));
    $pfDisk = (string) config('filesystems.portfolio_disk', 'public');
?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/25 border border-emerald-200 dark:border-emerald-800/60 px-4 py-3 flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400"></i>
            <span class="font-semibold text-emerald-800 dark:text-emerald-200"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-red-50 dark:bg-red-900/25 border border-red-200 dark:border-red-800/60 px-4 py-3 flex items-center gap-3">
            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
            <span class="font-semibold text-red-800 dark:text-red-200"><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div class="min-w-0">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-200 text-xs font-bold mb-3">
                <i class="fas fa-user-tie"></i>
                <?php echo e(__('student.portfolio_marketing.intro_badge')); ?>

            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight"><?php echo e(__('student.portfolio_marketing.intro_title')); ?></h1>
            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 mt-2 max-w-2xl leading-relaxed">
                <?php echo e(__('student.portfolio_marketing.intro_lead')); ?>

            </p>
        </div>
        <a href="<?php echo e(route('student.portfolio.index')); ?>" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-white dark:bg-slate-800/90 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 text-sm font-bold shadow-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shrink-0">
            <i class="fas fa-arrow-right"></i>
            <?php echo e(__('student.portfolio_marketing.back_to_list')); ?>

        </a>
    </div>

    <?php if($pfDisk === 'r2'): ?>
        <div class="rounded-2xl bg-gradient-to-r from-sky-50 to-cyan-50 dark:from-sky-950/40 dark:to-cyan-950/30 border border-sky-200/80 dark:border-sky-800/50 px-5 py-4 flex gap-4 text-sm text-sky-950 dark:text-sky-100">
            <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-900/50 border border-sky-200/60 dark:border-sky-700 flex items-center justify-center shrink-0 text-sky-600 dark:text-sky-400">
                <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <div>
                <p class="font-bold"><?php echo e(__('student.portfolio_marketing.r2_title')); ?></p>
                <p class="text-sky-900/85 dark:text-sky-200/90 mt-1 leading-relaxed"><?php echo e(__('student.portfolio_marketing.r2_body')); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/60 px-5 py-4">
            <p class="font-bold text-red-800 dark:text-red-200 mb-2 flex items-center gap-2">
                <i class="fas fa-circle-exclamation"></i>
                <?php echo e(__('student.portfolio_marketing.form_errors_title')); ?>

            </p>
            <ul class="list-disc list-inside text-red-800 dark:text-red-200/95 text-sm space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($e); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('student.portfolio.store')); ?>" method="POST" enctype="multipart/form-data"
          class="bg-white dark:bg-slate-800/95 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden"
          x-data="{ type: <?php echo e(json_encode(old('content_type', 'gallery'))); ?> }">
        <?php echo csrf_field(); ?>

        <div class="px-5 sm:px-8 py-5 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-l from-slate-50 to-white dark:from-slate-800/80 dark:to-slate-900/40">
            <h2 class="font-bold text-lg text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 flex items-center justify-center text-sm">
                    <i class="fas fa-sliders-h"></i>
                </span>
                <?php echo e(__('student.portfolio_marketing.section_type_title')); ?>

            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1"><?php echo e(__('student.portfolio_marketing.section_content_hint')); ?></p>
        </div>

        <div class="p-5 sm:p-8 space-y-8">
            <div>
                <label class="block text-sm font-bold text-slate-900 dark:text-slate-100 mb-3"><?php echo e(__('student.portfolio_marketing.content_type_label')); ?> <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <?php $__currentLoopData = $contentTypeLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="relative block cursor-pointer select-none group">
                            <input type="radio" name="content_type" value="<?php echo e($k); ?>" x-model="type"
                                   class="peer absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <span class="flex flex-col items-center justify-center gap-2 min-h-[5.5rem] px-3 py-4 rounded-2xl border-2 border-slate-200 dark:border-slate-600 bg-slate-50/50 dark:bg-slate-900/30 transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50/80 dark:peer-checked:bg-emerald-950/30 peer-checked:shadow-md peer-checked:shadow-emerald-500/10 text-sm font-black text-slate-800 dark:text-slate-100 group-hover:border-slate-300 dark:group-hover:border-slate-500">
                                <?php if($k === 'gallery'): ?> <i class="fas fa-images text-xl text-emerald-600 dark:text-emerald-400"></i> <?php endif; ?>
                                <?php if($k === 'video'): ?> <i class="fas fa-video text-xl text-emerald-600 dark:text-emerald-400"></i> <?php endif; ?>
                                <?php if($k === 'text'): ?> <i class="fas fa-align-right text-xl text-emerald-600 dark:text-emerald-400"></i> <?php endif; ?>
                                <?php if($k === 'link'): ?> <i class="fas fa-link text-xl text-emerald-600 dark:text-emerald-400"></i> <?php endif; ?>
                                <span class="text-center leading-snug"><?php echo e($label); ?></span>
                            </span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-900 dark:text-slate-100 mb-2"><?php echo e(__('student.portfolio_marketing.field_title')); ?> <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="<?php echo e(old('title')); ?>" required autocomplete="off"
                           placeholder="<?php echo e(__('student.portfolio_marketing.placeholder_title')); ?>"
                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-4 py-3 bg-white dark:bg-slate-900/40 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-900 dark:text-slate-100 mb-2"><?php echo e(__('student.portfolio_marketing.field_category')); ?></label>
                    <select name="project_type" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-4 py-3 bg-white dark:bg-slate-900/40 text-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        <option value="">—</option>
                        <?php $__currentLoopData = $projectTypeLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $plabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>" <?php if(old('project_type') === $val): echo 'selected'; endif; ?>><?php echo e($plabel); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-900 dark:text-slate-100 mb-2"><?php echo e(__('student.portfolio_marketing.field_extra_link')); ?></label>
                    <input type="url" name="github_url" value="<?php echo e(old('github_url')); ?>" placeholder="<?php echo e(__('student.portfolio_marketing.placeholder_extra_link')); ?>"
                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-4 py-3 bg-white dark:bg-slate-900/40 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5"><?php echo e(__('student.portfolio_marketing.field_extra_link_hint')); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-900 dark:text-slate-100 mb-2"><?php echo e(__('student.portfolio_marketing.field_learning_path')); ?></label>
                    <select name="academic_year_id" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-4 py-3 bg-white dark:bg-slate-900/40 text-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        <option value="">—</option>
                        <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($y->id); ?>" <?php if((string) old('academic_year_id') === (string) $y->id): echo 'selected'; endif; ?>><?php echo e($y->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-900 dark:text-slate-100 mb-2"><?php echo e(__('student.portfolio_marketing.field_course')); ?></label>
                    <select name="advanced_course_id" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-4 py-3 bg-white dark:bg-slate-900/40 text-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        <option value="">—</option>
                        <?php $__currentLoopData = $advancedCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->id); ?>" <?php if((string) old('advanced_course_id') === (string) $c->id): echo 'selected'; endif; ?>><?php echo e($c->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-900 dark:text-slate-100 mb-2"><?php echo e(__('student.portfolio_marketing.field_description')); ?></label>
                <textarea name="description" rows="3" placeholder="<?php echo e(__('student.portfolio_marketing.placeholder_description')); ?>"
                          class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-4 py-3 bg-white dark:bg-slate-900/40 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"><?php echo e(old('description')); ?></textarea>
            </div>

            <div x-show="type === 'text'" x-cloak class="rounded-2xl border border-slate-200 dark:border-slate-600 p-5 bg-slate-50/80 dark:bg-slate-900/30">
                <label class="block text-sm font-bold text-slate-900 dark:text-slate-100 mb-2"><?php echo e(__('student.portfolio_marketing.field_content_text')); ?></label>
                <textarea name="content_text" rows="10" placeholder="<?php echo e(__('student.portfolio_marketing.placeholder_content_text')); ?>"
                          class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-4 py-3 bg-white dark:bg-slate-900/40 text-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"><?php echo e(old('content_text')); ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div x-show="type === 'link'" x-cloak class="rounded-2xl border border-slate-200 dark:border-slate-600 p-5 bg-slate-50/80 dark:bg-slate-900/30 md:col-span-2">
                    <label class="block text-sm font-bold text-slate-900 dark:text-slate-100 mb-2"><?php echo e(__('student.portfolio_marketing.field_external_link')); ?></label>
                    <input type="url" name="project_url" value="<?php echo e(old('project_url')); ?>" placeholder="<?php echo e(__('student.portfolio_marketing.placeholder_url')); ?>"
                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-4 py-3 bg-white dark:bg-slate-900/40 text-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <div x-show="type === 'video'" x-cloak class="rounded-2xl border border-slate-200 dark:border-slate-600 p-5 bg-slate-50/80 dark:bg-slate-900/30 md:col-span-2">
                    <label class="block text-sm font-bold text-slate-900 dark:text-slate-100 mb-2"><?php echo e(__('student.portfolio_marketing.field_video')); ?> <span class="text-red-500">*</span></label>
                    <input type="url" name="video_url" value="<?php echo e(old('video_url')); ?>" placeholder="<?php echo e(__('student.portfolio_marketing.placeholder_url')); ?>"
                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-4 py-3 bg-white dark:bg-slate-900/40 text-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2"><?php echo e(__('student.portfolio_marketing.field_video_hint')); ?></p>
                </div>
            </div>

            <div class="rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-slate-50/50 dark:bg-slate-900/25 overflow-hidden" x-show="type === 'gallery'" x-cloak>
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-3 bg-white/60 dark:bg-slate-900/40">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 flex items-center justify-center">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </span>
                        <div>
                            <p class="font-bold text-slate-900 dark:text-slate-100"><?php echo e(__('student.portfolio_marketing.gallery_block_title')); ?></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo e(__('student.portfolio_marketing.gallery_block_sub', ['max' => $maxMb])); ?></p>
                        </div>
                    </div>
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-900 dark:text-amber-200"><?php echo e(__('student.portfolio_marketing.gallery_required_badge')); ?></span>
                </div>
                <div class="p-5 sm:p-6">
                    <label class="flex flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/30 px-4 py-10 cursor-pointer hover:border-emerald-400 dark:hover:border-emerald-600 hover:bg-emerald-50/30 dark:hover:bg-emerald-950/20 transition-colors">
                        <i class="fas fa-file-image text-3xl text-slate-400 dark:text-slate-500"></i>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200 text-center px-2"><?php echo e(__('student.portfolio_marketing.dropzone_label')); ?></span>
                        <input type="file" name="images[]" accept="image/*" multiple data-max="5" id="portfolio-images"
                               class="sr-only">
                    </label>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-3 text-center" id="images-hint"><?php echo e(__('student.portfolio_marketing.images_formats_hint')); ?></p>
                    <div id="portfolio-previews" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 mt-5 empty:hidden"></div>
                </div>
            </div>

            <div x-show="type !== 'gallery'" x-cloak class="rounded-xl border border-slate-200 dark:border-slate-600 px-4 py-3 text-sm text-slate-600 dark:text-slate-300 bg-slate-50/80 dark:bg-slate-900/30">
                <i class="fas fa-info-circle text-slate-400 ml-2"></i>
                <?php echo e(__('student.portfolio_marketing.non_gallery_note')); ?>

            </div>
        </div>

        <div class="px-5 sm:px-8 py-5 border-t border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-900/40 flex flex-col-reverse sm:flex-row gap-3 justify-end">
            <a href="<?php echo e(route('student.portfolio.index')); ?>" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 font-bold hover:bg-white dark:hover:bg-slate-800 transition-colors">
                <i class="fas fa-arrow-right"></i>
                <?php echo e(__('student.portfolio_marketing.btn_cancel')); ?>

            </a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-8 py-3 rounded-xl bg-gradient-to-l from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black shadow-lg shadow-emerald-600/25 transition-all">
                <i class="fas fa-paper-plane"></i>
                <?php echo e(__('student.portfolio_marketing.btn_submit_review')); ?>

            </button>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var input = document.getElementById('portfolio-images');
    var hint = document.getElementById('images-hint');
    var previews = document.getElementById('portfolio-previews');
    var dropLabel = input && input.closest('label');
    var msgFormats = <?php echo json_encode(__('student.portfolio_marketing.images_formats_hint'), 15, 512) ?>;
    var msgPickN = <?php echo json_encode(__('student.portfolio_marketing.images_hint_selected'), 15, 512) ?>;
    var msgTooMany = <?php echo json_encode(__('student.portfolio_marketing.images_hint_trim'), 15, 512) ?>;

    function setFiles(files) {
        if (!input || !files || !files.length) return;
        var max = 5;
        var dt = new DataTransfer();
        var n = Math.min(files.length, max);
        for (var i = 0; i < n; i++) dt.items.add(files[i]);
        input.files = dt.files;
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function renderPreviews(files) {
        if (!previews) return;
        previews.innerHTML = '';
        for (var i = 0; i < files.length; i++) {
            (function(f) {
                var r = new FileReader();
                r.onload = function(e) {
                    var wrap = document.createElement('div');
                    wrap.className = 'relative rounded-xl overflow-hidden border border-slate-200 dark:border-slate-600 aspect-square bg-slate-100 dark:bg-slate-800';
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-full object-cover';
                    img.alt = '';
                    wrap.appendChild(img);
                    previews.appendChild(wrap);
                };
                r.readAsDataURL(f);
            })(files[i]);
        }
    }

    if (input && hint && previews) {
        input.addEventListener('change', function() {
            var files = this.files;
            if (files.length > 5) {
                hint.textContent = msgTooMany;
                var dt = new DataTransfer();
                for (var i = 0; i < 5; i++) dt.items.add(files[i]);
                this.files = dt.files;
                files = this.files;
            } else if (files.length > 0) {
                hint.textContent = msgPickN.replace(':count', String(files.length));
            } else {
                hint.textContent = msgFormats;
            }
            renderPreviews(files);
        });

        if (dropLabel) {
            ['dragenter', 'dragover'].forEach(function(ev) {
                dropLabel.addEventListener(ev, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropLabel.classList.add('ring-2', 'ring-emerald-500', 'ring-offset-2', 'dark:ring-offset-slate-900');
                });
            });
            ['dragleave', 'drop'].forEach(function(ev) {
                dropLabel.addEventListener(ev, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropLabel.classList.remove('ring-2', 'ring-emerald-500', 'ring-offset-2', 'dark:ring-offset-slate-900');
                });
            });
            dropLabel.addEventListener('drop', function(e) {
                var fl = e.dataTransfer && e.dataTransfer.files;
                if (fl && fl.length) setFiles(fl);
            });
        }
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\portfolio\create.blade.php ENDPATH**/ ?>