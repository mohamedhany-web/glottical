<?php $__env->startSection('title', ($pageTitle ?? 'إدارة المستخدمين') . ' - ' . config('app.name', 'Muallimx')); ?>
<?php $__env->startSection('header', $pageTitle ?? 'إدارة المستخدمين'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .user-card {
        transition: all 0.2s ease;
        background: linear-gradient(to bottom, #ffffff 0%, #f8fafc 100%);
        border: 1px solid rgba(100, 116, 139, 0.2);
    }

    .user-card:hover {
        border-color: rgba(59, 130, 246, 0.3);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .table-row {
        transition: background-color 0.15s ease;
    }

    .table-row:hover {
        background: rgba(59, 130, 246, 0.05);
    }

    .avatar-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    /* شكل خاص لصفحة إدارة الطلاب ليطابق Dashboard */
    .students-dashboard-theme .hero-title {
        color: #1e293b;
        font-weight: 800;
    }
    .students-dashboard-theme .hero-subtitle {
        color: #64748b;
    }
    .students-dashboard-theme .students-hero {
        background: transparent;
        border: 0;
        box-shadow: none;
        padding: 0;
    }
    .students-dashboard-theme .students-card {
        background: #fff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 16px;
        overflow: hidden;
    }
    .students-dashboard-theme .students-card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(241, 245, 249, 0.9);
        background: rgba(248, 250, 252, 0.4);
    }
    .dark .students-dashboard-theme .students-card {
        background: #1e293b;
        border-color: #334155;
    }
    .dark .students-dashboard-theme .students-card-header {
        background: rgba(30, 41, 59, 0.8);
        border-bottom-color: #334155;
    }
    .dark .students-dashboard-theme .hero-title {
        color: #f1f5f9;
    }
    .dark .students-dashboard-theme .hero-subtitle {
        color: #cbd5e1;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    // التأكد من وجود المتغيرات
    $stats = $stats ?? [];
    $trends = $trends ?? [];
    $users = $users ?? collect();
    $recentUsers = $recentUsers ?? collect();
    $recentlyActiveUsers = $recentlyActiveUsers ?? collect();
    $usersByRole = $usersByRole ?? collect();
    $usersByMonth = $usersByMonth ?? collect();
    $pageMode = $pageMode ?? 'users';
    $pageTitle = $pageTitle ?? 'إدارة المستخدمين';
    $pageDescription = $pageDescription ?? 'متابعة الحسابات، الصلاحيات، وحالة النشاط عبر المنصة';
    $indexRoute = $indexRoute ?? 'admin.users.index';
    
    $statsCards = [
        [
            'label' => 'إجمالي المستخدمين',
            'value' => number_format($stats['total'] ?? 0),
            'icon' => 'fas fa-users',
            'color' => 'blue',
            'description' => 'كل المستخدمين المسجلين',
            'new_this_month' => $stats['new_this_month'] ?? 0,
            'trend' => $trends['users'] ?? null,
        ],
        [
            'label' => 'المستخدمون النشطون',
            'value' => number_format($stats['active'] ?? 0),
            'icon' => 'fas fa-user-check',
            'color' => 'emerald',
            'description' => 'حسابات نشطة',
        ],
        [
            'label' => 'المدرسون',
            'value' => number_format($stats['teachers'] ?? 0),
            'icon' => 'fas fa-chalkboard-teacher',
            'color' => 'indigo',
            'description' => 'مدربون مسجلون',
            'new_this_month' => $stats['new_teachers_this_month'] ?? 0,
            'trend' => $trends['teachers'] ?? null,
        ],
        [
            'label' => 'الطلاب',
            'value' => number_format($stats['students'] ?? 0),
            'icon' => 'fas fa-user-graduate',
            'color' => 'purple',
            'description' => 'طلاب مسجلون',
            'new_this_month' => $stats['new_students_this_month'] ?? 0,
            'trend' => $trends['students'] ?? null,
        ],
    ];

    $roles = [
        'super_admin' => ['label' => 'مدير عام', 'badge' => 'bg-rose-100 text-rose-700 border border-rose-200'],
        'admin' => ['label' => 'إداري', 'badge' => 'bg-rose-100 text-rose-700 border border-rose-200'],
        'instructor' => ['label' => 'مدرب', 'badge' => 'bg-sky-100 text-sky-700 border border-sky-200'],
        'teacher' => ['label' => 'مدرس', 'badge' => 'bg-sky-100 text-sky-700 border border-sky-200'],
        'student' => ['label' => __('admin.student_role_label'), 'badge' => 'bg-emerald-100 text-emerald-700 border border-emerald-200'],
        'parent' => ['label' => 'ولي أمر', 'badge' => 'bg-indigo-100 text-indigo-700 border border-indigo-200'],
        'employee' => ['label' => 'موظف', 'badge' => 'bg-amber-100 text-amber-700 border border-amber-200']
    ];
    
    $colorConfigs = [
        'blue' => [
            'bg' => 'linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%)',
            'border' => 'border-blue-200/50 hover:border-blue-300/70',
            'text' => 'text-blue-800/80',
            'value' => 'from-blue-700 via-blue-600 to-sky-600',
            'icon' => 'from-blue-500 via-blue-600 to-sky-600',
            'iconShadow' => 'rgba(59, 130, 246, 0.4)',
            'hover' => 'from-blue-100/60 via-sky-100/40 to-blue-50/30',
        ],
        'emerald' => [
            'bg' => 'linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(236, 253, 245, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%)',
            'border' => 'border-emerald-200/50 hover:border-emerald-300/70',
            'text' => 'text-emerald-800/80',
            'value' => 'from-emerald-700 via-green-600 to-teal-600',
            'icon' => 'from-emerald-500 via-green-500 to-teal-600',
            'iconShadow' => 'rgba(16, 185, 129, 0.4)',
            'hover' => 'from-emerald-100/60 via-green-100/40 to-teal-50/30',
        ],
        'indigo' => [
            'bg' => 'linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(238, 242, 255, 0.95) 50%, rgba(224, 231, 255, 0.9) 100%)',
            'border' => 'border-indigo-200/50 hover:border-indigo-300/70',
            'text' => 'text-indigo-800/80',
            'value' => 'from-indigo-700 via-purple-600 to-violet-600',
            'icon' => 'from-indigo-500 via-purple-500 to-violet-600',
            'iconShadow' => 'rgba(99, 102, 241, 0.4)',
            'hover' => 'from-indigo-100/60 via-purple-100/40 to-violet-50/30',
        ],
        'purple' => [
            'bg' => 'linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(250, 245, 255, 0.95) 50%, rgba(243, 232, 255, 0.9) 100%)',
            'border' => 'border-purple-200/50 hover:border-purple-300/70',
            'text' => 'text-purple-800/80',
            'value' => 'from-purple-700 via-purple-600 to-violet-600',
            'icon' => 'from-purple-500 via-purple-500 to-violet-600',
            'iconShadow' => 'rgba(168, 85, 247, 0.4)',
            'hover' => 'from-purple-100/60 via-purple-100/40 to-violet-50/30',
        ],
    ];
?>

<div class="space-y-8 <?php echo e($pageMode === 'students' ? 'students-dashboard-theme' : ''); ?>">
    <?php if(request('created') == '1'): ?>
        <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 px-5 py-4 text-emerald-800 dark:text-emerald-200 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-600"></i>
            تم إنشاء المستخدم بنجاح.
        </div>
    <?php endif; ?>
    <?php if(session('success') || request('updated') == '1'): ?>
        <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 px-5 py-4 text-emerald-800 dark:text-emerald-200 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <?php echo e(session('success', 'تم التعديل بنجاح')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('warning')): ?>
        <div class="rounded-2xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 px-5 py-4 text-amber-800 dark:text-amber-200 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-exclamation-triangle text-amber-600"></i>
            <?php echo e(session('warning')); ?>

        </div>
    <?php endif; ?>
    <!-- الهيدر المحسن -->
    <div class="<?php echo e($pageMode === 'students' ? 'students-hero animate-fade-in' : 'bg-gradient-to-r from-slate-50 to-white dark:from-slate-800 dark:to-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-lg'); ?>">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md <?php echo e($pageMode === 'students' ? 'shadow-blue-500/25' : ''); ?>">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl mb-1 <?php echo e($pageMode === 'students' ? 'hero-title font-heading' : 'font-black text-slate-900 dark:text-slate-100'); ?>"><?php echo e($pageTitle); ?></h1>
                    <p class="text-sm sm:text-base font-medium <?php echo e($pageMode === 'students' ? 'hero-subtitle' : 'text-slate-600 dark:text-slate-300'); ?>"><?php echo e($pageDescription); ?></p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.users.create')); ?>" 
               class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                <i class="fas fa-user-plus"></i>
                <span><?php echo e($pageMode === 'students' ? 'إضافة حساب طالب جديد' : 'إضافة مستخدم جديد'); ?></span>
            </a>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <?php $__currentLoopData = $statsCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $config = $colorConfigs[$stat['color']]; ?>
            <div class="rounded-2xl p-5 sm:p-6 relative overflow-hidden border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-md hover:shadow-lg transition-all duration-200 w-full">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2"><?php echo e($stat['label']); ?></p>
                        <p class="text-4xl sm:text-3xl font-black text-slate-900 dark:text-slate-100"><?php echo e($stat['value']); ?></p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md flex-shrink-0 mr-3 sm:mr-0">
                        <i class="<?php echo e($stat['icon']); ?> text-white text-xl"></i>
                    </div>
                </div>
                <?php if(isset($stat['new_this_month'])): ?>
                    <p class="text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">
                        <?php echo e($stat['label'] == 'إجمالي المستخدمين' ? 'مستخدمون' : ($stat['label'] == 'المدرسون' ? 'مدربون' : 'طلاب')); ?> جدد هذا الشهر: 
                        <span class="font-bold text-blue-600"><?php echo e(number_format($stat['new_this_month'])); ?></span>
                    </p>
                <?php else: ?>
                    <p class="text-xs font-medium text-slate-600 dark:text-slate-400 mb-2"><?php echo e($stat['description']); ?></p>
                <?php endif; ?>
                <?php if(isset($stat['trend']) && $stat['trend']): ?>
                    <?php
                        $diff = (int) round($stat['trend']['difference']);
                        $percent = $stat['trend']['percent'];
                        $positive = $diff >= 0;
                    ?>
                    <div class="mt-2 flex items-center gap-2 text-sm flex-wrap">
                        <span class="font-bold <?php echo e($positive ? 'text-emerald-600' : 'text-rose-600'); ?>">
                            <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($diff)); ?>

                        </span>
                        <span class="text-slate-600 dark:text-slate-400">عن الشهر الماضي</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold <?php echo e($positive ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200'); ?>">
                            <?php echo e($percent >= 0 ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($pageMode === 'students'): ?>
    <section class="students-card">
        <div class="students-card-header">
            <h3 class="text-base font-bold text-slate-900">التحكم والرقابة المدفوعة</h3>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-3">
            <a href="<?php echo e(route('admin.students-control.paid-features')); ?>" class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition-colors">
                <i class="fas fa-layer-group"></i>
                إدارة المزايا المدفوعة
            </a>
            <a href="<?php echo e(route('admin.students-control.consumption')); ?>" class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-colors">
                <i class="fas fa-chart-pie"></i>
                استهلاك المستخدمين
            </a>
            <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 font-semibold hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <i class="fas fa-calendar-check"></i>
                الاشتراكات
            </a>
        </div>
    </section>
    <?php endif; ?>

    <!-- البحث والفلترة -->
    <section class="<?php echo e($pageMode === 'students' ? 'students-card' : 'rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-lg overflow-hidden'); ?>">
        <div class="<?php echo e($pageMode === 'students' ? 'students-card-header' : 'px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40'); ?>">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-filter text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-slate-100">البحث والفلترة</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 font-medium mt-1">ابحث وفلتر المستخدمين حسب الدور والحالة</p>
                </div>
            </div>
        </div>
        <div class="px-6 py-5">
            <form method="GET" action="<?php echo e(route($indexRoute)); ?>" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-2">
                        <i class="fas fa-search text-blue-600 text-sm"></i>
                        البحث
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-blue-500 dark:text-blue-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                               placeholder="الاسم، البريد الإلكتروني، رقم الهاتف" 
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 pr-10 text-sm text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                    </div>
                </div>
                <?php if($pageMode !== 'students'): ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-2">
                        <i class="fas fa-user-tag text-blue-600 text-sm"></i>
                        الدور
                    </label>
                    <select name="role" 
                            class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">جميع الأدوار</option>
                        <option value="super_admin" <?php echo e(request('role') == 'super_admin' ? 'selected' : ''); ?>>مدير عام</option>
                        <option value="admin" <?php echo e(request('role') == 'admin' ? 'selected' : ''); ?>>إداري</option>
                        <option value="instructor" <?php echo e(request('role') == 'instructor' ? 'selected' : ''); ?>>مدرب</option>
                        <option value="teacher" <?php echo e(request('role') == 'teacher' ? 'selected' : ''); ?>>مدرس</option>
                        <option value="student" <?php echo e(request('role') == 'student' ? 'selected' : ''); ?>><?php echo e(__('admin.student_role_label')); ?></option>
                        <option value="parent" <?php echo e(request('role') == 'parent' ? 'selected' : ''); ?>>ولي أمر</option>
                        <option value="employee" <?php echo e(request('role') == 'employee' ? 'selected' : ''); ?>>موظف</option>
                    </select>
                </div>
                <?php endif; ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-2">
                        <i class="fas fa-toggle-on text-blue-600 text-sm"></i>
                        الحالة
                    </label>
                    <select name="status" 
                            class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">جميع الحالات</option>
                        <option value="1" <?php echo e(request('status') == '1' ? 'selected' : ''); ?>>نشط</option>
                        <option value="0" <?php echo e(request('status') == '0' ? 'selected' : ''); ?>>غير نشط</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" 
                            class="flex-1 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-4 py-2.5 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-search"></i>
                        <span>بحث</span>
                    </button>
                    <?php if(request()->anyFilled(['search', 'role', 'status'])): ?>
                    <a href="<?php echo e(route($indexRoute)); ?>" 
                       class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-semibold transition-colors" 
                       title="مسح الفلتر">
                        <i class="fas fa-times"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </section>

    <!-- قائمة المستخدمين -->
    <section class="<?php echo e($pageMode === 'students' ? 'students-card' : 'rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-lg overflow-hidden'); ?>">
        <div class="<?php echo e($pageMode === 'students' ? 'students-card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4' : 'px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4'); ?>">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-slate-100"><?php echo e($pageMode === 'students' ? 'قائمة الطلاب والحسابات' : 'قائمة المستخدمين'); ?></h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 font-medium mt-1">
                        <span class="font-bold text-blue-600"><?php echo e($users->total()); ?></span> <?php echo e($pageMode === 'students' ? 'طالب' : 'مستخدم'); ?>

                    </p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-900/40">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700 dark:text-slate-300">
                        <th class="px-6 py-4 text-right">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user text-blue-600"></i>
                                <span>المستخدم</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user-tag text-blue-600"></i>
                                <span>الدور</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-toggle-on text-blue-600"></i>
                                <span>الحالة</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar text-blue-600"></i>
                                <span>تاريخ التسجيل</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <i class="fas fa-cog text-blue-600"></i>
                                <span>الإجراءات</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white dark:bg-slate-800 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="table-row">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="avatar-gradient w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md">
                                        <?php echo e(mb_substr($user->name, 0, 1, 'UTF-8')); ?>

                                    </div>
                                    <div class="space-y-1">
                                        <p class="font-bold text-slate-900 dark:text-slate-100 text-base"><?php echo e($user->name); ?></p>
                                        <p class="text-xs text-slate-600 dark:text-slate-400 font-medium flex items-center gap-2">
                                            <i class="fas fa-envelope text-blue-500 text-xs"></i>
                                            <?php echo e($user->email ?: 'لا يوجد بريد إلكتروني'); ?>

                                        </p>
                                        <p class="text-xs text-slate-600 dark:text-slate-400 font-medium flex items-center gap-2">
                                            <i class="fas fa-phone text-blue-500 text-xs"></i>
                                            <?php echo e($user->phone); ?>

                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                    // التحقق من كون المستخدم موظف أولاً
                                    if ($user->is_employee) {
                                        $roleKey = 'employee';
                                    } else {
                                        $roleKey = $user->role;
                                    }
                                    $roleMeta = $roles[$roleKey] ?? $roles['student'];
                                ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold <?php echo e($roleMeta['badge']); ?>">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    <?php echo e($roleMeta['label']); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold <?php echo e($user->is_active ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800'); ?>">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    <?php echo e($user->is_active ? 'نشط' : 'غير نشط'); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100"><?php echo e($user->created_at->format('Y-m-d')); ?></div>
                                    <div class="text-xs text-slate-600 dark:text-slate-400 font-medium"><?php echo e($user->created_at->format('H:i')); ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?php echo e(route('admin.users.show', $user->id)); ?>" 
                                       class="w-9 h-9 flex items-center justify-center bg-slate-50 dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-200 rounded-lg font-semibold transition-colors shadow-sm hover:shadow-md"
                                       title="عرض">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" 
                                       class="w-9 h-9 flex items-center justify-center bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 text-blue-600 dark:text-blue-300 rounded-lg font-semibold transition-colors shadow-sm hover:shadow-md"
                                       title="تعديل">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <?php if($user->id !== auth()->id()): ?>
                                        <button type="button" onclick="deleteUser(this)" 
                                                data-delete-url="<?php echo e(route('admin.users.delete', $user->id)); ?>"
                                                class="w-9 h-9 flex items-center justify-center bg-rose-50 dark:bg-rose-900/20 hover:bg-rose-100 dark:hover:bg-rose-900/40 text-rose-600 dark:text-rose-300 rounded-lg font-semibold transition-colors shadow-sm hover:shadow-md"
                                                title="حذف">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center">
                                        <i class="fas fa-users text-3xl text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 dark:text-slate-100 text-lg mb-1">لا توجد نتائج مطابقة</p>
                                        <p class="text-sm text-slate-600 dark:text-slate-400 font-medium">جرب تغيير معايير البحث</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($users->hasPages()): ?>
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
                <?php echo e($users->appends(request()->query())->links()); ?>

            </div>
        <?php endif; ?>
    </section>

    <!-- آخر المستخدمين والمستخدمين النشطون -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- آخر المستخدمين -->
        <section class="user-card rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                        <i class="fas fa-user-plus text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-slate-100">آخر المستخدمين المسجلين</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 font-medium mt-1">آخر 10 مستخدمين انضموا للمنصة</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-3 max-h-96 overflow-y-auto">
                <?php $__empty_1 = true; $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recentUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors border border-transparent hover:border-slate-200 dark:hover:border-slate-600">
                    <div class="avatar-gradient w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md">
                        <?php echo e(mb_substr($recentUser->name, 0, 1, 'UTF-8')); ?>

                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-900 dark:text-slate-100 truncate"><?php echo e($recentUser->name); ?></p>
                        <div class="flex items-center gap-3 mt-1 flex-wrap">
                            <?php
                                $recentRoleKey = $recentUser->is_employee ? 'employee' : ($recentUser->role ?? 'student');
                            ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e(($roles[$recentRoleKey] ?? $roles['student'])['badge']); ?>">
                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                <?php echo e(($roles[$recentRoleKey] ?? $roles['student'])['label']); ?>

                            </span>
                            <span class="text-xs text-slate-600 dark:text-slate-400 font-medium"><?php echo e($recentUser->created_at->diffForHumans()); ?></span>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($recentUser->is_active ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800'); ?>">
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                        <?php echo e($recentUser->is_active ? 'نشط' : 'غير نشط'); ?>

                    </span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-users text-2xl text-blue-600"></i>
                    </div>
                    <p class="text-slate-600 dark:text-slate-400 font-medium">لا توجد مستخدمين بعد</p>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- المستخدمين النشطون مؤخراً -->
        <section class="user-card rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                        <i class="fas fa-user-check text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-slate-100">المستخدمين النشطون مؤخراً</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 font-medium mt-1">نشطوا خلال آخر 7 أيام</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-3 max-h-96 overflow-y-auto">
                <?php $__empty_1 = true; $__currentLoopData = $recentlyActiveUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activeUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors border border-transparent hover:border-slate-200 dark:hover:border-slate-600">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md">
                        <?php echo e(mb_substr($activeUser->name, 0, 1, 'UTF-8')); ?>

                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-900 dark:text-slate-100 truncate"><?php echo e($activeUser->name); ?></p>
                        <div class="flex items-center gap-3 mt-1 flex-wrap">
                            <?php
                                $activeRoleKey = $activeUser->is_employee ? 'employee' : ($activeUser->role ?? 'student');
                            ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e(($roles[$activeRoleKey] ?? $roles['student'])['badge']); ?>">
                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                <?php echo e(($roles[$activeRoleKey] ?? $roles['student'])['label']); ?>

                            </span>
                            <span class="text-xs text-slate-600 dark:text-slate-400 font-medium">آخر نشاط: <?php echo e($activeUser->updated_at->diffForHumans()); ?></span>
                        </div>
                    </div>
                    <div class="w-2.5 h-2.5 bg-emerald-500 rounded-full shadow-md"></div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-user-check text-2xl text-emerald-600"></i>
                    </div>
                    <p class="text-slate-600 dark:text-slate-400 font-medium">لا يوجد مستخدمين نشطون مؤخراً</p>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- توزيع المستخدمين وإحصائيات التسجيل -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- توزيع المستخدمين حسب الدور -->
        <section class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                        <i class="fas fa-chart-pie text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-slate-100">توزيع المستخدمين حسب الدور</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 font-medium mt-1">نظرة عامة على توزيع المستخدمين</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <?php
                        $totalForPercentage = $stats['total'] > 0 ? $stats['total'] : 1;
                        $roleDistribution = [
                            'super_admin' => ['count' => $usersByRole['super_admin'] ?? 0, 'label' => 'مدير عام', 'color' => 'rose', 'icon' => 'fas fa-user-shield'],
                            'admin' => ['count' => $usersByRole['admin'] ?? 0, 'label' => 'إداري', 'color' => 'rose', 'icon' => 'fas fa-user-shield'],
                            'instructor' => ['count' => $usersByRole['instructor'] ?? 0, 'label' => 'مدرب', 'color' => 'sky', 'icon' => 'fas fa-chalkboard-teacher'],
                            'teacher' => ['count' => $usersByRole['teacher'] ?? 0, 'label' => 'مدرس', 'color' => 'sky', 'icon' => 'fas fa-chalkboard-teacher'],
                            'student' => ['count' => $usersByRole['student'] ?? 0, 'label' => __('admin.student_role_label'), 'color' => 'emerald', 'icon' => 'fas fa-user-graduate'],
                            'parent' => ['count' => $usersByRole['parent'] ?? 0, 'label' => 'ولي أمر', 'color' => 'indigo', 'icon' => 'fas fa-user-friends'],
                            'employee' => ['count' => \App\Models\User::where('is_employee', true)->count(), 'label' => 'موظف', 'color' => 'amber', 'icon' => 'fas fa-briefcase'],
                        ];
                        // دمج super_admin مع admin
                        if (isset($roleDistribution['super_admin']) && isset($roleDistribution['admin'])) {
                            $roleDistribution['admin']['count'] += $roleDistribution['super_admin']['count'];
                            unset($roleDistribution['super_admin']);
                        }
                        // دمج instructor مع teacher
                        if (isset($roleDistribution['instructor']) && isset($roleDistribution['teacher'])) {
                            $roleDistribution['instructor']['count'] += $roleDistribution['teacher']['count'];
                            $roleDistribution['instructor']['label'] = 'مدرسون';
                            unset($roleDistribution['teacher']);
                        }
                    ?>
                    <?php $__currentLoopData = $roleDistribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleKey => $roleData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $percentage = ($roleData['count'] / $totalForPercentage) * 100;
                            $colorClasses = [
                                'rose' => ['bg' => 'bg-rose-500', 'text' => 'text-rose-600', 'light' => 'bg-rose-100', 'border' => 'border-rose-200'],
                                'sky' => ['bg' => 'bg-blue-500', 'text' => 'text-blue-600', 'light' => 'bg-blue-100', 'border' => 'border-blue-200'],
                                'emerald' => ['bg' => 'bg-emerald-500', 'text' => 'text-emerald-600', 'light' => 'bg-emerald-100', 'border' => 'border-emerald-200'],
                                'indigo' => ['bg' => 'bg-indigo-500', 'text' => 'text-indigo-600', 'light' => 'bg-indigo-100', 'border' => 'border-indigo-200'],
                                'amber' => ['bg' => 'bg-amber-500', 'text' => 'text-amber-600', 'light' => 'bg-amber-100', 'border' => 'border-amber-200'],
                            ];
                            $color = $colorClasses[$roleData['color']] ?? $colorClasses['sky'];
                        ?>
                        <div class="p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-<?php echo e($color['text']); ?>/30 hover:shadow-md transition-all">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 <?php echo e($color['light']); ?> rounded-lg flex items-center justify-center">
                                        <i class="<?php echo e($roleData['icon']); ?> <?php echo e($color['text']); ?> text-base"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 dark:text-slate-100 text-sm"><?php echo e($roleData['label']); ?></p>
                                        <p class="text-xs text-slate-600 dark:text-slate-400 font-medium"><?php echo e(number_format($roleData['count'])); ?> مستخدم</p>
                                    </div>
                                </div>
                                <span class="text-base font-bold <?php echo e($color['text']); ?>"><?php echo e(number_format($percentage, 1)); ?>%</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                                <div class="<?php echo e($color['bg']); ?> h-2 rounded-full transition-all duration-300" style="width: <?php echo e($percentage); ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>

        <!-- إحصائيات التسجيل الشهرية -->
        <section class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white shadow-md">
                        <i class="fas fa-chart-line text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-slate-100">إحصائيات التسجيل الشهرية</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 font-medium mt-1">آخر 6 أشهر</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <?php if($usersByMonth->count() > 0): ?>
                    <?php
                        $maxCount = $usersByMonth->max('count') ?: 1;
                        $monthNames = [
                            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
                        ];
                    ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $usersByMonth->reverse(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monthData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $barHeight = ($monthData->count / $maxCount) * 100;
                                $monthName = $monthNames[$monthData->month] ?? $monthData->month;
                            ?>
                            <div class="p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:shadow-md transition-all">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-slate-100"><?php echo e($monthName); ?> <?php echo e($monthData->year); ?></span>
                                    <span class="text-base font-bold text-purple-600"><?php echo e(number_format($monthData->count)); ?></span>
                                </div>
                                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-3 overflow-hidden">
                                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-3 rounded-full transition-all duration-300" style="width: <?php echo e($barHeight); ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-purple-50 dark:bg-purple-900/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-chart-line text-2xl text-purple-600"></i>
                        </div>
                        <p class="text-slate-600 dark:text-slate-400 font-medium">لا توجد بيانات شهرية متاحة</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- الإجراءات السريعة -->
    <section class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-bolt text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-slate-100">إجراءات سريعة</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 font-medium mt-1">تنظيم وإدارة صلاحيات المستخدمين بكفاءة</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-blue-600 bg-blue-50 px-3 py-1.5 rounded-lg">
                <i class="fas fa-tools"></i>
                Quick Actions
            </span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            <a href="<?php echo e(route('admin.roles.index')); ?>" 
               class="group rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-md transition-all duration-200 user-card">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 shadow-sm">
                        <i class="fas fa-shield-alt text-lg"></i>
                    </div>
                </div>
                <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-2">إدارة الأدوار</h4>
                <p class="text-xs text-slate-600 dark:text-slate-400 font-medium leading-relaxed">تعريف الصلاحيات وتوزيعها حسب الفريق</p>
            </a>
            <a href="<?php echo e(route('admin.permissions.index')); ?>" 
               class="group rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-md transition-all duration-200 user-card">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm">
                        <i class="fas fa-key text-lg"></i>
                    </div>
                </div>
                <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-2">مصفوفة الصلاحيات</h4>
                <p class="text-xs text-slate-600 dark:text-slate-400 font-medium leading-relaxed">إدارة الصلاحيات الدقيقة لكل مستخدم</p>
            </a>
            <a href="<?php echo e(route('admin.users.create')); ?>" 
               class="group rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-md transition-all duration-200 user-card">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shadow-sm">
                        <i class="fas fa-user-plus text-lg"></i>
                    </div>
                </div>
                <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-2">إضافة حساب جديد</h4>
                <p class="text-xs text-slate-600 dark:text-slate-400 font-medium leading-relaxed">إنشاء حسابات للمدرسين أو الطلاب الجدد</p>
            </a>
            <a href="<?php echo e(route('admin.activity-log')); ?>" 
               class="group rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-md transition-all duration-200 user-card">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 shadow-sm">
                        <i class="fas fa-history text-lg"></i>
                    </div>
                </div>
                <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-2">سجل النشاطات</h4>
                <p class="text-xs text-slate-600 dark:text-slate-400 font-medium leading-relaxed">مراجعة تحركات الفريق على المنصة</p>
            </a>
        </div>
    </section>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    function deleteUser(btn) {
        const deleteUrl = btn && btn.getAttribute ? btn.getAttribute('data-delete-url') : null;
        if (!deleteUrl) {
            alert('خطأ: رابط الحذف غير متوفر. حدّث الصفحة وحاول مرة أخرى.');
            return;
        }
        if (!confirm('هل أنت متأكد من حذف هذا المستخدم؟ هذا الإجراء لا يمكن التراجع عنه.')) {
            return;
        }
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('خطأ: لم يتم العثور على CSRF token');
            return;
        }

        fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(async function(response) {
            var contentType = response.headers.get('content-type') || '';
            var data = {};
            try {
                var text = await response.text();
                if (text && contentType.indexOf('application/json') !== -1) {
                    data = JSON.parse(text);
                } else if (text && text.trim().length > 0) {
                    data = { message: text };
                }
            } catch (e) {
                data = {};
            }
            return { ok: response.ok, status: response.status, data: data };
        })
        .then(function(result) {
            if (result.ok && result.status === 200) {
                var msg = (result.data && result.data.message) ? result.data.message : 'تم حذف المستخدم بنجاح';
                if (result.data && result.data.success === false) {
                    alert('خطأ: ' + (result.data.message || msg));
                    return;
                }
                alert(msg);
                window.location.reload();
                return;
            }
            var errorMsg = (result.data && (result.data.message || result.data.error)) || '';
            if (!errorMsg) {
                if (result.status === 419) errorMsg = 'انتهت الجلسة. حدّث الصفحة وحاول مرة أخرى.';
                else if (result.status === 403) errorMsg = 'غير مصرح لك بهذا الإجراء.';
                else if (result.status === 404) errorMsg = 'المستخدم غير موجود.';
                else errorMsg = 'حدث خطأ أثناء حذف المستخدم.';
            }
            alert('خطأ: ' + errorMsg);
        })
        .catch(function(error) {
            console.error('Error:', error);
            alert('حدث خطأ أثناء حذف المستخدم: ' + (error.message || 'تأكد من الاتصال ثم أعد المحاولة.'));
        });
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\users\index.blade.php ENDPATH**/ ?>