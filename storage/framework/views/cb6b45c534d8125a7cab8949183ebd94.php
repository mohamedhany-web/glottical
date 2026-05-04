
<?php $__env->startSection('title', 'تصفح الملفات — ' . $server->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('admin.live-servers.index')); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500"><i class="fas fa-arrow-right"></i></a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white"><i class="fas fa-folder-open text-amber-500 ml-2"></i>تصفح الملفات — <?php echo e($server->name); ?></h1>
                <nav class="text-sm text-slate-500 mt-0.5 font-mono break-all">
                    <?php $parts = array_filter(explode('/', $path)); $current = ''; ?>
                    <a href="<?php echo e(route('admin.live-servers.ssh-browse', [$server, 'path' => '/'])); ?>" class="text-cyan-500 hover:underline">/</a>
                    <?php $__currentLoopData = $parts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $current .= '/' . $part; ?>
                        <span class="text-slate-400">/</span><a href="<?php echo e(route('admin.live-servers.ssh-browse', [$server, 'path' => $current])); ?>" class="text-cyan-500 hover:underline"><?php echo e($part); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </nav>
            </div>
        </div>
        <a href="<?php echo e(route('admin.live-servers.edit', $server)); ?>" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium hover:bg-slate-300"><i class="fas fa-cog ml-1"></i> إعدادات السيرفر</a>
    </div>

    <?php if(session('error')): ?>
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-red-700 dark:text-red-400 text-sm"><i class="fas fa-exclamation-circle ml-1"></i> <?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="text-right py-3 px-4 font-semibold text-slate-600 dark:text-slate-300">الاسم</th>
                    <th class="text-right py-3 px-4 font-semibold text-slate-600 dark:text-slate-300 w-24">الحجم</th>
                </tr>
            </thead>
            <tbody>
                <?php if($path !== '/'): ?>
                <?php $parent = dirname($path); if ($parent === '.') $parent = '/'; ?>
                <tr class="border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/30">
                    <td class="py-2 px-4" colspan="2">
                        <a href="<?php echo e(route('admin.live-servers.ssh-browse', [$server, 'path' => $parent])); ?>" class="flex items-center gap-2 text-cyan-500 hover:underline">
                            <i class="fas fa-level-up-alt"></i> المجلد الأعلى
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
                <?php $__currentLoopData = $dirs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dir): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/30">
                    <td class="py-2 px-4">
                        <a href="<?php echo e(route('admin.live-servers.ssh-browse', [$server, 'path' => $dir['path']])); ?>" class="flex items-center gap-2 text-slate-800 dark:text-white hover:text-cyan-500">
                            <i class="fas fa-folder text-amber-500"></i> <?php echo e($dir['name']); ?>

                        </a>
                    </td>
                    <td class="py-2 px-4 text-slate-500">—</td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/30">
                    <td class="py-2 px-4">
                        <a href="<?php echo e(route('admin.live-servers.ssh-file', [$server, 'path' => $file['path']])); ?>" class="flex items-center gap-2 text-slate-800 dark:text-white hover:text-cyan-500">
                            <i class="fas fa-file text-slate-400"></i> <?php echo e($file['name']); ?>

                        </a>
                    </td>
                    <td class="py-2 px-4 text-slate-500 font-mono text-xs"><?php echo e(number_format($file['size'])); ?> بايت</td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php if(empty($dirs) && empty($files) && $path === '/'): ?>
        <p class="py-8 text-center text-slate-500">المجلد فارغ أو لا يمكن قراءته.</p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\live-servers\ssh-browse.blade.php ENDPATH**/ ?>