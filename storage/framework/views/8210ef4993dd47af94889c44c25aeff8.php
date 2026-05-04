<?php $__env->startSection('title', 'لوحة تحكم الموظف'); ?>
<?php $__env->startSection('header', 'لوحة تحكم الموظف'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .dashboard-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);
        border-radius: 20px;
        padding: 24px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border: 2px solid rgba(44, 169, 189, 0.2);
        box-shadow: 0 4px 16px rgba(44, 169, 189, 0.1);
    }

    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, rgba(44, 169, 189, 0.15) 0%, transparent 100%);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }

    .dashboard-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(44, 169, 189, 0.2);
        border-color: rgba(44, 169, 189, 0.4);
    }

    .welcome-section {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);
        border-radius: 20px;
        padding: 32px 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(44, 169, 189, 0.1);
        border: 2px solid rgba(44, 169, 189, 0.2);
    }

    .welcome-section::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, rgba(44, 169, 189, 0.15) 0%, transparent 100%);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }

    .task-card {
        transition: all 0.2s;
        border: 1px solid rgba(226, 232, 240, 0.8);
    }

    .task-card:hover {
        transform: translateX(-4px);
        background: linear-gradient(to right, rgba(44, 169, 189, 0.05), transparent);
        border-color: rgba(44, 169, 189, 0.3);
        box-shadow: 0 4px 12px rgba(44, 169, 189, 0.1);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- ترحيب شخصي -->
    <div class="welcome-section dashboard-card relative overflow-hidden">
        <div class="relative z-10">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-black mb-2 text-gray-900">مرحباً، <?php echo e($user->name); ?></h2>
                    <p class="text-gray-600 text-base sm:text-lg font-medium">إليك نظرة عامة على مهامك ونشاطك اليوم</p>
                    <?php if($user->employeeJob): ?>
                        <p class="text-gray-500 text-sm mt-2 flex items-center gap-2">
                            <i class="fas fa-briefcase"></i>
                            <span><?php echo e($user->employeeJob->name); ?></span>
                            <?php if($user->employee_code): ?>
                                <span class="mr-2">(<?php echo e($user->employee_code); ?>)</span>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-user-tie text-4xl"></i>
                </div>
            </div>
        </div>
    </div>

    <?php if(!empty($jobInsights)): ?>
    <div class="rounded-2xl border-2 border-indigo-200/60 bg-gradient-to-l from-indigo-50/90 to-white p-5 sm:p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <h3 class="text-lg font-black text-gray-900 flex items-center gap-2">
                <i class="fas fa-chart-pie text-indigo-600"></i>
                <?php echo e($jobInsights['label']); ?>

            </h3>
            <?php
                $deskRoute = match ($jobCode ?? '') {
                    'accountant' => route('employee.accountant-desk.index'),
                    'sales' => route('employee.sales.desk'),
                    'hr' => route('employee.hr-desk.index'),
                    'general_supervision' => route('employee.supervision-desk.index'),
                    'supervisor' => route('employee.supervision-desk.index'),
                    default => null,
                };
            ?>
            <?php if($deskRoute): ?>
                <a href="<?php echo e($deskRoute); ?>" class="inline-flex items-center gap-2 text-sm font-bold text-indigo-700 hover:text-indigo-900">
                    فتح لوحة الوظيفة التفصيلية <i class="fas fa-arrow-left text-xs"></i>
                </a>
            <?php endif; ?>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__currentLoopData = $jobInsights['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl bg-white border border-gray-200 px-4 py-3 flex items-center justify-between gap-3">
                    <span class="text-sm font-medium text-gray-600"><?php echo e($item['text']); ?></span>
                    <span class="text-2xl font-black tabular-nums
                        <?php if(($item['color'] ?? '') === 'amber'): ?> text-amber-700
                        <?php elseif(($item['color'] ?? '') === 'emerald'): ?> text-emerald-700
                        <?php elseif(($item['color'] ?? '') === 'sky'): ?> text-sky-700
                        <?php elseif(($item['color'] ?? '') === 'indigo'): ?> text-indigo-700
                        <?php elseif(($item['color'] ?? '') === 'rose'): ?> text-rose-700
                        <?php elseif(($item['color'] ?? '') === 'blue'): ?> text-blue-700
                        <?php elseif(($item['color'] ?? '') === 'red'): ?> text-red-700
                        <?php else: ?> text-gray-900 <?php endif; ?>"><?php echo e(number_format($item['value'])); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6">
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي المهام</p>
                        <p class="text-3xl font-black text-gray-900"><?php echo e($stats['total_tasks']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-tasks text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-yellow-200/50 hover:border-yellow-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">معلقة</p>
                        <p class="text-3xl font-black text-yellow-700"><?php echo e($stats['pending_tasks']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">قيد التنفيذ</p>
                        <p class="text-3xl font-black text-blue-700"><?php echo e($stats['in_progress_tasks']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-spinner text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-green-200/50 hover:border-green-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">مكتملة</p>
                        <p class="text-3xl font-black text-green-700"><?php echo e($stats['completed_tasks']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-red-200/50 hover:border-red-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(254, 242, 242, 0.95) 50%, rgba(254, 226, 226, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">متأخرة</p>
                        <p class="text-3xl font-black text-red-700"><?php echo e($stats['overdue_tasks']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- المهام الأخيرة -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-tasks text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-gray-900">المهام الأخيرة</h3>
                    <p class="text-xs text-gray-600 font-medium mt-1">آخر 10 مهام مخصصة لك</p>
                </div>
            </div>
            <a href="<?php echo e(route('employee.tasks.index')); ?>" 
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-list mr-2"></i>
                عرض جميع المهام
            </a>
        </div>

        <div class="divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="task-card px-6 py-4 hover:bg-gray-50 transition-all">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-2">
                            <h4 class="text-base font-bold text-gray-900"><?php echo e($task->title); ?></h4>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                <?php if($task->priority === 'urgent'): ?> bg-red-100 text-red-800
                                <?php elseif($task->priority === 'high'): ?> bg-orange-100 text-orange-800
                                <?php elseif($task->priority === 'medium'): ?> bg-yellow-100 text-yellow-800
                                <?php else: ?> bg-gray-100 text-gray-800
                                <?php endif; ?>">
                                <?php if($task->priority === 'urgent'): ?> عاجل
                                <?php elseif($task->priority === 'high'): ?> عالي
                                <?php elseif($task->priority === 'medium'): ?> متوسط
                                <?php else: ?> منخفض
                                <?php endif; ?>
                            </span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                <?php if($task->status === 'completed'): ?> bg-green-100 text-green-800
                                <?php elseif($task->status === 'in_progress'): ?> bg-blue-100 text-blue-800
                                <?php elseif($task->status === 'pending'): ?> bg-yellow-100 text-yellow-800
                                <?php else: ?> bg-gray-100 text-gray-800
                                <?php endif; ?>">
                                <?php if($task->status === 'completed'): ?> مكتملة
                                <?php elseif($task->status === 'in_progress'): ?> قيد التنفيذ
                                <?php elseif($task->status === 'pending'): ?> معلقة
                                <?php else: ?> <?php echo e($task->status); ?>

                                <?php endif; ?>
                            </span>
                        </div>
                        <?php if($task->description): ?>
                            <p class="text-sm text-gray-600 mb-2 line-clamp-2"><?php echo e(Str::limit($task->description, 100)); ?></p>
                        <?php endif; ?>
                        <div class="flex items-center gap-4 text-xs text-gray-500 flex-wrap">
                            <?php if($task->assigner): ?>
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-user-tie"></i>
                                    <?php echo e($task->assigner->name); ?>

                                </span>
                            <?php endif; ?>
                            <?php if($task->deadline): ?>
                                <span class="flex items-center gap-1 <?php echo e($task->deadline < now() && !in_array($task->status, ['completed', 'cancelled']) ? 'text-red-600 font-semibold' : ''); ?>">
                                    <i class="fas fa-calendar-alt"></i>
                                    <?php echo e($task->deadline->format('Y-m-d')); ?>

                                </span>
                            <?php endif; ?>
                            <?php if($task->progress !== null): ?>
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-chart-line"></i>
                                    <?php echo e($task->progress); ?>%
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <a href="<?php echo e(route('employee.tasks.show', $task)); ?>" 
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg font-medium transition-colors whitespace-nowrap">
                        <i class="fas fa-eye mr-2"></i>عرض
                    </a>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="px-6 py-16 text-center">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-tasks text-3xl text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 text-lg mb-1">لا توجد مهام حالياً</p>
                        <p class="text-sm text-gray-600 font-medium">سيتم إشعارك عند تعيين مهام جديدة لك</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- إجراءات سريعة حسب صلاحيات الوظيفة -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if($user->employeeCan('tasks')): ?>
        <a href="<?php echo e(route('employee.tasks.index')); ?>"
           class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-blue-300 hover:shadow-md transition-all duration-200">
            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 shadow-sm mb-3">
                <i class="fas fa-tasks text-lg"></i>
            </div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">مهامي</h4>
            <p class="text-xs text-gray-600 font-medium leading-relaxed">متابعة المهام المسندة إليك</p>
        </a>
        <?php endif; ?>

        <?php if($user->employeeCan('desk_accountant')): ?>
        <a href="<?php echo e(route('employee.accountant-desk.index')); ?>" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-amber-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700 shadow-sm mb-3"><i class="fas fa-calculator text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">لوحة المحاسب</h4>
            <p class="text-xs text-gray-600">طلبات الدفع، الاتفاقيات، دفعات الرواتب</p>
        </a>
        <?php endif; ?>
        <?php if($user->employeeCan('sales_desk')): ?>
        <a href="<?php echo e(route('employee.sales.desk')); ?>" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-emerald-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 shadow-sm mb-3"><i class="fas fa-shopping-cart text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">لوحة المبيعات</h4>
            <p class="text-xs text-gray-600">طلبات الكورسات والإيرادات</p>
        </a>
        <?php endif; ?>
        <?php if($user->employeeCan('hr_desk')): ?>
        <a href="<?php echo e(route('employee.hr-desk.index')); ?>" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-rose-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-rose-100 flex items-center justify-center text-rose-700 shadow-sm mb-3"><i class="fas fa-users text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">لوحة الموارد البشرية</h4>
            <p class="text-xs text-gray-600">دليل الموظفين، مراجعة الإجازات، وسجل HR</p>
        </a>
        <?php endif; ?>
        <?php if($user->employeeCan('supervision_desk')): ?>
        <a href="<?php echo e(route('employee.supervision-desk.index')); ?>" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-indigo-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-700 shadow-sm mb-3"><i class="fas fa-clipboard-check text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">لوحة الإشراف</h4>
            <p class="text-xs text-gray-600">مهام الفريق والمتأخرات</p>
        </a>
        <?php endif; ?>
        <?php if($user->employeeCan('academic_supervision_desk')): ?>
        <a href="<?php echo e(route('employee.academic-supervision.index')); ?>" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-teal-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-teal-100 flex items-center justify-center text-teal-700 shadow-sm mb-3"><i class="fas fa-user-graduate text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">الإشراف الأكاديمي</h4>
            <p class="text-xs text-gray-600">متابعة الطلاب المعيّنين لك</p>
        </a>
        <?php endif; ?>

        <?php if($user->employeeCan('leaves')): ?>
        <a href="<?php echo e(route('employee.leaves.index')); ?>" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-cyan-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-cyan-100 flex items-center justify-center text-cyan-700 shadow-sm mb-3"><i class="fas fa-umbrella-beach text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">إجازاتي</h4>
            <p class="text-xs text-gray-600">طلبات الإجازة والمتابعة</p>
        </a>
        <?php endif; ?>
        <?php if($user->employeeCan('accounting')): ?>
        <a href="<?php echo e(route('employee.accounting.index')); ?>" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-slate-400 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700 shadow-sm mb-3"><i class="fas fa-wallet text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">محاسبتي الشخصية</h4>
            <p class="text-xs text-gray-600">راتبك وخصوماتك وحسابك البنكي</p>
        </a>
        <?php endif; ?>
        <?php if($user->employeeCan('agreements')): ?>
        <a href="<?php echo e(route('employee.agreements.index')); ?>" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-violet-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center text-violet-700 shadow-sm mb-3"><i class="fas fa-file-contract text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">اتفاقيات العمل</h4>
            <p class="text-xs text-gray-600">عقودك مع المنصة</p>
        </a>
        <?php endif; ?>
        <?php if($user->employeeCan('reports')): ?>
        <a href="<?php echo e(route('employee.reports')); ?>" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-purple-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-purple-700 shadow-sm mb-3"><i class="fas fa-chart-line text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">تقاريري</h4>
            <p class="text-xs text-gray-600">أداء المهام والإجازات</p>
        </a>
        <?php endif; ?>
        <?php if($user->employeeCan('calendar')): ?>
        <a href="<?php echo e(route('employee.calendar')); ?>" class="group rounded-xl border border-gray-200 bg-white p-6 hover:border-orange-300 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center text-orange-700 shadow-sm mb-3"><i class="fas fa-calendar-alt text-lg"></i></div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">التقويم</h4>
            <p class="text-xs text-gray-600">المواعيد والمهام</p>
        </a>
        <?php endif; ?>

        <?php if($user->employeeJob): ?>
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shadow-sm mb-3">
                <i class="fas fa-briefcase text-lg"></i>
            </div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">وظيفتك</h4>
            <p class="text-xs text-gray-600 font-medium leading-relaxed">
                <strong><?php echo e($user->employeeJob->name); ?></strong>
                <?php if($user->employee_code): ?><br>الرمز: <?php echo e($user->employee_code); ?><?php endif; ?>
            </p>
        </div>
        <?php endif; ?>

        <?php if($user->employeeCan('tasks')): ?>
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600 shadow-sm mb-3">
                <i class="fas fa-chart-bar text-lg"></i>
            </div>
            <h4 class="text-sm font-bold text-gray-900 mb-2">معدل إنجاز المهام</h4>
            <p class="text-xs text-gray-600 font-medium leading-relaxed">
                <strong class="text-green-600"><?php echo e($stats['total_tasks'] > 0 ? round(($stats['completed_tasks'] / $stats['total_tasks']) * 100, 1) : 0); ?>%</strong>
            </p>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\dashboard.blade.php ENDPATH**/ ?>