

<?php $__env->startSection('title', __('instructor.my_requests_to_management') . ' - ' . config('app.name', 'Muallimx')); ?>
<?php $__env->startSection('header', __('instructor.submit_requests_to_management')); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <?php if(session('success')): ?>
        <div class="mb-6 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 text-emerald-800 px-4 py-3">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="mb-6 flex justify-end">
        <a href="<?php echo e(route('instructor.management-requests.create')); ?>"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all">
            <i class="fas fa-plus"></i>
            <?php echo e(__('instructor.new_request')); ?>

        </a>
    </div>

    <div class="bg-white dark:bg-slate-800/95 rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-2xl font-black text-gray-900 flex items-center gap-3">
                <i class="fas fa-inbox text-indigo-600"></i>
                <?php echo e(__('instructor.my_requests_to_management')); ?>

            </h2>
            <p class="text-gray-500 mt-1"><?php echo e(__('instructor.my_requests_description')); ?></p>
        </div>

        <form method="GET" class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-wrap gap-4">
            <select name="status" class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value=""><?php echo e(__('instructor.all_statuses_filter')); ?></option>
                <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>><?php echo e(__('instructor.pending_review')); ?></option>
                <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>><?php echo e(__('instructor.approved')); ?></option>
                <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>><?php echo e(__('instructor.rejected')); ?></option>
            </select>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-indigo-700"><?php echo e(__('common.search')); ?></button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-900"><?php echo e(__('instructor.request_subject')); ?></th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-900"><?php echo e(__('common.status')); ?></th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-900"><?php echo e(__('common.date')); ?></th>
                        <th class="px-6 py-4 text-center text-sm font-bold text-gray-900"><?php echo e(__('instructor.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800/95 divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/40 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-900"><?php echo e($req->subject); ?></p>
                            <p class="text-sm text-gray-500 mt-0.5"><?php echo e(Str::limit($req->message, 60)); ?></p>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($req->status == 'pending'): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800"><?php echo e(__('instructor.pending_review')); ?></span>
                            <?php elseif($req->status == 'approved'): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800"><?php echo e(__('instructor.approved')); ?></span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 dark:bg-rose-900/40 text-rose-800"><?php echo e(__('instructor.rejected')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <?php echo e($req->created_at->format('Y-m-d H:i')); ?>

                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="<?php echo e(route('instructor.management-requests.show', $req)); ?>"
                               class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-semibold text-sm">
                                <i class="fas fa-eye"></i>
                                <?php echo e(__('common.view')); ?>

                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                            <p class="font-medium"><?php echo e(__('instructor.no_requests_yet')); ?></p>
                            <a href="<?php echo e(route('instructor.management-requests.create')); ?>" class="mt-2 inline-block text-indigo-600 font-semibold"><?php echo e(__('instructor.new_request')); ?></a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($requests->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <?php echo e($requests->appends(request()->query())->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\instructor\management-requests\index.blade.php ENDPATH**/ ?>