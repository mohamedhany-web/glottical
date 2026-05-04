<?php
    $brand = config('app.name');
?>

<?php $__env->startSection('title', __('public.faq_page_title') . ' - ' . __('public.site_suffix')); ?>
<?php $__env->startSection('meta_description', __('public.faq_meta_description', ['brand' => $brand])); ?>
<?php $__env->startSection('meta_keywords', __('public.faq_meta_keywords', ['brand' => $brand])); ?>
<?php $__env->startSection('canonical_url', url('/faq')); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .faq-acc-item {
        transition: box-shadow 0.25s ease, border-color 0.25s ease, transform 0.2s ease;
        border: 1px solid rgb(226 232 240);
    }
    .faq-acc-item:hover {
        border-color: rgba(40, 53, 147, 0.22);
        box-shadow: 0 14px 36px -18px rgba(31, 42, 122, 0.35);
    }
    .faq-acc-item.is-open {
        border-color: rgba(40, 53, 147, 0.35);
        box-shadow: 0 16px 40px -20px rgba(31, 42, 122, 0.3);
    }
    .faq-chevron {
        transition: transform 0.25s ease;
    }
    .faq-acc-item.is-open .faq-chevron {
        transform: rotate(180deg);
    }
    html.dark .faq-acc-item {
        border-color: rgb(51 65 85);
        background: rgb(30 41 59 / 0.85);
    }
    .filter-btn-faq.is-active {
        background: #283593;
        color: #fff;
        border-color: #283593;
        box-shadow: 0 8px 22px -10px rgba(40, 53, 147, 0.55);
    }
    .filter-btn-faq:not(.is-active) {
        background: #fff;
        color: rgb(51 65 85);
        border: 1px solid rgb(226 232 240);
    }
    html.dark .filter-btn-faq:not(.is-active) {
        background: rgb(30 41 59);
        color: rgb(226 232 240);
        border-color: rgb(71 85 105);
    }
    [x-cloak] { display: none !important; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<section class="pt-24 sm:pt-28 lg:pt-32 pb-10 sm:pb-12 overflow-hidden relative" style="background:radial-gradient(circle at 18% 75%,rgba(255,229,247,.7),transparent 32%),radial-gradient(circle at 92% 15%,rgba(251,86,7,.08),transparent 28%),linear-gradient(180deg,#eef1ff 0%,#fafbff 50%,#ffffff 100%)">
    <div class="absolute inset-0 pointer-events-none opacity-35" style="background-image:radial-gradient(circle at 1px 1px,rgba(40,53,147,.07) 1px,transparent 0);background-size:28px 28px"></div>
    <div class="w-full max-w-[1200px] mx-auto px-6 sm:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14 items-center">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-5" style="background:#FFE5F7;color:#283593;border:1px solid #f5c7e8">
                    <i class="fas fa-circle-question"></i> <?php echo e(__('public.faq_hero_highlight')); ?>

                </span>
                <h1 class="text-[1.75rem] sm:text-[2.35rem] lg:text-[2.85rem] leading-[1.15] font-black text-[#1F2A7A] dark:text-white mb-4" style="font-family:Tajawal,Cairo,sans-serif">
                    <?php echo e(__('public.faq_page_title')); ?>

                </h1>
                <p class="text-slate-600 dark:text-slate-400 text-base sm:text-lg leading-8 mb-6 max-w-xl">
                    <?php echo e(__('public.faq_hero_sub')); ?>

                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="<?php echo e(route('public.contact')); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl font-bold text-white px-6 py-3 shadow-lg transition-all hover:scale-[1.02]" style="background:#FB5607;box-shadow:0 12px 28px -10px rgba(251,86,7,.45)">
                        <i class="fas fa-envelope"></i> <?php echo e(__('public.contact_page_title')); ?>

                    </a>
                    <a href="<?php echo e(route('public.help')); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl font-bold px-6 py-3 border-2 transition-all text-[#1F2A7A] dark:text-slate-100 bg-white/90 dark:bg-slate-800 border-slate-200 dark:border-slate-600 hover:border-[#283593]/40">
                        <i class="fas fa-life-ring"></i> <?php echo e(__('public.help_page_title')); ?>

                    </a>
                </div>
            </div>
            
            <div class="relative min-h-[220px] lg:min-h-[280px]">
                <div class="absolute inset-0 rounded-[32px] opacity-90" style="background:linear-gradient(145deg,#283593 0%,#1F2A7A 45%,#3d4db8 100%);box-shadow:0 24px 50px -28px rgba(40,53,147,.55)"></div>
                <div class="relative p-6 sm:p-8 text-white h-full flex flex-col justify-center gap-4">
                    <div class="flex items-start gap-3 rounded-2xl bg-white/10 backdrop-blur-sm px-4 py-3 border border-white/15">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-lg" style="background:#FB5607">
                            <i class="fas fa-magnifying-glass"></i>
                        </span>
                        <div>
                            <p class="font-bold text-sm opacity-90"><?php echo e(__('public.faq_page_title')); ?></p>
                            <p class="text-sm text-white/80 leading-relaxed"><?php echo e(__('public.faq_sidebar_hint')); ?></p>
                        </div>
                    </div>
                    <div class="flex gap-3 flex-wrap">
                        <a href="<?php echo e(route('public.courses')); ?>" class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-bold bg-white/15 border border-white/20 hover:bg-white/25 transition-colors">
                            <i class="fas fa-chalkboard-user"></i> <?php echo e(__('public.courses_page_title')); ?>

                        </a>
                        <a href="<?php echo e(route('public.certificates')); ?>" class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-bold bg-white/15 border border-white/20 hover:bg-white/25 transition-colors">
                            <i class="fas fa-certificate"></i> <?php echo e(__('public.certificates_page_title')); ?>

                        </a>
                        <a href="<?php echo e(route('public.pricing')); ?>" class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-bold bg-white/15 border border-white/20 hover:bg-white/25 transition-colors">
                            <i class="fas fa-credit-card"></i> <?php echo e(__('public.pricing_page_title')); ?>

                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<div class="leading-none text-white dark:text-slate-900 -mt-px" aria-hidden="true">
    <svg class="w-full h-10 sm:h-14 text-white dark:text-slate-900" preserveAspectRatio="none" viewBox="0 0 1440 48" fill="currentColor">
        <path d="M0,24 C360,64 720,0 1080,24 C1260,36 1380,32 1440,28 L1440,48 L0,48 Z"/>
    </svg>
</div>

<?php
    $defaultGrouped = collect($defaultFaqs ?? [])->groupBy('category');
    $hasDbFaqs = isset($faqs) && $faqs->isNotEmpty();
?>

<section id="faq-main" class="pt-2 pb-14 sm:pb-16 bg-white dark:bg-slate-900">
    <div class="w-full max-w-[1200px] mx-auto px-6 sm:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12">
            
            <aside class="lg:col-span-4 xl:col-span-3 lg:sticky lg:top-28 lg:self-start space-y-6">
                <?php if(isset($categories) && $categories->isNotEmpty()): ?>
                <div class="rounded-[24px] border border-slate-200 dark:border-slate-700 bg-gradient-to-b from-slate-50 to-white dark:from-slate-800 dark:to-slate-900 p-5 sm:p-6 shadow-[0_16px_40px_-28px_rgba(31,42,122,.2)]">
                    <p class="text-xs font-bold uppercase tracking-wide text-[#283593] dark:text-indigo-300 mb-3"><?php echo e(__('public.faq_sidebar_categories')); ?></p>
                    <div class="flex flex-col gap-2">
                        <button type="button" class="filter-btn filter-btn-faq is-active w-full text-start rounded-xl px-4 py-2.5 text-sm font-bold transition-all" data-category="all">
                            <?php echo e(__('public.faq_filter_all')); ?>

                        </button>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="filter-btn filter-btn-faq w-full text-start rounded-xl px-4 py-2.5 text-sm font-semibold transition-all hover:border-[#283593]/35" data-category="<?php echo e($cat); ?>">
                            <?php echo e($cat); ?>

                        </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="rounded-[24px] overflow-hidden border border-slate-200 dark:border-slate-700" style="background:linear-gradient(160deg,#283593 0%,#1a237e 100%)">
                    <div class="p-5 sm:p-6 text-white">
                        <p class="text-xs font-bold uppercase tracking-wide text-white/70 mb-4"><?php echo e(__('public.faq_quick_links')); ?></p>
                        <ul class="space-y-2">
                            <li>
                                <a href="<?php echo e(route('public.contact')); ?>" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold bg-white/10 hover:bg-white/20 transition-colors">
                                    <i class="fas fa-paper-plane text-[#FFE569]"></i> <?php echo e(__('public.contact_page_title')); ?>

                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('public.help')); ?>" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold bg-white/10 hover:bg-white/20 transition-colors">
                                    <i class="fas fa-book-open text-[#FFE569]"></i> <?php echo e(__('public.help_page_title')); ?>

                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('public.privacy')); ?>" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold bg-white/10 hover:bg-white/20 transition-colors">
                                    <i class="fas fa-shield-halved text-[#FFE569]"></i> <?php echo e(__('public.privacy_page_title')); ?>

                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </aside>

            
            <div class="lg:col-span-8 xl:col-span-9 space-y-10 min-w-0">
                <?php if(isset($categories) && $categories->isNotEmpty()): ?>
                <div class="lg:hidden flex flex-wrap gap-2">
                    <button type="button" class="filter-btn filter-btn-faq is-active px-4 py-2 rounded-xl text-sm font-bold" data-category="all"><?php echo e(__('public.faq_filter_all')); ?></button>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button" class="filter-btn filter-btn-faq px-4 py-2 rounded-xl text-sm font-semibold" data-category="<?php echo e($cat); ?>"><?php echo e($cat); ?></button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>

                <?php if($hasDbFaqs): ?>
                <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryName => $categoryFaqs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="faq-block" data-category="<?php echo e($categoryName ?? 'general'); ?>">
                    <?php if($categoryName): ?>
                    <div class="flex items-center gap-3 mb-5">
                        <span class="w-1.5 h-8 rounded-full shrink-0" style="background:linear-gradient(180deg,#FB5607,#283593)"></span>
                        <h2 class="text-xl sm:text-2xl font-black text-[#1F2A7A] dark:text-white flex items-center gap-2" style="font-family:Tajawal,Cairo,sans-serif">
                            <i class="fas fa-layer-group text-[#283593] dark:text-indigo-400"></i>
                            <?php echo e($categoryName); ?>

                        </h2>
                    </div>
                    <?php endif; ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $categoryFaqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="faq-acc-item rounded-2xl overflow-hidden bg-white dark:bg-slate-800/90" x-data="{ open: false }" :class="{ 'is-open': open }">
                            <button type="button" @click="open = !open" class="w-full px-5 sm:px-6 py-4 text-start flex items-center justify-between gap-4 hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition-colors">
                                <span class="text-base font-bold text-slate-800 dark:text-slate-100 flex-1 min-w-0"><?php echo e($faq->question); ?></span>
                                <i class="fas fa-chevron-down faq-chevron text-[#283593] dark:text-indigo-400 flex-shrink-0"></i>
                            </button>
                            <div x-show="open" x-cloak class="border-t border-slate-100 dark:border-slate-700">
                                <div class="px-5 sm:px-6 py-4 text-slate-600 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
                                    <?php echo nl2br(e($faq->answer)); ?>

                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>

                <?php if($defaultGrouped->isNotEmpty()): ?>
                <div id="default" class="faq-block default-faqs" data-category="default">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="w-1.5 h-8 rounded-full shrink-0" style="background:linear-gradient(180deg,#283593,#FB5607)"></span>
                        <h2 class="text-xl sm:text-2xl font-black text-[#1F2A7A] dark:text-white flex items-center gap-2" style="font-family:Tajawal,Cairo,sans-serif">
                            <i class="fas fa-graduation-cap text-[#FB5607]"></i>
                            <?php echo e(__('public.faq_section_platform', ['brand' => $brand])); ?>

                        </h2>
                    </div>
                    <div class="space-y-8">
                        <?php $__currentLoopData = $defaultGrouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catName => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <?php if($catName): ?>
                            <h3 class="text-base font-bold text-slate-700 dark:text-slate-300 mb-3 flex items-center gap-2">
                                <i class="fas fa-tag text-[#283593] dark:text-indigo-400 text-sm"></i>
                                <?php echo e($catName); ?>

                            </h3>
                            <?php endif; ?>
                            <div class="space-y-3">
                                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="faq-acc-item rounded-2xl overflow-hidden bg-white dark:bg-slate-800/90" x-data="{ open: false }" :class="{ 'is-open': open }">
                                    <button type="button" @click="open = !open" class="w-full px-5 sm:px-6 py-4 text-start flex items-center justify-between gap-4 hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition-colors">
                                        <span class="text-base font-bold text-slate-800 dark:text-slate-100 flex-1 min-w-0"><?php echo e($item['question']); ?></span>
                                        <i class="fas fa-chevron-down faq-chevron text-[#283593] dark:text-indigo-400 flex-shrink-0"></i>
                                    </button>
                                    <div x-show="open" x-cloak class="border-t border-slate-100 dark:border-slate-700">
                                        <div class="px-5 sm:px-6 py-4 text-slate-600 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
                                            <?php echo nl2br(e($item['answer'] ?? '')); ?>

                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if(!$hasDbFaqs && $defaultGrouped->isEmpty()): ?>
                <div class="text-center py-16 rounded-[28px] border border-dashed border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/50">
                    <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-4 text-white text-3xl" style="background:linear-gradient(135deg,#283593,#1F2A7A)">
                        <i class="fas fa-question"></i>
                    </div>
                    <p class="text-slate-600 dark:text-slate-400 text-lg font-medium mb-5"><?php echo e(__('public.faq_empty_title')); ?></p>
                    <a href="<?php echo e(route('public.contact')); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl font-bold text-white transition-all hover:scale-[1.02]" style="background:#FB5607;box-shadow:0 12px 28px -10px rgba(251,86,7,.45)">
                        <i class="fas fa-envelope"></i>
                        <?php echo e(__('public.faq_empty_cta')); ?>

                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="pt-6 sm:pt-8 pb-14 sm:pb-16" style="background:linear-gradient(180deg,#f4f7ff 0%,#ffffff 100%)">
    <div class="w-full max-w-[1200px] mx-auto px-6 sm:px-8">
        <div class="rounded-[28px] border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-[0_20px_44px_-26px_rgba(31,42,122,.28)] px-6 sm:px-10 py-10 sm:py-12 text-center">
            <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-5" style="background:#FFE5F7;color:#283593">
                <i class="fas fa-headset"></i> <?php echo e(__('public.support')); ?>

            </span>
            <h3 class="text-2xl sm:text-3xl font-black mb-3 text-[#1F2A7A] dark:text-white" style="font-family:Tajawal,Cairo,sans-serif"><?php echo e(__('public.faq_cta_title')); ?></h3>
            <p class="text-slate-600 dark:text-slate-400 text-base sm:text-lg max-w-2xl mx-auto leading-8 mb-8">
                <?php echo e(__('public.faq_cta_desc')); ?>

            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
                <a href="<?php echo e(route('public.contact')); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl font-bold text-white px-8 py-3.5 transition-all hover:scale-[1.02]" style="background:#FB5607;box-shadow:0 12px 28px -10px rgba(251,86,7,.45)">
                    <i class="fas fa-paper-plane"></i> <?php echo e(__('public.faq_cta_btn')); ?>

                </a>
                <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl font-bold px-8 py-3.5 border-2 border-slate-200 dark:border-slate-600 text-[#1F2A7A] dark:text-slate-100 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <i class="fas fa-home"></i> <?php echo e(__('public.home')); ?>

                </a>
            </div>
        </div>
    </div>
</section>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var buttons = document.querySelectorAll('.filter-btn');
    var blocks = document.querySelectorAll('.faq-block');
    if (buttons.length === 0 || blocks.length === 0) return;
    buttons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var cat = this.getAttribute('data-category');
            buttons.forEach(function(b) {
                if (b.getAttribute('data-category') === cat) {
                    b.classList.add('is-active');
                } else {
                    b.classList.remove('is-active');
                }
            });
            blocks.forEach(function(block) {
                var blockCat = block.getAttribute('data-category');
                block.style.display = (cat === 'all' || blockCat === cat) ? '' : 'none';
            });
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\public\faq.blade.php ENDPATH**/ ?>