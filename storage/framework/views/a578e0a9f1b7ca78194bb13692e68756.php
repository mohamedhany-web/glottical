

<?php $__env->startSection('title', 'مشرف: '.$supervisor->name); ?>

<?php $__env->startSection('content'); ?>

<script type="application/json" id="academic-all-students-json"><?php echo json_encode($allStudents, 15, 512) ?></script>
<div class="space-y-6" x-data="{
    allStudents: [],
    q: '',
    selectedId: '',
    results: [],
    init() {
        var el = document.getElementById('academic-all-students-json');
        try {
            this.allStudents = el && el.textContent ? JSON.parse(el.textContent.trim()) : [];
        } catch (e) {
            this.allStudents = [];
        }
        this.results = this.allStudents.slice();
    },
    search() {
        var q = (this.q || '').trim().toLowerCase();
        var self = this;
        if (!q.length) {
            this.results = this.allStudents.slice();
        } else {
            this.results = this.allStudents.filter(function (u) {
                return [u.name, u.email, u.phone].filter(Boolean).some(function (v) {
                    return String(v).toLowerCase().indexOf(q) !== -1;
                });
            });
        }
        if (!this.results.some(function (u) { return String(u.id) === String(self.selectedId); })) {
            this.selectedId = '';
        }
    }
}">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?php echo e($supervisor->name); ?></h1>
            <p class="text-sm text-gray-600"><?php echo e($supervisor->email); ?></p>
        </div>
        <a href="<?php echo e(route('admin.academic-supervision.index')); ?>" class="text-sm font-semibold text-teal-700 hover:underline">← قائمة المشرفين</a>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 text-sm px-4 py-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('info')): ?>
        <div class="rounded-xl border border-sky-200 bg-sky-50 text-sky-800 text-sm px-4 py-3"><?php echo e(session('info')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-800 text-sm px-4 py-3"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <h2 class="text-base font-bold text-gray-900 mb-3">إضافة طالب</h2>
        <p class="text-xs text-gray-500 mb-2">ابحث بالاسم أو البريد أو الجوال — والدروب داون يعرض كل الطلاب.</p>
        <form method="post" action="<?php echo e(route('admin.academic-supervision.supervisors.students.attach', $supervisor)); ?>" class="max-w-4xl">
            <?php echo csrf_field(); ?>
            <div class="flex flex-col md:flex-row gap-2 md:items-center">
                <input type="search" x-model="q" @input.debounce.300ms="search()" placeholder="بحث عن طالب…"
                       class="w-full md:w-72 rounded-xl border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                <select name="student_id" x-model="selectedId" required
                        class="w-full md:flex-1 rounded-xl border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                    <option value="">اختر طالباً</option>
                    <template x-for="u in results" :key="u.id">
                        <option :value="u.id" x-text="`${u.name} — ${u.email || 'بدون بريد'}${u.phone ? ' — ' + u.phone : ''}`"></option>
                    </template>
                </select>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-teal-600 text-white text-sm font-semibold hover:bg-teal-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="!selectedId">
                    <i class="fas fa-link"></i>
                    ربط الطالب
                </button>
            </div>
        </form>
        <div class="mt-2">
            <p x-show="q.length > 0 && !results.length" class="text-xs text-amber-700 mt-1">لا توجد نتائج مطابقة.</p>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900">الطلاب المعيّنون</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-3">الطالب</th>
                        <th class="text-right px-4 py-3">آخر ظهور</th>
                        <th class="text-right px-4 py-3 w-40"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-900"><?php echo e($st->name); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($st->email); ?></p>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap"><?php echo e($st->last_login_at ? $st->last_login_at->diffForHumans() : '—'); ?></td>
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('admin.academic-supervision.supervisors.students.show', [$supervisor, $st])); ?>" class="text-teal-700 font-semibold hover:underline">عرض</a>
                                <form method="post" action="<?php echo e(route('admin.academic-supervision.supervisors.students.detach', [$supervisor, $st])); ?>" class="inline mr-2" onsubmit="return confirm('إلغاء ربط هذا الطالب؟');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-600 text-xs font-semibold hover:underline">إلغاء الربط</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="px-4 py-10 text-center text-gray-500">لا يوجد طلاب بعد.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100"><?php echo e($students->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\academic-supervision\show.blade.php ENDPATH**/ ?>