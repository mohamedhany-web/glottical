<?php $__env->startSection('title', 'لوحة الإدارة - Muallimx'); ?>
<?php $__env->startSection('page_title', 'لوحة الإدارة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">

    
    <div class="animate-fade-in">
        <h2 class="text-2xl font-heading font-bold text-navy-800">مرحباً، <?php echo e(auth()->user()->name); ?></h2>
        <p class="text-slate-500 text-sm mt-1">نظرة عامة على أداء المنصة اليوم — تظهر الأرقام والأقسام حسب صلاحيات دورك</p>
    </div>

    <?php
        $ds = $dashboardShow ?? [];
        $hasAnyDashboardWidget = collect($ds)->filter(fn ($v) => (bool) $v)->isNotEmpty();
    ?>
    <?php if(isset($dashboardShow) && ! $hasAnyDashboardWidget): ?>
        <div class="rounded-xl border border-amber-200 bg-amber-50/80 px-4 py-3 text-sm text-amber-900">
            <i class="fas fa-info-circle ml-1"></i>
            لا توجد بطاقات إحصائية لعرضها حالياً. اطلب من المسؤول إسناد الصلاحيات المناسبة لدورك (مثل الكورسات، الطلبات، الفواتير، …).
        </div>
    <?php endif; ?>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php if(!empty($ds['users_metric'])): ?>
        <?php $usersMetric = $metrics['users'] ?? null; $usersTrend = $usersMetric['trend'] ?? null; ?>
        <div class="stat-card animate-fade-in animate-fade-in-1" style="--before-bg: #6366f1;">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">إجمالي المستخدمين</p>
                    <p class="text-3xl font-heading font-bold text-slate-800"><?php echo e(number_format($usersMetric['total'] ?? 0)); ?></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-brand to-blue-600 shadow-lg shadow-brand/20">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-sm">
                <span class="text-slate-400">هذا الشهر:</span>
                <span class="font-semibold text-slate-700"><?php echo e(number_format($usersMetric['new_this_month'] ?? 0)); ?></span>
                <?php if($usersTrend): ?>
                    <?php $percent = $usersTrend['percent']; $positive = $percent >= 0; ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($positive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'); ?>">
                        <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($ds['students_metric'])): ?>
        <?php $studentsMetric = $metrics['students'] ?? null; $studentsTrend = $studentsMetric['trend'] ?? null; ?>
        <div class="stat-card animate-fade-in animate-fade-in-2">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">الطلاب</p>
                    <p class="text-3xl font-heading font-bold text-slate-800"><?php echo e(number_format($studentsMetric['total'] ?? 0)); ?></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-lg shadow-emerald-500/25">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-sm">
                <span class="text-slate-400">هذا الشهر:</span>
                <span class="font-semibold text-slate-700"><?php echo e(number_format($studentsMetric['new_this_month'] ?? 0)); ?></span>
                <?php if($studentsTrend): ?>
                    <?php $percent = $studentsTrend['percent']; $positive = $percent >= 0; ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($positive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'); ?>">
                        <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($ds['instructors_metric'])): ?>
        <?php $instructorsMetric = $metrics['instructors'] ?? null; $instructorsTrend = $instructorsMetric['trend'] ?? null; ?>
        <div class="stat-card animate-fade-in animate-fade-in-3">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">المدربين</p>
                    <p class="text-3xl font-heading font-bold text-slate-800"><?php echo e(number_format($instructorsMetric['total'] ?? 0)); ?></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-violet-500 to-violet-600 shadow-lg shadow-violet-500/25">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-sm">
                <span class="text-slate-400">هذا الشهر:</span>
                <span class="font-semibold text-slate-700"><?php echo e(number_format($instructorsMetric['new_this_month'] ?? 0)); ?></span>
                <?php if($instructorsTrend): ?>
                    <?php $percent = $instructorsTrend['percent']; $positive = $percent >= 0; ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($positive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'); ?>">
                        <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($ds['courses_metric'])): ?>
        <?php $coursesMetric = $metrics['courses'] ?? null; $coursesTrend = $coursesMetric['trend'] ?? null; ?>
        <div class="stat-card animate-fade-in animate-fade-in-4">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">الكورسات</p>
                    <p class="text-3xl font-heading font-bold text-slate-800"><?php echo e(number_format($coursesMetric['total'] ?? 0)); ?></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-amber-500 to-orange-500 shadow-lg shadow-amber-500/25">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-sm">
                <span class="text-slate-400">هذا الشهر:</span>
                <span class="font-semibold text-slate-700"><?php echo e(number_format($coursesMetric['new_this_month'] ?? 0)); ?></span>
                <?php if($coursesTrend): ?>
                    <?php $percent = $coursesTrend['percent']; $positive = $percent >= 0; ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($positive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'); ?>">
                        <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php if(!empty($ds['revenue_total'])): ?>
        <div class="stat-card animate-fade-in">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">إجمالي الإيرادات</p>
                    <p class="text-2xl font-heading font-bold text-slate-800"><?php echo e(number_format($stats['total_revenue'] ?? 0, 2)); ?> <span class="text-base font-normal text-slate-400">ج.م</span></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($ds['monthly_revenue'])): ?>
        <?php $revenueMetric = $metrics['monthly_revenue'] ?? null; $revenueTrend = $revenueMetric['trend'] ?? null; ?>
        <div class="stat-card animate-fade-in">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">إيرادات الشهر</p>
                    <p class="text-2xl font-heading font-bold text-slate-800"><?php echo e(number_format($revenueMetric['current'] ?? 0, 2)); ?> <span class="text-base font-normal text-slate-400">ج.م</span></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-sky-500 to-blue-600 shadow-lg shadow-sky-500/25">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <?php if($revenueTrend): ?>
                <?php $diff = $revenueTrend['difference']; $percent = $revenueTrend['percent']; $positive = $diff >= 0; ?>
                <div class="mt-3 flex items-center gap-2 text-sm">
                    <span class="font-semibold <?php echo e($positive ? 'text-emerald-600' : 'text-rose-500'); ?>"><?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($diff, 2)); ?> ج.م</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($positive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'); ?>">
                        <?php echo e($percent >= 0 ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                    </span>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if(!empty($ds['pending_invoices_metric'])): ?>
        <?php $pendingMetric = $metrics['pending_invoices'] ?? null; $pendingTrend = $pendingMetric['trend'] ?? null; ?>
        <div class="stat-card animate-fade-in">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">فواتير معلقة</p>
                    <p class="text-3xl font-heading font-bold text-slate-800"><?php echo e(number_format($pendingMetric['total'] ?? 0)); ?></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-amber-400 to-orange-500 shadow-lg shadow-amber-500/25">
                    <i class="fas fa-file-invoice"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-sm">
                <span class="text-slate-400">هذا الشهر:</span>
                <span class="font-semibold text-slate-700"><?php echo e(number_format($pendingMetric['new_this_month'] ?? 0)); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($ds['enrollments_metric'])): ?>
        <?php $enrollmentsMetric = $metrics['enrollments'] ?? null; $enrollmentsTrend = $enrollmentsMetric['trend'] ?? null; ?>
        <div class="stat-card animate-fade-in">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">التسجيلات النشطة</p>
                    <p class="text-3xl font-heading font-bold text-slate-800"><?php echo e(number_format($enrollmentsMetric['total'] ?? 0)); ?></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-fuchsia-500 to-purple-600 shadow-lg shadow-fuchsia-500/25">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-sm">
                <span class="text-slate-400">هذا الشهر:</span>
                <span class="font-semibold text-slate-700"><?php echo e(number_format($enrollmentsMetric['new_this_month'] ?? 0)); ?></span>
                <?php if($enrollmentsTrend): ?>
                    <?php $percent = $enrollmentsTrend['percent']; $positive = $percent >= 0; ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($positive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'); ?>">
                        <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    
    <?php if(!empty($ds['activity_feed']) || !empty($ds['exam_attempts'])): ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <?php if(!empty($ds['activity_feed'])): ?>
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-history text-indigo-500"></i> آخر النشاطات
                </h3>
            </div>
            <div>
                <?php if(isset($stats['recent_activities']) && $stats['recent_activities']->count() > 0): ?>
                    <?php $__currentLoopData = $stats['recent_activities']->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="list-row">
                            <div class="w-9 h-9 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-history text-indigo-500 text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-700 truncate"><?php echo e($activity->user->name ?? 'مستخدم محذوف'); ?></p>
                                <p class="text-xs text-slate-400"><?php echo e($activity->action); ?> &middot; <?php echo e($activity->created_at->diffForHumans()); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <div class="px-6 py-3 border-t border-slate-100">
                        <a href="<?php echo e(route('admin.activity-log')); ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                            عرض جميع النشاطات <i class="fas fa-arrow-left text-[10px]"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="py-12 text-center">
                        <i class="fas fa-history text-3xl text-slate-200 mb-2"></i>
                        <p class="text-sm text-slate-400">لا توجد أنشطة بعد</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($ds['exam_attempts'])): ?>
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-violet-500"></i> آخر محاولات الامتحانات
                </h3>
            </div>
            <div>
                <?php if(isset($stats['recent_exam_attempts']) && $stats['recent_exam_attempts']->count() > 0): ?>
                    <?php $__currentLoopData = $stats['recent_exam_attempts']->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="list-row">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-700"><?php echo e($attempt->student->name ?? 'طالب محذوف'); ?></p>
                                <p class="text-xs text-slate-400"><?php echo e($attempt->exam->title ?? 'امتحان محذوف'); ?></p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold flex-shrink-0
                                <?php echo e($attempt->score >= 80 ? 'bg-emerald-50 text-emerald-600' : ($attempt->score >= 60 ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600')); ?>">
                                <?php echo e($attempt->score); ?>%
                            </span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="py-12 text-center">
                        <i class="fas fa-clipboard-check text-3xl text-slate-200 mb-2"></i>
                        <p class="text-sm text-slate-400">لا توجد محاولات امتحانات بعد</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    
    <?php if((!empty($ds['recent_users']) && isset($recent_users)) || (!empty($ds['recent_courses']) && isset($recent_courses))): ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <?php if(!empty($ds['recent_users']) && isset($recent_users)): ?>
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-users text-sky-500"></i> آخر المستخدمين
                </h3>
                <a href="<?php echo e(route('admin.users.index')); ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                    عرض الكل <i class="fas fa-arrow-left text-[10px]"></i>
                </a>
            </div>
            <div>
                <?php $__currentLoopData = $recent_users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="list-row">
                    <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        <?php echo e(mb_substr($user->name, 0, 1)); ?>

                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate"><?php echo e($user->name); ?></p>
                        <p class="text-xs text-slate-400"><?php echo e($user->phone ?? $user->email); ?></p>
                    </div>
                    <div class="text-left flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                            <?php if($user->role === 'student'): ?> bg-emerald-50 text-emerald-600
                            <?php elseif($user->role === 'instructor'): ?> bg-violet-50 text-violet-600
                            <?php elseif($user->role === 'super_admin'): ?> bg-rose-50 text-rose-600
                            <?php else: ?> bg-slate-100 text-slate-500 <?php endif; ?>">
                            <?php if($user->role === 'student'): ?> طالب
                            <?php elseif($user->role === 'instructor'): ?> مدرب
                            <?php elseif($user->role === 'super_admin'): ?> مدير عام
                            <?php else: ?> غير محدد <?php endif; ?>
                        </span>
                        <p class="text-[11px] text-slate-300 mt-0.5"><?php echo e($user->created_at->diffForHumans()); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($ds['recent_courses']) && isset($recent_courses)): ?>
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-book text-amber-500"></i> آخر الكورسات
                </h3>
                <a href="<?php echo e(route('admin.advanced-courses.index')); ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                    عرض الكل <i class="fas fa-arrow-left text-[10px]"></i>
                </a>
            </div>
            <div>
                <?php $__empty_1 = true; $__currentLoopData = $recent_courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="list-row">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-book text-white text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate"><?php echo e($course->title); ?></p>
                        <p class="text-xs text-slate-400"><?php echo e($course->academicSubject->name ?? 'غير محدد'); ?></p>
                    </div>
                    <div class="text-left flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                            <?php if($course->is_active): ?> bg-emerald-50 text-emerald-600
                            <?php else: ?> bg-slate-100 text-slate-500 <?php endif; ?>">
                            <?php if($course->is_active): ?> نشط <?php else: ?> غير نشط <?php endif; ?>
                        </span>
                        <p class="text-[11px] text-slate-300 mt-0.5"><?php echo e($course->created_at->diffForHumans()); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="py-12 text-center">
                    <i class="fas fa-book text-3xl text-slate-200 mb-2"></i>
                    <p class="text-sm text-slate-400">لا توجد كورسات بعد</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    
    <?php if(!empty($salesSection) || !empty($hrSection)): ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <?php if(!empty($salesSection)): ?>
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-shopping-cart text-emerald-500"></i> قسم المبيعات
                </h3>
                <a href="<?php echo e(route('admin.orders.index')); ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                    عرض الكل <i class="fas fa-arrow-left text-[10px]"></i>
                </a>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                        <p class="text-xs text-slate-500 font-medium">طلبات معلقة</p>
                        <p class="text-xl font-heading font-bold text-slate-800"><?php echo e($salesSection['orders_pending']); ?></p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                        <p class="text-xs text-slate-500 font-medium">معتمدة هذا الشهر</p>
                        <p class="text-xl font-heading font-bold text-slate-800"><?php echo e($salesSection['orders_approved_month']); ?></p>
                    </div>
                    <div class="col-span-2 rounded-xl bg-emerald-50 p-3 border border-emerald-100">
                        <p class="text-xs text-emerald-600 font-medium">إيرادات الشهر (طلبات معتمدة)</p>
                        <p class="text-xl font-heading font-bold text-emerald-700"><?php echo e(number_format($salesSection['revenue_month'] ?? 0, 2)); ?> ج.م</p>
                    </div>
                </div>
                <p class="text-xs font-semibold text-slate-500 mb-2">آخر الطلبات</p>
                <?php $__empty_1 = true; $__currentLoopData = $salesSection['recent_orders'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="list-row">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate"><?php echo e($order->user->name ?? '—'); ?></p>
                        <p class="text-xs text-slate-400 truncate"><?php echo e($order->course->title ?? '—'); ?></p>
                    </div>
                    <div class="text-left flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                            <?php if($order->status === 'approved'): ?> bg-emerald-50 text-emerald-600
                            <?php elseif($order->status === 'pending'): ?> bg-amber-50 text-amber-600
                            <?php else: ?> bg-rose-50 text-rose-600 <?php endif; ?>">
                            <?php if($order->status === 'approved'): ?> معتمد
                            <?php elseif($order->status === 'pending'): ?> معلق
                            <?php else: ?> مرفوض <?php endif; ?>
                        </span>
                        <p class="text-sm font-bold text-slate-700 mt-0.5"><?php echo e(number_format($order->amount ?? 0, 0)); ?> ج.م</p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-slate-400 py-4 text-center">لا توجد طلبات حديثة</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($hrSection)): ?>
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-users-cog text-indigo-500"></i> قسم الموارد البشرية
                </h3>
                <a href="<?php echo e(route('admin.employees.index')); ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                    عرض الكل <i class="fas fa-arrow-left text-[10px]"></i>
                </a>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                        <p class="text-xs text-slate-500 font-medium">إجمالي الموظفين</p>
                        <p class="text-xl font-heading font-bold text-slate-800"><?php echo e($hrSection['employees_total']); ?></p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                        <p class="text-xs text-slate-500 font-medium">طلبات إجازة معلقة</p>
                        <p class="text-xl font-heading font-bold text-slate-800"><?php echo e($hrSection['leaves_pending']); ?></p>
                    </div>
                    <div class="col-span-2 rounded-xl bg-indigo-50 p-3 border border-indigo-100">
                        <p class="text-xs text-indigo-600 font-medium">إجازات معتمدة هذا الشهر</p>
                        <p class="text-xl font-heading font-bold text-indigo-700"><?php echo e($hrSection['leaves_approved_month']); ?></p>
                    </div>
                </div>
                <p class="text-xs font-semibold text-slate-500 mb-2">آخر طلبات الإجازة</p>
                <?php $__empty_1 = true; $__currentLoopData = $hrSection['recent_leaves'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="list-row">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate"><?php echo e($leave->employee->name ?? '—'); ?></p>
                        <p class="text-xs text-slate-400"><?php echo e($leave->days ?? 0); ?> يوم · <?php echo e(optional($leave->employee->employeeJob)->name ?? '—'); ?></p>
                    </div>
                    <div class="text-left flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                            <?php if($leave->status === 'approved'): ?> bg-emerald-50 text-emerald-600
                            <?php elseif($leave->status === 'pending'): ?> bg-amber-50 text-amber-600
                            <?php else: ?> bg-rose-50 text-rose-600 <?php endif; ?>">
                            <?php if($leave->status === 'approved'): ?> معتمد
                            <?php elseif($leave->status === 'pending'): ?> معلق
                            <?php else: ?> مرفوض <?php endif; ?>
                        </span>
                        <p class="text-[11px] text-slate-300 mt-0.5"><?php echo e($leave->created_at->diffForHumans()); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-slate-400 py-4 text-center">لا توجد طلبات إجازة حديثة</p>
                <?php endif; ?>
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-xs font-semibold text-slate-500 mb-2">آخر الموظفين المضافة</p>
                    <?php $__empty_1 = true; $__currentLoopData = $hrSection['recent_employees'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="list-row">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-700 truncate"><?php echo e($emp->name); ?></p>
                            <p class="text-xs text-slate-400"><?php echo e(optional($emp->employeeJob)->name ?? 'موظف'); ?></p>
                        </div>
                        <p class="text-[11px] text-slate-300"><?php echo e($emp->hire_date ? $emp->hire_date->diffForHumans() : $emp->created_at->diffForHumans()); ?></p>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-sm text-slate-400 py-2 text-center">لا يوجد موظفون</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    
    <?php if((!empty($ds['invoices_panel']) && isset($pending_invoices) && $pending_invoices->count() > 0) || (!empty($ds['payments_panel']) && isset($recent_payments) && $recent_payments->count() > 0)): ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <?php if(!empty($ds['invoices_panel']) && isset($pending_invoices) && $pending_invoices->count() > 0): ?>
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-file-invoice text-amber-500"></i> الفواتير المعلقة
                </h3>
                <a href="#" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">عرض الكل <i class="fas fa-arrow-left text-[10px]"></i></a>
            </div>
            <div>
                <?php $__currentLoopData = $pending_invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="list-row">
                    <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-file-invoice text-amber-500 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate"><?php echo e($invoice->invoice_number ?? 'غير محدد'); ?></p>
                        <p class="text-xs text-slate-400"><?php echo e($invoice->user->name ?? 'غير محدد'); ?></p>
                    </div>
                    <div class="text-left flex-shrink-0">
                        <p class="text-sm font-bold text-slate-700"><?php echo e(number_format($invoice->total_amount ?? 0, 2)); ?> ج.م</p>
                        <p class="text-[11px] text-slate-300"><?php echo e($invoice->created_at->diffForHumans()); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($ds['payments_panel']) && isset($recent_payments) && $recent_payments->count() > 0): ?>
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-money-bill-wave text-emerald-500"></i> المدفوعات الأخيرة
                </h3>
                <a href="#" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">عرض الكل <i class="fas fa-arrow-left text-[10px]"></i></a>
            </div>
            <div>
                <?php $__currentLoopData = $recent_payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="list-row">
                    <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate"><?php echo e($payment->payment_number ?? 'غير محدد'); ?></p>
                        <p class="text-xs text-slate-400"><?php echo e($payment->user->name ?? 'غير محدد'); ?></p>
                    </div>
                    <div class="text-left flex-shrink-0">
                        <p class="text-sm font-bold text-emerald-600"><?php echo e(number_format($payment->amount ?? 0, 2)); ?> ج.م</p>
                        <p class="text-[11px] text-slate-300"><?php echo e($payment->paid_at ? $payment->paid_at->diffForHumans() : $payment->created_at->diffForHumans()); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    
    <?php if(!empty($subscriptionPackages)): ?>
    <div class="section-card animate-fade-in">
        <div class="section-card-header">
            <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-crown text-amber-500"></i> العناصر المدفوعة — الاشتراكات
            </h3>
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                    عرض كل الاشتراكات <i class="fas fa-arrow-left text-[10px]"></i>
                </a>
                <a href="<?php echo e(route('admin.subscriptions.create')); ?>" class="text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded-lg transition-colors">
                    إضافة اشتراك
                </a>
            </div>
        </div>
        <div class="p-5">
            <?php if($subscriptionPackages->count() > 0): ?>
            <p class="text-xs text-slate-500 mb-4">الباقات المتاحة وجميع المشتركين في كل باقة. يمكنك الدخول على أي مشترك لإدارة اشتراكه ومراجعة بياناته بالكامل.</p>
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php $__currentLoopData = $subscriptionPackages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 overflow-hidden">
                    <div class="px-4 py-3 bg-white border-b border-slate-200 flex items-center justify-between">
                        <h4 class="text-sm font-bold text-slate-800"><?php echo e($package['plan_name']); ?></h4>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                            <i class="fas fa-users text-[10px]"></i>
                            <?php echo e($package['count']); ?> مشترك
                        </span>
                    </div>
                    <div class="p-3 max-h-64 overflow-y-auto space-y-1.5">
                        <?php $__empty_1 = true; $__currentLoopData = $package['subscriptions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-100 bg-white px-3 py-2 hover:border-indigo-200 transition-colors">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-700 truncate"><?php echo e($sub->user->name ?? '—'); ?></p>
                                <p class="text-[11px] text-slate-400"><?php echo e($sub->user->phone ?? $sub->user->email ?? '—'); ?></p>
                            </div>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <a href="<?php echo e(route('admin.subscriptions.show', $sub)); ?>" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-sky-100 text-sky-600 hover:bg-sky-200 transition-colors text-xs" title="تفاصيل الاشتراك والتحكم">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if($sub->user): ?>
                                <a href="<?php echo e(route('admin.users.show', $sub->user->id)); ?>" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors text-xs" title="بيانات المستخدم">
                                    <i class="fas fa-user"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-xs text-slate-400 py-2 text-center">لا مشتركين في هذه الباقة</p>
                        <?php endif; ?>
                    </div>
                    <?php if($package['count'] > $package['subscriptions']->count()): ?>
                    <div class="px-4 py-2 border-t border-slate-100 bg-slate-50/80 text-center">
                        <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                            عرض كل الاشتراكات (<?php echo e($package['count']); ?> في هذه الباقة) <i class="fas fa-arrow-left text-[10px]"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
            <div class="py-10 text-center rounded-xl bg-slate-50 border border-slate-100">
                <i class="fas fa-layer-group text-3xl text-slate-300 mb-2"></i>
                <p class="text-sm text-slate-500">لا توجد اشتراكات أو باقات حالياً.</p>
                <a href="<?php echo e(route('admin.subscriptions.create')); ?>" class="inline-flex items-center gap-2 mt-3 text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                    <i class="fas fa-plus"></i> إضافة اشتراك جديد
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="section-card animate-fade-in">
        <div class="section-card-header">
            <div>
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-bolt text-amber-500"></i> إجراءات سريعة
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">روابط مباشرة للمهام اليومية</p>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                <?php $__currentLoopData = ($quickActions ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($action['route']); ?>" class="group flex flex-col items-center gap-3 p-5 rounded-xl bg-slate-50/80 hover:bg-white border border-transparent hover:border-slate-200 hover:shadow-lg transition-all duration-200">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br <?php echo e($action['icon_background']); ?> flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                            <i class="<?php echo e($action['icon']); ?> text-white text-lg"></i>
                        </div>
                        <p class="text-xs font-semibold text-slate-600 text-center leading-relaxed"><?php echo e($action['title']); ?></p>
                        <?php $actionCount = $action['count'] ?? 0; ?>
                        <p class="text-2xl font-heading font-bold text-slate-800"><?php echo e(number_format($actionCount)); ?></p>
                        <?php if(!empty($action['meta'])): ?>
                            <p class="text-[11px] text-slate-400"><?php echo e($action['meta']); ?></p>
                        <?php endif; ?>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if(empty($quickActions)): ?>
                    <div class="col-span-full text-center py-8 text-slate-400 text-sm">
                        لا توجد مهام عاجلة حالياً
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\dashboard.blade.php ENDPATH**/ ?>