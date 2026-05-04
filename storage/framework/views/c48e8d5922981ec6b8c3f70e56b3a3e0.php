<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title>419 - <?php echo e(__('errors.419_title')); ?> | <?php echo e(config('app.name')); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Noto+Sans+Arabic:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Cairo', 'Noto Sans Arabic', sans-serif; }
        body { min-height: 100vh; background: #f8fafc; overflow-x: hidden; }
        .hero-section {
            background: linear-gradient(to bottom, #eff6ff, #dbeafe, #ffffff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
    </style>
</head>
<body>
    <section class="hero-section px-4 py-16">
        <div class="max-w-xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-sky-500 to-indigo-600 rounded-full shadow-lg mb-6">
                <i class="fas fa-shield-halved text-white text-sm"></i>
                <span class="text-white font-bold text-sm"><?php echo e(__('errors.419_title')); ?></span>
            </div>
            <h1 class="text-7xl md:text-8xl font-black text-gray-900 mb-4">419</h1>
            <p class="text-lg text-gray-600 leading-relaxed mb-8"><?php echo e(__('errors.419_message')); ?></p>
            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-lg text-right mb-8">
                <div class="flex items-start gap-3 text-gray-700 text-sm">
                    <i class="fas fa-lightbulb text-amber-500 mt-0.5"></i>
                    <span><?php echo e(__('errors.419_hint')); ?></span>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button type="button" onclick="window.location.reload()" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-sky-600 to-indigo-600 text-white px-8 py-3 rounded-full font-bold shadow-lg hover:opacity-95 transition-opacity">
                    <i class="fas fa-rotate"></i>
                    <span><?php echo e(__('errors.refresh_page')); ?></span>
                </button>
                <a href="<?php echo e($errorHomeUrl); ?>" class="inline-flex items-center justify-center gap-2 bg-white text-sky-700 px-8 py-3 rounded-full font-bold border-2 border-sky-600 hover:bg-sky-50 transition-colors">
                    <i class="fas fa-home"></i>
                    <span><?php echo e($errorHomeLabel); ?></span>
                </a>
            </div>
        </div>
    </section>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\errors\419.blade.php ENDPATH**/ ?>