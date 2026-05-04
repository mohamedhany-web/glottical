

<?php $__env->startSection('title', 'تقويم الاستشارات'); ?>

<?php $__env->startPush('styles'); ?>
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css' rel='stylesheet' />
<style>
    .fc { direction: rtl; }
    .fc-toolbar { flex-direction: row-reverse; }
    .fc-button-group { flex-direction: row-reverse; }
    .fc-event { cursor: pointer; border-radius: 4px; padding: 2px 4px; }
    .fc-daygrid-event { white-space: normal; font-size: 0.85rem; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 dark:bg-slate-900 py-6">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm mb-6 p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">تقويم الاستشارات</h1>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">الجلسات المجدولة مع الطلاب بعد تأكيد الإدارة</p>
                </div>
                <div class="text-sm text-gray-600 dark:text-slate-300 flex items-center gap-2">
                    <i class="fas fa-comments text-emerald-500"></i>
                    <span>جلسات: <strong><?php echo e($stats['total'] ?? 0); ?></strong> — قادمة: <strong><?php echo e($stats['upcoming'] ?? 0); ?></strong></span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-3">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4 md:p-6">
                    <div id="calendar" class="calendar-container"></div>
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-slate-700 flex items-center gap-2 text-sm text-gray-600 dark:text-slate-300">
                        <span class="w-4 h-4 rounded bg-emerald-600 inline-block"></span>
                        <span>استشارة مدفوعة</span>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm p-4 md:p-6">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">القادمة</h3>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        <?php $__empty_1 = true; $__currentLoopData = $events->where('start_date', '>=', now())->take(12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <a href="<?php echo e($event->url ?? '#'); ?>" class="block p-3 rounded-lg border border-gray-200 dark:border-slate-600 hover:border-emerald-500 transition-colors">
                                <div class="text-sm font-bold text-gray-900 dark:text-white truncate"><?php echo e($event->title); ?></div>
                                <div class="text-xs text-gray-500 mt-1"><?php echo e($event->start_date->format('d/m/Y H:i')); ?></div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-sm text-gray-500 text-center py-6">لا توجد جلسات قادمة</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/ar.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'ar',
        direction: 'rtl',
        initialView: 'dayGridMonth',
        headerToolbar: {
            right: 'prev,next today',
            center: 'title',
            left: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: { today: 'اليوم', month: 'شهر', week: 'أسبوع', day: 'يوم' },
        events: {
            url: '<?php echo e(route("instructor.calendar.events")); ?>',
            failure: function() { alert('حدث خطأ في تحميل الأحداث'); }
        },
        eventClick: function(info) {
            if (info.event.url) {
                window.location.href = info.event.url;
                info.jsEvent.preventDefault();
            }
        },
        height: 'auto',
        contentHeight: 600,
        firstDay: 6,
        navLinks: true,
        dayMaxEvents: 4
    });
    calendar.render();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\instructor\calendar\index.blade.php ENDPATH**/ ?>