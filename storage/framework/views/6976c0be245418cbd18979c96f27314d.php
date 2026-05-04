<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title><?php echo e($statusCode); ?> - <?php echo e($title); ?> | <?php echo e(config('app.name')); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Noto+Sans+Arabic:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Cairo', 'Noto Sans Arabic', sans-serif; }
        body { min-height: 100vh; background: #f8fafc; overflow-x: hidden; }
        .hero-section {
            background: linear-gradient(to bottom, #f1f5f9, #e2e8f0, #ffffff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <section class="hero-section px-4 py-16">
        <div class="max-w-xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-600 rounded-full shadow mb-6">
                <i class="fas fa-circle-info text-white text-sm"></i>
                <span class="text-white font-bold text-sm"><?php echo e(__('errors.http_generic_title')); ?></span>
            </div>
            <h1 class="text-6xl md:text-7xl font-black text-gray-900 mb-2"><?php echo e($statusCode); ?></h1>
            <h2 class="text-2xl font-bold text-gray-800 mb-4"><?php echo e($title); ?></h2>
            <p class="text-lg text-gray-600 leading-relaxed mb-10"><?php echo e($message); ?></p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?php echo e($errorHomeUrl); ?>" class="inline-flex items-center justify-center gap-2 bg-slate-700 text-white px-8 py-3 rounded-full font-bold shadow-lg hover:bg-slate-800 transition-colors">
                    <i class="fas fa-home"></i>
                    <span><?php echo e($errorHomeLabel); ?></span>
                </a>
                <a href="javascript:history.back()" class="inline-flex items-center justify-center gap-2 bg-white text-slate-700 px-8 py-3 rounded-full font-bold border-2 border-slate-300 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    <span><?php echo e(__('errors.back_previous')); ?></span>
                </a>
            </div>
        </div>
    </section>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\errors\generic.blade.php ENDPATH**/ ?>