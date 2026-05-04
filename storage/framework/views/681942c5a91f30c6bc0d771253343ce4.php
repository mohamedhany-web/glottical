<?php $__env->startSection('title', 'إنشاء فاتورة جديدة'); ?>
<?php $__env->startSection('header', 'إنشاء فاتورة جديدة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">إنشاء فاتورة جديدة</h1>
        
        <form action="<?php echo e(route('admin.invoices.store')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العميل *</label>
                    <label for="invoice-client-search" class="sr-only">بحث عن عميل بالاسم أو البريد</label>
                    <input type="search" id="invoice-client-search" autocomplete="off" placeholder="بحث بالاسم أو البريد أو الجوال…"
                           class="w-full mb-2 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    <select id="invoice-user-id" name="user_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">اختر العميل</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $searchHaystack = mb_strtolower(
                                trim($user->name.' '.($user->email ?? '').' '.($user->phone ?? '')),
                                'UTF-8'
                            );
                        ?>
                        <option value="<?php echo e($user->id); ?>" data-search="<?php echo e(e($searchHaystack)); ?>"><?php echo e($user->name); ?> — <?php echo e($user->email); ?> <?php if($user->phone): ?> · <?php echo e($user->phone); ?> <?php endif; ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع الفاتورة *</label>
                    <select name="type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="course">كورس</option>
                        <option value="subscription">اشتراك</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ الفرعي *</label>
                    <input type="number" name="subtotal" step="0.01" min="0" required value="<?php echo e(old('subtotal')); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الضريبة</label>
                    <input type="number" name="tax_amount" step="0.01" min="0" value="<?php echo e(old('tax_amount', 0)); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الخصم</label>
                    <input type="number" name="discount_amount" step="0.01" min="0" value="<?php echo e(old('discount_amount', 0)); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الاستحقاق</label>
                    <input type="date" name="due_date" value="<?php echo e(old('due_date')); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"><?php echo e(old('description')); ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"><?php echo e(old('notes')); ?></textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    إنشاء الفاتورة
                </button>
                <a href="<?php echo e(route('admin.invoices.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    var searchInput = document.getElementById('invoice-client-search');
    var select = document.getElementById('invoice-user-id');
    if (!searchInput || !select) return;
    var options = Array.prototype.slice.call(select.querySelectorAll('option'));
    function applyFilter() {
        var q = (searchInput.value || '').trim().toLowerCase();
        options.forEach(function (opt) {
            if (!opt.value) {
                opt.hidden = false;
                return;
            }
            if (opt.selected) {
                opt.hidden = false;
                return;
            }
            var hay = (opt.getAttribute('data-search') || '').toLowerCase();
            opt.hidden = q.length > 0 && hay.indexOf(q) === -1;
        });
    }
    searchInput.addEventListener('input', applyFilter);
    searchInput.addEventListener('search', applyFilter);
    select.addEventListener('change', applyFilter);
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\invoices\create.blade.php ENDPATH**/ ?>