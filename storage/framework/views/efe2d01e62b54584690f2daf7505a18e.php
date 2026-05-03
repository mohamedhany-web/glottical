<div class="flex flex-col h-full">
    <!-- Logo -->
    <div class="px-4 py-5 flex-shrink-0 border-b border-slate-200 dark:border-slate-600">
        <div class="sidebar-logo flex items-center gap-3">
            <?php if(! empty($adminPanelLogoUrl)): ?>
            <div class="w-9 h-9 rounded-[10px] flex items-center justify-center flex-shrink-0 overflow-hidden bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-600 shadow-sm">
                <img src="<?php echo e($adminPanelLogoUrl); ?>" alt="" width="36" height="36" class="w-full h-full object-contain p-0.5">
            </div>
            <?php else: ?>
            <div class="w-9 h-9 rounded-[10px] bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-md shadow-blue-500/25 flex-shrink-0">
                <span class="text-lg font-black text-white">M</span>
            </div>
            <?php endif; ?>
            <div class="sidebar-logo-text">
                <h2 class="text-sm font-heading font-bold text-slate-800 dark:text-slate-100 tracking-tight leading-tight">Muallimx</h2>
                <p class="text-[9px] text-slate-500 dark:text-slate-400 font-medium"><?php echo e(__('admin.admin_panel')); ?></p>
            </div>
        </div>
    </div>

    <?php
        $u = auth()->user();
        $u->loadMissing(['roles.permissions', 'directPermissions']);
        // إخفاء روابط (الإنجازات/الشارات/التقييمات) من السايدبار فقط بدون حذف الصفحات.
        $hideEducationExtrasInSidebar = true;
        // هل المستخدم super_admin بدون RBAC مخصص؟ → يرى كل شيء
        $isFull = $u->isAdmin() && !$u->roles()->exists();
        $rbacStrictEmployee = $u->is_employee && $u->roles()->exists();
        // تسمية القسم + المجموعة الكبيرة (كانت تظهر فقط لـ super_admin بدون أدوار فاختفت عن موظفي RBAC)
        $sidebarStudentHub = $isFull
            || $u->hasPermission('manage.users')
            || $u->hasPermission('manage.students-accounts')
            || $u->hasPermission('manage.enrollments')
            || $u->hasPermission('manage.subscriptions')
            || $u->hasPermission('manage.student-control')
            || $u->hasPermission('manage.support-tickets')
            || $u->hasPermission('manage.consultations')
            || $u->hasPermission('manage.hiring-academies')
            || $u->hasPermission('manage.curriculum-library')
            || $u->hasPermission('manage.teacher-features')
            || $u->hasPermission('manage.quality-control')
            || $u->hasPermission('view.reports');
    ?>
    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 overflow-y-auto sidebar-nav" style="min-height: 0;">
        <ul class="space-y-0.5">
            
            <?php $dashboardActive = request()->routeIs('admin.dashboard'); ?>
            <li>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="sidebar-link <?php echo e($dashboardActive ? 'active' : ''); ?>">
                    <i class="fas fa-chart-line"></i>
                    <span><?php echo e(__('admin.dashboard')); ?></span>
                </a>
            </li>

            <?php $profileActive = request()->routeIs('admin.profile*'); ?>
            <li>
                <a href="<?php echo e(route('admin.profile')); ?>" class="sidebar-link <?php echo e($profileActive ? 'active' : ''); ?>">
                    <i class="fas fa-user"></i>
                    <span><?php echo e(__('admin.profile')); ?></span>
                </a>
            </li>

            <?php if(!$rbacStrictEmployee || $u->hasPermission('manage.notifications')): ?>
            <?php
                try {
                    $sidebarInboxUnread = \App\Models\Notification::where('user_id', $u->id)->unread()->valid()->count();
                } catch (\Exception $e) {
                    $sidebarInboxUnread = 0;
                }
            ?>
            <li>
                <a href="<?php echo e(route('admin.notifications.inbox')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.notifications.inbox') ? 'active' : ''); ?>">
                    <i class="fas fa-inbox"></i>
                    <span>وارد الإشعارات</span>
                    <?php if($sidebarInboxUnread > 0): ?>
                        <span class="sidebar-badge bg-rose-500 text-white"><?php echo e($sidebarInboxUnread > 99 ? '99+' : $sidebarInboxUnread); ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endif; ?>

            <?php if($isFull || $u->hasPermission('manage.contact-messages')): ?>
            <?php
                try {
                    $sidebarContactUnread = \App\Models\ContactMessage::whereNull('read_at')->count();
                } catch (\Exception $e) {
                    $sidebarContactUnread = 0;
                }
            ?>
            <li>
                <a href="<?php echo e(route('admin.contact-messages.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.contact-messages.*') ? 'active' : ''); ?>">
                    <i class="fas fa-envelope-open-text"></i>
                    <span>رسائل التواصل</span>
                    <?php if($sidebarContactUnread > 0): ?>
                        <span class="sidebar-badge bg-amber-500 text-white"><?php echo e($sidebarContactUnread > 99 ? '99+' : $sidebarContactUnread); ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endif; ?>

            <?php if($isFull || $u->hasPermission('manage.site-services')): ?>
            <li>
                <a href="<?php echo e(route('admin.site-services.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.site-services.*') ? 'active' : ''); ?>">
                    <i class="fas fa-concierge-bell"></i>
                    <span>خدمات الموقع</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if($isFull || $u->hasPermission('manage.site-testimonials') || $u->hasPermission('manage.site-services')): ?>
            <li>
                <a href="<?php echo e(route('admin.site-testimonials.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.site-testimonials.*') ? 'active' : ''); ?>">
                    <i class="fas fa-quote-right"></i>
                    <span>آراء الموقع (الرئيسية)</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if($isFull || $u->hasPermission('manage.system-settings')): ?>
            <li>
                <a href="<?php echo e(route('admin.system-settings.edit')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.system-settings.*') ? 'active' : ''); ?>">
                    <i class="fas fa-sliders-h"></i>
                    <span>إعدادات النظام</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if($isFull || $u->hasPermission('manage.about-page')): ?>
            <li>
                <a href="<?php echo e(route('admin.about.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.about.*') ? 'active' : ''); ?>">
                    <i class="fas fa-info-circle"></i>
                    <span>صفحة من نحن</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if(($isFull || $u->hasPermission('manage.faq')) && Route::has('admin.faq.index')): ?>
            <li>
                <a href="<?php echo e(route('admin.faq.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.faq.*') ? 'active' : ''); ?>">
                    <i class="fas fa-question-circle"></i>
                    <span>الأسئلة الشائعة</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if($sidebarStudentHub): ?>
            <li class="sidebar-section-label">أقسام حسب الوظيفة</li>
            <?php endif; ?>

            <?php if($sidebarStudentHub): ?>
            
            <?php
                $studentControlOpen = request()->routeIs('admin.students-accounts.*')
                    || request()->routeIs('admin.users.*')
                    || request()->routeIs('admin.students-control.*')
                    || request()->routeIs('admin.online-enrollments.*')
                    || request()->routeIs('admin.subscriptions.*')
                    || request()->routeIs('admin.teacher-features.*')
                    || request()->routeIs('admin.support-tickets.*')
                    || request()->routeIs('admin.support-inquiry-categories.*')
                    || request()->routeIs('admin.academy-opportunities.*')
                    || request()->routeIs('admin.hiring-academies.*')
                    || request()->routeIs('admin.curriculum-library.*')
                    || request()->routeIs('admin.consultations.*')
                    || request()->routeIs('admin.quality-control.students')
                    || request()->routeIs('admin.reports.users')
                    || request()->routeIs('admin.portfolio-marketing-profiles.*')
                    || request()->routeIs('admin.portfolio.*');
            ?>
            <li x-data="{ open: <?php echo e($studentControlOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" class="sidebar-group-btn">
                    <span class="flex items-center gap-3">
                        <i class="fas fa-user-shield w-5 text-center text-indigo-400"></i>
                        <span>التحكم الشامل بالطلاب والخدمات المدفوعة</span>
                    </span>
                    <i class="fas fa-chevron-down chevron" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-1 mr-3 space-y-0.5 border-r border-slate-200 pr-3">
                    <?php if($isFull || $u->hasPermission('manage.users') || $u->hasPermission('manage.students-accounts')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.students-accounts.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.students-accounts.*') ? 'active' : ''); ?>">
                            <i class="fas fa-users"></i><span>إدارة الطلاب والحسابات</span>
                            <?php
                                try {
                                    $studentsCount = \App\Models\User::where('role', 'student')->count();
                                } catch (\Exception $e) {
                                    $studentsCount = 0;
                                }
                            ?>
                            <?php if($studentsCount > 0): ?>
                                <span class="sidebar-badge bg-indigo-500 text-white"><?php echo e($studentsCount); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.enrollments')) && Route::has('admin.online-enrollments.index')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.online-enrollments.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.online-enrollments.*') ? 'active' : ''); ?>">
                            <i class="fas fa-user-graduate"></i><span>تسجيلات الطلاب</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.subscriptions')) && Route::has('admin.subscriptions.index')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.subscriptions.*') ? 'active' : ''); ?>">
                            <i class="fas fa-calendar-check"></i><span>اشتراكات الخدمات المدفوعة</span>
                            <?php
                                try {
                                    $activeSubsCount = \App\Models\Subscription::where('status', 'active')->count();
                                } catch (\Exception $e) {
                                    $activeSubsCount = 0;
                                }
                            ?>
                            <?php if($activeSubsCount > 0): ?>
                                <span class="sidebar-badge bg-emerald-500 text-white"><?php echo e($activeSubsCount); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.teacher-features')) && Route::has('admin.teacher-features.index')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.teacher-features.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.teacher-features.*') ? 'active' : ''); ?>">
                            <i class="fas fa-chalkboard-teacher"></i><span>مزايا اشتراك المعلمين</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.support-tickets')) && Route::has('admin.support-tickets.index')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.support-tickets.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.support-tickets.*') ? 'active' : ''); ?>">
                            <i class="fas fa-headset"></i><span>الدعم الفني (التذاكر)</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.support-tickets')) && Route::has('admin.support-inquiry-categories.index')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.support-inquiry-categories.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.support-inquiry-categories.*') ? 'active' : ''); ?>">
                            <i class="fas fa-tags"></i><span>تصنيفات دعم الطلاب</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.consultations')) && Route::has('admin.consultations.index')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.consultations.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.consultations.*') ? 'active' : ''); ?>">
                            <i class="fas fa-comments-dollar"></i><span>استشارات المدربين</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.hiring-academies')) && Route::has('admin.hiring-academies.index')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.hiring-academies.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.hiring-academies.*') ? 'active' : ''); ?>">
                            <i class="fas fa-school"></i><span><?php echo e(__('admin.hiring_sidebar_academies')); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.hiring-academies')) && Route::has('admin.academy-opportunities.index')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.academy-opportunities.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.academy-opportunities.*') ? 'active' : ''); ?>">
                            <i class="fas fa-building"></i><span>فرص الأكاديميات</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.curriculum-library')) && Route::has('admin.curriculum-library.index')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.curriculum-library.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.curriculum-library.*') ? 'active' : ''); ?>">
                            <i class="fas fa-book-open"></i><span>مكتبة المناهج (المدفوع)</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.student-control') || $u->hasPermission('manage.quality-control')) && Route::has('admin.quality-control.students')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.quality-control.students')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.quality-control.students') ? 'active' : ''); ?>">
                            <i class="fas fa-shield-alt"></i><span>مراقبة شاملة على الطلاب</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('view.reports') || $u->hasPermission('manage.student-control')) && Route::has('admin.reports.users')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.reports.users')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.reports.users') ? 'active' : ''); ?>">
                            <i class="fas fa-chart-bar"></i><span>تقارير الطلاب والاشتراكات</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.subscriptions') || $u->hasPermission('manage.student-control')) && Route::has('admin.students-control.paid-features')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.students-control.paid-features')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.students-control.paid-features*') ? 'active' : ''); ?>">
                            <i class="fas fa-layer-group"></i><span>إدارة المزايا المدفوعة</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.subscriptions') || $u->hasPermission('manage.student-control')) && Route::has('admin.portfolio-marketing-profiles.index')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.portfolio-marketing-profiles.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.portfolio-marketing-profiles.*') ? 'active' : ''); ?>">
                            <i class="fas fa-id-card"></i><span>مراجعة الملف التعريفي (التسويق الشخصي)</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.subscriptions') || $u->hasPermission('manage.student-control')) && Route::has('admin.portfolio.index')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.portfolio.index')); ?>" class="sidebar-sub-link <?php echo e((request()->routeIs('admin.portfolio.index') || request()->routeIs('admin.portfolio.show')) ? 'active' : ''); ?>">
                            <i class="fas fa-images"></i><span>مراجعة مشاريع البورتفوليو</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.student-control')) && Route::has('admin.students-control.consumption')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.students-control.consumption')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.students-control.consumption') ? 'active' : ''); ?>">
                            <i class="fas fa-chart-pie"></i><span>استهلاك المستخدمين</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>

            <?php endif; ?>

            <?php if($isFull || $u->hasPermission('manage.orders') || $u->hasPermission('manage.coupons') || $u->hasPermission('manage.referrals') || $u->hasPermission('manage.leads') || $u->hasPermission('view.sales-analytics')): ?>
            
            <?php $salesSectionOpen = request()->routeIs('admin.orders.*') || request()->routeIs('admin.sales.index') || request()->routeIs('admin.sales.leads.*') || request()->routeIs('admin.coupons.*') || request()->routeIs('admin.coupon-commissions.*') || request()->routeIs('admin.referrals.*') || request()->routeIs('admin.referral-programs.*'); ?>
            <li x-data="{ open: <?php echo e($salesSectionOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" class="sidebar-group-btn">
                    <span class="flex items-center gap-3"><i class="fas fa-shopping-cart w-5 text-center text-emerald-400"></i><span>قسم المبيعات</span></span>
                    <i class="fas fa-chevron-down chevron" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-1 mr-3 space-y-0.5 border-r border-slate-200 pr-3">
                    <?php if($isFull || $u->hasPermission('manage.leads')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.sales.leads.*') ? 'active' : ''); ?>">
                            <i class="fas fa-user-plus"></i><span>العملاء المحتملون (Leads)</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('view.sales-analytics')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.sales.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.sales.index') ? 'active' : ''); ?>">
                            <i class="fas fa-chart-line"></i><span>لوحة تحليلات المبيعات</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.orders')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.orders.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.orders.*') ? 'active' : ''); ?>">
                            <i class="fas fa-shopping-bag"></i><span>الطلبات</span>
                            <?php try { $pendingOrdersSales = \App\Models\Order::where('status', 'pending')->count(); } catch (\Exception $e) { $pendingOrdersSales = 0; } ?>
                            <?php if($pendingOrdersSales > 0): ?><span class="sidebar-badge bg-indigo-500 text-white"><?php echo e($pendingOrdersSales); ?></span><?php endif; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.coupons')): ?>
                    <li><a href="<?php echo e(route('admin.coupons.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.coupons.*') && !request()->routeIs('admin.coupon-commissions.*') ? 'active' : ''); ?>"><i class="fas fa-ticket-alt"></i><span>الكوبونات والخصومات</span></a></li>
                    <li><a href="<?php echo e(route('admin.coupon-commissions.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.coupon-commissions.*') ? 'active' : ''); ?>"><i class="fas fa-coins"></i><span>عمولات كوبونات التسويق</span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.referrals')): ?>
                    <li><a href="<?php echo e(route('admin.referral-programs.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.referral-programs.*') ? 'active' : ''); ?>"><i class="fas fa-gift"></i><span>برامج الإحالة</span></a></li>
                    <li><a href="<?php echo e(route('admin.referrals.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.referrals.*') ? 'active' : ''); ?>"><i class="fas fa-user-friends"></i><span>الإحالات</span></a></li>
                    <?php endif; ?>
                </ul>
            </li>

            <?php endif; ?>

            <?php if($isFull || $u->hasPermission('manage.users') || $u->hasPermission('manage.leaves') || $u->hasPermission('manage.employee-agreements') || $u->hasPermission('manage.instructor-requests')): ?>
            
            <?php $hrSectionOpen = request()->routeIs('admin.employees.*') || request()->routeIs('admin.employee-jobs.*') || request()->routeIs('admin.employee-tasks.*') || request()->routeIs('admin.leaves.*') || request()->routeIs('admin.employee-agreements.*'); ?>
            <li x-data="{ open: <?php echo e($hrSectionOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" class="sidebar-group-btn">
                    <span class="flex items-center gap-3"><i class="fas fa-users-cog w-5 text-center text-cyan-400"></i><span>قسم الموارد البشرية</span></span>
                    <i class="fas fa-chevron-down chevron" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-1 mr-3 space-y-0.5 border-r border-slate-200 pr-3">
                    <?php if($isFull || $u->hasPermission('manage.users')): ?>
                    <li><a href="<?php echo e(route('admin.employees.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.employees.*') ? 'active' : ''); ?>"><i class="fas fa-user-tie"></i><span>الموظفين</span></a></li>
                    <li><a href="<?php echo e(route('admin.employee-jobs.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.employee-jobs.*') ? 'active' : ''); ?>"><i class="fas fa-briefcase"></i><span>الوظائف</span></a></li>
                    <li>
                        <a href="<?php echo e(route('admin.employee-tasks.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.employee-tasks.*') ? 'active' : ''); ?>">
                            <i class="fas fa-tasks"></i><span>مهام الموظفين</span>
                            <?php try { $pendingTasksHR = \App\Models\EmployeeTask::where('status', 'pending')->count(); } catch (\Exception $e) { $pendingTasksHR = 0; } ?>
                            <?php if($pendingTasksHR > 0): ?><span class="sidebar-badge bg-amber-400 text-amber-900"><?php echo e($pendingTasksHR); ?></span><?php endif; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.leaves')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.leaves.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.leaves.*') ? 'active' : ''); ?>">
                            <i class="fas fa-calendar-alt"></i><span>طلبات الإجازة</span>
                            <?php try { $pendingLeavesHR = \App\Models\LeaveRequest::where('status', 'pending')->count(); } catch (\Exception $e) { $pendingLeavesHR = 0; } ?>
                            <?php if($pendingLeavesHR > 0): ?><span class="sidebar-badge bg-amber-400 text-amber-900"><?php echo e($pendingLeavesHR); ?></span><?php endif; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.employee-agreements')): ?>
                    <li><a href="<?php echo e(route('admin.employee-agreements.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.employee-agreements.*') ? 'active' : ''); ?>"><i class="fas fa-file-contract"></i><span>اتفاقيات الموظفين ورواتبهم</span></a></li>
                    <?php endif; ?>
                </ul>
            </li>

            <?php endif; ?>

            <?php if($isFull || $u->hasPermission('manage.invoices') || $u->hasPermission('manage.payments') || $u->hasPermission('manage.transactions') || $u->hasPermission('manage.wallets') || $u->hasPermission('view.wallets') || $u->hasPermission('manage.salaries') || $u->hasPermission('manage.expenses') || $u->hasPermission('manage.instructor-accounts')): ?>
            
            <?php $accountingSectionOpen = request()->routeIs('admin.invoices.*') || request()->routeIs('admin.payments.*') || request()->routeIs('admin.wallets.*') || request()->routeIs('admin.salaries.*') || request()->routeIs('admin.expenses.*') || request()->routeIs('admin.installments.*') || request()->routeIs('admin.accounting.*') || request()->routeIs('admin.transactions.*'); ?>
            <li x-data="{ open: <?php echo e($accountingSectionOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" class="sidebar-group-btn">
                    <span class="flex items-center gap-3"><i class="fas fa-calculator w-5 text-center text-amber-400"></i><span>قسم المحاسبة</span></span>
                    <i class="fas fa-chevron-down chevron" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-1 mr-3 space-y-0.5 border-r border-slate-200 pr-3">
                    <?php if($isFull || $u->hasPermission('manage.invoices')): ?>
                    <li><a href="<?php echo e(route('admin.invoices.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.invoices.*') ? 'active' : ''); ?>"><i class="fas fa-file-invoice"></i><span>الفواتير</span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.payments')): ?>
                    <li><a href="<?php echo e(route('admin.payments.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.payments.*') ? 'active' : ''); ?>"><i class="fas fa-credit-card"></i><span>المدفوعات</span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.transactions')): ?>
                    <li><a href="<?php echo e(route('admin.transactions.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.transactions.*') ? 'active' : ''); ?>"><i class="fas fa-exchange-alt"></i><span>المعاملات</span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.wallets') || $u->hasPermission('view.wallets')): ?>
                    <li><a href="<?php echo e(route('admin.wallets.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.wallets.*') ? 'active' : ''); ?>"><i class="fas fa-wallet"></i><span>المحافظ</span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.salaries')): ?>
                    <li><a href="<?php echo e(route('admin.salaries.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.salaries.*') ? 'active' : ''); ?>"><i class="fas fa-money-check-alt"></i><span>رواتب المدربين</span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.employee-agreements')): ?>
                    <li><a href="<?php echo e(route('admin.employee-agreements.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.employee-agreements.*') ? 'active' : ''); ?>"><i class="fas fa-users-cog"></i><span>اتفاقيات الموظفين</span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.expenses')): ?>
                    <li><a href="<?php echo e(route('admin.expenses.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.expenses.*') ? 'active' : ''); ?>"><i class="fas fa-receipt"></i><span>المصروفات</span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.installments')): ?>
                    <li><a href="<?php echo e(route('admin.installments.agreements.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.installments.agreements.*') ? 'active' : ''); ?>"><i class="fas fa-handshake"></i><span>اتفاقيات التقسيط</span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.invoices') || $u->hasPermission('manage.payments') || $u->hasPermission('manage.transactions')): ?>
                    <li><a href="<?php echo e(route('admin.accounting.reports')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.accounting.reports*') ? 'active' : ''); ?>"><i class="fas fa-chart-pie"></i><span>تقارير المحاسبة</span></a></li>
                    <?php endif; ?>
                </ul>
            </li>

            <?php endif; ?>

            <?php if($isFull || $u->hasPermission('manage.users') || $u->hasPermission('manage.notifications') || $u->hasPermission('view.activity-log') || $u->hasPermission('view.statistics') || $u->hasPermission('manage.email-broadcasts') || $u->hasPermission('manage.performance') || $u->hasPermission('manage.two-factor-logs')): ?>
            <li class="sidebar-section-label">إدارة النظام</li>
            
            <?php
                $systemManagementOpen = request()->routeIs('admin.users.*')
                    || request()->routeIs('admin.orders.*')
                    || request()->routeIs('admin.notifications.*')
                    || request()->routeIs('admin.employee-notifications.*')
                    || request()->routeIs('admin.email-broadcasts.*')
                    || request()->routeIs('admin.activity-log*')
                    || request()->routeIs('admin.two-factor-logs.*')
                    || request()->routeIs('admin.statistics.*')
                    || request()->routeIs('admin.performance.*');
            ?>
            <li x-data="{ open: <?php echo e($systemManagementOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" class="sidebar-group-btn">
                    <span class="flex items-center gap-3">
                        <i class="fas fa-cogs w-5 text-center"></i>
                        <span><?php echo e(__('admin.system_management')); ?></span>
                    </span>
                    <i class="fas fa-chevron-down chevron" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-1 mr-3 space-y-0.5 border-r border-slate-200 pr-3">
                    <?php if($isFull || $u->hasPermission('manage.users')): ?>
                    <li><a href="<?php echo e(route('admin.users.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.users*') ? 'active' : ''); ?>"><i class="fas fa-users"></i><span><?php echo e(__('admin.users')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.orders')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.orders.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.orders.*') ? 'active' : ''); ?>">
                            <i class="fas fa-shopping-cart"></i><span><?php echo e(__('admin.orders')); ?></span>
                            <?php try { $pendingOrders = \App\Models\Order::where('status', 'pending')->count(); } catch (\Exception $e) { $pendingOrders = 0; } ?>
                            <?php if($pendingOrders > 0): ?><span class="sidebar-badge bg-indigo-500 text-white"><?php echo e($pendingOrders); ?></span><?php endif; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.notifications')): ?>
                    <li><a href="<?php echo e(route('admin.notifications.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.notifications.*') ? 'active' : ''); ?>"><i class="fas fa-bell"></i><span><?php echo e(__('admin.notifications')); ?></span></a></li>
                    <li><a href="<?php echo e(route('admin.employee-notifications.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.employee-notifications.*') ? 'active' : ''); ?>"><i class="fas fa-user-tie"></i><span><?php echo e(__('admin.employee_notifications')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.email-broadcasts')): ?>
                    <li><a href="<?php echo e(route('admin.email-broadcasts.index', 'all_users')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.email-broadcasts.*') ? 'active' : ''); ?>"><i class="fas fa-envelope"></i><span>إشعارات البريد (Gmail)</span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('view.activity-log')): ?>
                    <li><a href="<?php echo e(route('admin.activity-log')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.activity-log*') ? 'active' : ''); ?>"><i class="fas fa-history"></i><span><?php echo e(__('admin.activity_log')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.two-factor-logs')): ?>
                    <li><a href="<?php echo e(route('admin.two-factor-logs.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.two-factor-logs.*') ? 'active' : ''); ?>"><i class="fas fa-shield-alt"></i><span><?php echo e(__('admin.two_factor_logs')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('view.statistics')): ?>
                    <li><a href="<?php echo e(route('admin.statistics.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.statistics*') ? 'active' : ''); ?>"><i class="fas fa-chart-bar"></i><span><?php echo e(__('admin.statistics')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.performance')): ?>
                    <li><a href="<?php echo e(route('admin.performance.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.performance.*') ? 'active' : ''); ?>"><i class="fas fa-tachometer-alt"></i><span><?php echo e(__('admin.performance')); ?></span></a></li>
                    <?php endif; ?>
                </ul>
            </li>

            <?php endif; ?>

            <?php if($isFull || $u->hasPermission('manage.agreements') || $u->hasPermission('manage.withdrawals') || $u->hasPermission('manage.employee-agreements')): ?>
            
            <?php $agreementsOpen = request()->routeIs('admin.agreements.*') || request()->routeIs('admin.withdrawals.*') || request()->routeIs('admin.employee-agreements.*'); ?>
            <li x-data="{ open: <?php echo e($agreementsOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" class="sidebar-group-btn">
                    <span class="flex items-center gap-3"><i class="fas fa-handshake w-5 text-center text-amber-400"></i><span><?php echo e(__('admin.agreements_system')); ?></span></span>
                    <i class="fas fa-chevron-down chevron" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-1 mr-3 space-y-0.5 border-r border-slate-200 pr-3">
                    <?php if(($isFull || $u->hasPermission('manage.agreements')) && Route::has('admin.agreements.index')): ?>
                    <li><a href="<?php echo e(route('admin.agreements.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.agreements.*') ? 'active' : ''); ?>"><i class="fas fa-file-contract"></i><span><?php echo e(__('admin.instructor_agreements')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.employee-agreements')) && Route::has('admin.employee-agreements.index')): ?>
                    <li><a href="<?php echo e(route('admin.employee-agreements.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.employee-agreements.*') ? 'active' : ''); ?>"><i class="fas fa-user-tie"></i><span><?php echo e(__('admin.employee_agreements')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.withdrawals')) && Route::has('admin.withdrawals.index')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.withdrawals.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.withdrawals.*') ? 'active' : ''); ?>">
                            <i class="fas fa-money-bill-wave"></i><span><?php echo e(__('admin.withdrawal_requests')); ?></span>
                            <?php try { $pendingWithdrawals = \App\Models\WithdrawalRequest::where('status', 'pending')->count(); } catch (\Exception $e) { $pendingWithdrawals = 0; } ?>
                            <?php if($pendingWithdrawals > 0): ?><span class="sidebar-badge bg-amber-400 text-amber-900"><?php echo e($pendingWithdrawals); ?></span><?php endif; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>

            <?php endif; ?>

            <?php if($isFull || $u->hasPermission('manage.invoices') || $u->hasPermission('manage.payments') || $u->hasPermission('manage.transactions') || $u->hasPermission('manage.wallets') || $u->hasPermission('view.wallets') || $u->hasPermission('manage.subscriptions') || $u->hasPermission('manage.installments') || $u->hasPermission('manage.salaries') || $u->hasPermission('manage.expenses') || $u->hasPermission('manage.instructor-accounts')): ?>
            <li class="sidebar-section-label">المالية</li>
            
            <?php
                $accountingOpen = request()->routeIs('admin.invoices.*') || request()->routeIs('admin.payments.*') || request()->routeIs('admin.transactions.*') || request()->routeIs('admin.wallets.*') || request()->routeIs('admin.expenses.*') || request()->routeIs('admin.subscriptions.*') || request()->routeIs('admin.installments.*') || request()->routeIs('admin.accounting.*') || request()->routeIs('admin.salaries.*') || request()->routeIs('admin.employee-agreements.*');
            ?>
            <li x-data="{ open: <?php echo e($accountingOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" class="sidebar-group-btn">
                    <span class="flex items-center gap-3"><i class="fas fa-money-bill-wave w-5 text-center text-emerald-400"></i><span><?php echo e(__('admin.accounting')); ?></span></span>
                    <i class="fas fa-chevron-down chevron" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-1 mr-3 space-y-0.5 border-r border-slate-200 pr-3">
                    <?php if($isFull || $u->hasPermission('manage.invoices')): ?>
                    <li><a href="<?php echo e(route('admin.invoices.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.invoices.*') ? 'active' : ''); ?>"><i class="fas fa-file-invoice"></i><span><?php echo e(__('admin.invoices')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.payments')): ?>
                    <li><a href="<?php echo e(route('admin.payments.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.payments.*') ? 'active' : ''); ?>"><i class="fas fa-credit-card"></i><span><?php echo e(__('admin.payments')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.transactions')): ?>
                    <li><a href="<?php echo e(route('admin.transactions.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.transactions.*') ? 'active' : ''); ?>"><i class="fas fa-exchange-alt"></i><span><?php echo e(__('admin.transactions')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.wallets') || $u->hasPermission('view.wallets')): ?>
                    <li><a href="<?php echo e(route('admin.wallets.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.wallets.*') ? 'active' : ''); ?>"><i class="fas fa-wallet"></i><span><?php echo e(__('admin.wallets')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.salaries')): ?>
                    <li><a href="<?php echo e(route('admin.salaries.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.salaries.*') ? 'active' : ''); ?>"><i class="fas fa-money-check-alt"></i><span><?php echo e(__('admin.instructor_finances')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.instructor-accounts')): ?>
                    <li><a href="<?php echo e(route('admin.accounting.instructor-accounts.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.accounting.instructor-accounts.*') ? 'active' : ''); ?>"><i class="fas fa-user-tie"></i><span>حسابات المدربين</span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.employee-agreements')): ?>
                    <li><a href="<?php echo e(route('admin.employee-agreements.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.employee-agreements.*') ? 'active' : ''); ?>"><i class="fas fa-users-cog"></i><span>اتفاقيات الموظفين ورواتبهم</span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.expenses')): ?>
                    <li><a href="<?php echo e(route('admin.expenses.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.expenses.*') ? 'active' : ''); ?>"><i class="fas fa-receipt"></i><span><?php echo e(__('admin.expenses')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.subscriptions')): ?>
                    <li><a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.subscriptions.*') ? 'active' : ''); ?>"><i class="fas fa-calendar-check"></i><span><?php echo e(__('admin.subscriptions')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.installments')): ?>
                    <?php $installmentsOpen = request()->routeIs('admin.installments.*'); ?>
                    <li x-data="{ open: <?php echo e($installmentsOpen ? 'true' : 'false'); ?> }">
                        <button @click="open = !open" class="sidebar-sub-link w-full justify-between">
                            <span class="flex items-center gap-2"><i class="fas fa-calendar-check w-4 text-center"></i><span><?php echo e(__('admin.installment_management')); ?></span></span>
                            <i class="fas fa-chevron-down text-[9px] text-slate-500 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <ul x-show="open" x-transition class="mt-0.5 mr-2 space-y-0.5 border-r border-slate-200 pr-2">
                            <li><a href="<?php echo e(route('admin.installments.plans.index')); ?>" class="sidebar-sub-link text-xs <?php echo e(request()->routeIs('admin.installments.plans.*') ? 'active' : ''); ?>"><i class="fas fa-layer-group w-3.5"></i><span><?php echo e(__('admin.installment_plans')); ?></span></a></li>
                            <li><a href="<?php echo e(route('admin.installments.agreements.index')); ?>" class="sidebar-sub-link text-xs <?php echo e(request()->routeIs('admin.installments.agreements.*') ? 'active' : ''); ?>"><i class="fas fa-handshake w-3.5"></i><span><?php echo e(__('admin.payment_agreements')); ?></span></a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.invoices') || $u->hasPermission('manage.payments') || $u->hasPermission('manage.transactions')): ?>
                    <li><a href="<?php echo e(route('admin.accounting.reports')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.accounting.*') ? 'active' : ''); ?>"><i class="fas fa-chart-pie"></i><span><?php echo e(__('admin.accounting_reports')); ?></span></a></li>
                    <?php endif; ?>
                </ul>
            </li>

            <?php endif; ?>

            <?php if($isFull || $u->hasPermission('manage.coupons') || $u->hasPermission('manage.referrals') || $u->hasPermission('manage.loyalty') || $u->hasPermission('manage.popup-ads') || $u->hasPermission('manage.personal-branding')): ?>
            
            <?php
                $marketingOpen = request()->routeIs('admin.coupons.*') || request()->routeIs('admin.coupon-commissions.*') || request()->routeIs('admin.referral-programs.*') || request()->routeIs('admin.referrals.*') || request()->routeIs('admin.loyalty.*') || request()->routeIs('admin.personal-branding.*') || request()->routeIs('admin.popup-ads.*');
            ?>
            <li x-data="{ open: <?php echo e($marketingOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" class="sidebar-group-btn">
                    <span class="flex items-center gap-3"><i class="fas fa-tags w-5 text-center text-pink-400"></i><span><?php echo e(__('admin.marketing')); ?></span></span>
                    <i class="fas fa-chevron-down chevron" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-1 mr-3 space-y-0.5 border-r border-slate-200 pr-3">
                    <?php if($isFull || $u->hasPermission('manage.popup-ads')): ?>
                    <li><a href="<?php echo e(route('admin.popup-ads.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.popup-ads.*') ? 'active' : ''); ?>"><i class="fas fa-bullhorn"></i><span><?php echo e(__('admin.popup_ads')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.personal-branding')): ?>
                    <li><a href="<?php echo e(route('admin.personal-branding.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.personal-branding.*') ? 'active' : ''); ?>"><i class="fas fa-user-tie"></i><span><?php echo e(__('admin.personal_branding')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.coupons')): ?>
                    <li><a href="<?php echo e(route('admin.coupons.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.coupons.*') && !request()->routeIs('admin.coupon-commissions.*') ? 'active' : ''); ?>"><i class="fas fa-ticket-alt"></i><span><?php echo e(__('admin.coupons_discounts')); ?></span></a></li>
                    <li><a href="<?php echo e(route('admin.coupon-commissions.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.coupon-commissions.*') ? 'active' : ''); ?>"><i class="fas fa-coins"></i><span>عمولات كوبونات التسويق</span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.referrals')): ?>
                    <li><a href="<?php echo e(route('admin.referral-programs.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.referral-programs.*') ? 'active' : ''); ?>"><i class="fas fa-gift"></i><span><?php echo e(__('admin.referral_programs')); ?></span></a></li>
                    <li><a href="<?php echo e(route('admin.referrals.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.referrals.*') ? 'active' : ''); ?>"><i class="fas fa-user-friends"></i><span><?php echo e(__('admin.referrals')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.loyalty')): ?>
                    <li><a href="<?php echo e(route('admin.loyalty.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.loyalty.*') ? 'active' : ''); ?>"><i class="fas fa-star"></i><span><?php echo e(__('admin.loyalty_programs')); ?></span></a></li>
                    <?php endif; ?>
                </ul>
            </li>

            <?php endif; ?>

            <?php if($isFull || $u->hasPermission('manage.subscriptions') || $u->hasPermission('manage.courses') || $u->hasPermission('manage.packages') || $u->hasPermission('manage.teacher-features') || $u->hasPermission('manage.curriculum-library')): ?>
            <li class="sidebar-section-label">العناصر المدفوعة</li>
            
            <?php
                $paidSubscriptionsOpen = request()->routeIs('admin.subscriptions.*')
                    || request()->routeIs('admin.teacher-features.*')
                    || request()->routeIs('admin.packages.*')
                    || request()->routeIs('admin.curriculum-library.*');
            ?>
            <li x-data="{ open: <?php echo e($paidSubscriptionsOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" class="sidebar-group-btn">
                    <span class="flex items-center gap-3"><i class="fas fa-credit-card w-5 text-center text-cyan-400"></i><span>العناصر المدفوعة</span></span>
                    <i class="fas fa-chevron-down chevron" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-1 mr-3 space-y-0.5 border-r border-slate-200 pr-3">
                    <?php if($isFull || $u->hasPermission('manage.subscriptions')): ?>
                    <li><a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.subscriptions.*') ? 'active' : ''); ?>"><i class="fas fa-calendar-check"></i><span><?php echo e(__('admin.subscriptions')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.teacher-features')): ?>
                    <li><a href="<?php echo e(route('admin.teacher-features.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.teacher-features.*') ? 'active' : ''); ?>"><i class="fas fa-chalkboard-teacher"></i><span>مزايا اشتراك المعلمين</span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.curriculum-library')): ?>
                    <li><a href="<?php echo e(route('admin.curriculum-library.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.curriculum-library.*') ? 'active' : ''); ?>"><i class="fas fa-book-open"></i><span>مكتبة المناهج</span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.packages')): ?>
                    <li><a href="<?php echo e(route('admin.packages.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.packages.*') ? 'active' : ''); ?>"><i class="fas fa-tags"></i><span><?php echo e(__('admin.pricing_packages')); ?></span></a></li>
                    <?php endif; ?>
                </ul>
            </li>

            <?php endif; ?>

            <?php if($isFull || $u->hasPermission('manage.enrollments') || $u->hasPermission('manage.courses') || $u->hasPermission('manage.exams') || $u->hasPermission('manage.lectures') || $u->hasPermission('manage.assignments') || $u->hasPermission('manage.live-sessions') || $u->hasPermission('manage.live-servers') || $u->hasPermission('manage.question-bank') || $u->hasPermission('manage.attendance') || $u->hasPermission('manage.achievements') || $u->hasPermission('manage.badges') || $u->hasPermission('manage.reviews')): ?>
            <li class="sidebar-section-label">التعليم</li>
            
            <?php if($isFull || $u->hasPermission('manage.enrollments')): ?>
            <?php $enrollmentsOpen = request()->routeIs('admin.online-enrollments.*'); ?>
            <li x-data="{ open: <?php echo e($enrollmentsOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" class="sidebar-group-btn">
                    <span class="flex items-center gap-3"><i class="fas fa-user-graduate w-5 text-center text-teal-400"></i><span><?php echo e(__('admin.enrollments')); ?></span></span>
                    <i class="fas fa-chevron-down chevron" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-1 mr-3 space-y-0.5 border-r border-slate-200 pr-3">
                    <li><a href="<?php echo e(route('admin.online-enrollments.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.online-enrollments.*') ? 'active' : ''); ?>"><i class="fas fa-laptop"></i><span><?php echo e(__('admin.online_enrollments')); ?></span></a></li>
                </ul>
            </li>
            <?php endif; ?>

            
            <?php if($isFull || $u->hasPermission('manage.courses') || $u->hasPermission('manage.lectures') || $u->hasPermission('manage.assignments') || $u->hasPermission('manage.exams') || $u->hasPermission('manage.question-bank') || $u->hasPermission('manage.attendance') || $u->hasPermission('manage.achievements') || $u->hasPermission('manage.badges') || $u->hasPermission('manage.reviews')): ?>
            <?php
                $contentManagementOpen = request()->routeIs('admin.advanced-courses.*') || request()->routeIs('admin.course-categories.*') || request()->routeIs('admin.exams.*') || request()->routeIs('admin.question-bank.*') || request()->routeIs('admin.question-categories.*') || request()->routeIs('admin.lectures.*') || request()->routeIs('admin.assignments.*') || request()->routeIs('admin.attendance.*') || request()->routeIs('admin.achievements.*') || request()->routeIs('admin.badges.*') || request()->routeIs('admin.reviews.*');
            ?>
            <li x-data="{ open: <?php echo e($contentManagementOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" class="sidebar-group-btn">
                    <span class="flex items-center gap-3"><i class="fas fa-folder w-5 text-center text-violet-400"></i><span><?php echo e(__('admin.content_management')); ?></span></span>
                    <i class="fas fa-chevron-down chevron" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-1 mr-3 space-y-0.5 border-r border-slate-200 pr-3">
                    <?php if($isFull || $u->hasPermission('manage.courses')): ?>
                    <?php $advancedCoursesActive = request()->routeIs('admin.advanced-courses.*') || request()->routeIs('admin.courses.lessons.*'); ?>
                    <li><a href="<?php echo e(route('admin.advanced-courses.index')); ?>" class="sidebar-sub-link <?php echo e($advancedCoursesActive ? 'active' : ''); ?>"><i class="fas fa-graduation-cap"></i><span><?php echo e(__('admin.courses_management')); ?></span></a></li>
                    <li><a href="<?php echo e(route('admin.course-categories.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.course-categories.*') ? 'active' : ''); ?>"><i class="fas fa-tags"></i><span><?php echo e(__('admin.course_categories')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.lectures')): ?>
                    <li><a href="<?php echo e(route('admin.lectures.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.lectures.*') ? 'active' : ''); ?>"><i class="fas fa-video"></i><span><?php echo e(__('admin.lectures')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.assignments')): ?>
                    <li><a href="<?php echo e(route('admin.assignments.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.assignments.*') ? 'active' : ''); ?>"><i class="fas fa-tasks"></i><span><?php echo e(__('admin.assignments_projects')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.exams')): ?>
                    <li><a href="<?php echo e(route('admin.exams.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.exams.*') ? 'active' : ''); ?>"><i class="fas fa-clipboard-check"></i><span><?php echo e(__('admin.exams')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.question-bank')): ?>
                    <?php $questionBankActive = request()->routeIs('admin.question-bank.*') || request()->routeIs('admin.question-categories.*'); ?>
                    <li><a href="<?php echo e(route('admin.question-bank.index')); ?>" class="sidebar-sub-link <?php echo e($questionBankActive ? 'active' : ''); ?>"><i class="fas fa-database"></i><span><?php echo e(__('admin.question_bank')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.attendance')): ?>
                    <li><a href="<?php echo e(route('admin.attendance.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.attendance.*') ? 'active' : ''); ?>"><i class="fas fa-user-check"></i><span>الحضور والانصراف</span></a></li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.achievements')) && !$hideEducationExtrasInSidebar): ?>
                    <li><a href="<?php echo e(route('admin.achievements.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.achievements.*') ? 'active' : ''); ?>"><i class="fas fa-trophy"></i><span>الإنجازات</span></a></li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.badges')) && !$hideEducationExtrasInSidebar): ?>
                    <li><a href="<?php echo e(route('admin.badges.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.badges.*') ? 'active' : ''); ?>"><i class="fas fa-medal"></i><span>الشارات</span></a></li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.reviews')) && !$hideEducationExtrasInSidebar): ?>
                    <li><a href="<?php echo e(route('admin.reviews.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.reviews.*') ? 'active' : ''); ?>"><i class="fas fa-star-half-alt"></i><span>التقييمات والمراجعات</span></a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            
            <?php if($isFull || $u->hasPermission('manage.live-sessions') || $u->hasPermission('manage.live-servers')): ?>
            <?php
                $liveOpen = request()->routeIs('admin.live-sessions.*')
                    || request()->routeIs('admin.live-recordings.*')
                    || request()->routeIs('admin.classroom-recordings.*')
                    || request()->routeIs('admin.live-servers.*')
                    || request()->routeIs('admin.live-settings.*')
                    || request()->routeIs('admin.n8n.live-session-reports.*')
                    || request()->routeIs('admin.n8n.settings');
            ?>
            <li x-data="{ open: <?php echo e($liveOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" class="sidebar-group-btn">
                    <span class="flex items-center gap-3">
                        <i class="fas fa-broadcast-tower w-5 text-center text-red-400"></i>
                        <span>جلسات البث المباشر والمعلمين</span>
                    </span>
                    <i class="fas fa-chevron-down chevron" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-1 mr-3 space-y-0.5 border-r border-slate-200 pr-3">
                    <?php if(($isFull || $u->hasPermission('manage.live-sessions')) && Route::has('admin.live-sessions.index')): ?>
                        <li>
                            <a href="<?php echo e(route('admin.live-sessions.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.live-sessions.*') ? 'active' : ''); ?>">
                                <i class="fas fa-video"></i><span>جلسات البث المباشر</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.live-sessions')) && Route::has('admin.live-recordings.index')): ?>
                        <li>
                            <a href="<?php echo e(route('admin.live-recordings.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.live-recordings.*') ? 'active' : ''); ?>">
                                <i class="fas fa-play-circle"></i><span>تسجيلات الجلسات</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if(($isFull || $u->hasPermission('manage.live-sessions')) && Route::has('admin.n8n.live-session-reports.index')): ?>
                        <li>
                            <a href="<?php echo e(route('admin.n8n.live-session-reports.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.n8n.live-session-reports.*') ? 'active' : ''); ?>">
                                <i class="fas fa-robot"></i><span>تقارير n8n</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if(($isFull || $u->hasPermission('manage.live-servers')) && Route::has('admin.n8n.settings')): ?>
                        <li>
                            <a href="<?php echo e(route('admin.n8n.settings')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.n8n.settings') ? 'active' : ''); ?>">
                                <i class="fas fa-plug"></i><span>إعداد تكامل n8n</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if(($isFull || $u->hasPermission('manage.live-sessions')) && Route::has('admin.classroom-recordings.index')): ?>
                        <li>
                            <a href="<?php echo e(route('admin.classroom-recordings.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.classroom-recordings.*') ? 'active' : ''); ?>">
                                <i class="fas fa-chalkboard"></i><span>تسجيلات Classroom</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.live-servers')) && Route::has('admin.live-servers.index')): ?>
                        <li>
                            <a href="<?php echo e(route('admin.live-servers.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.live-servers.index') || request()->routeIs('admin.live-servers.create') || request()->routeIs('admin.live-servers.edit') ? 'active' : ''); ?>">
                                <i class="fas fa-server"></i><span>سيرفرات البث (VPS)</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.live-servers')) && Route::has('admin.live-servers.control')): ?>
                        <li>
                            <a href="<?php echo e(route('admin.live-servers.control')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.live-servers.control') ? 'active' : ''); ?>">
                                <i class="fas fa-tachometer-alt"></i><span>لوحة التحكم بالسيرفرات</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(($isFull || $u->hasPermission('manage.live-servers')) && Route::has('admin.live-settings.index')): ?>
                        <li>
                            <a href="<?php echo e(route('admin.live-settings.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.live-settings.*') ? 'active' : ''); ?>">
                                <i class="fas fa-sliders-h"></i><span>إعدادات نظام اللايف</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            <?php endif; ?>

            <?php if($isFull || $u->hasPermission('manage.users') || $u->hasPermission('manage.tasks') || $u->hasPermission('manage.leaves') || $u->hasPermission('manage.instructor-requests') || $u->hasPermission('manage.employee-agreements') || $u->hasPermission('academic_supervision.manage')): ?>
            <li class="sidebar-section-label">الفريق</li>
            
            <?php $employeesOpen = request()->routeIs('admin.employees.*') || request()->routeIs('admin.employee-jobs.*') || request()->routeIs('admin.employee-tasks.*') || request()->routeIs('admin.leaves.*') || request()->routeIs('admin.tasks.*') || request()->routeIs('admin.instructor-requests.*') || request()->routeIs('admin.academic-supervision.*'); ?>
            <li x-data="{ open: <?php echo e($employeesOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" class="sidebar-group-btn">
                    <span class="flex items-center gap-3"><i class="fas fa-users-cog w-5 text-center text-cyan-400"></i><span><?php echo e(__('admin.management')); ?></span></span>
                    <i class="fas fa-chevron-down chevron" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-1 mr-3 space-y-0.5 border-r border-slate-200 pr-3">
                    <?php if($isFull || $u->hasPermission('manage.users')): ?>
                    <li><a href="<?php echo e(route('admin.employees.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.employees.*') ? 'active' : ''); ?>"><i class="fas fa-user-tie"></i><span><?php echo e(__('admin.employees')); ?></span></a></li>
                    <li><a href="<?php echo e(route('admin.employee-jobs.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.employee-jobs.*') ? 'active' : ''); ?>"><i class="fas fa-briefcase"></i><span><?php echo e(__('admin.jobs')); ?></span></a></li>
                    <li>
                        <a href="<?php echo e(route('admin.employee-tasks.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.employee-tasks.*') ? 'active' : ''); ?>">
                            <i class="fas fa-tasks"></i><span><?php echo e(__('admin.employee_tasks')); ?></span>
                            <?php try { $pendingTasks = \App\Models\EmployeeTask::where('status', 'pending')->count(); } catch (\Exception $e) { $pendingTasks = 0; } ?>
                            <?php if($pendingTasks > 0): ?><span class="sidebar-badge bg-amber-400 text-amber-900"><?php echo e($pendingTasks); ?></span><?php endif; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('academic_supervision.manage')): ?>
                    <li><a href="<?php echo e(route('admin.academic-supervision.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.academic-supervision.*') ? 'active' : ''); ?>"><i class="fas fa-user-graduate"></i><span>الإشراف الأكاديمي</span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.tasks')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.tasks.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.tasks.*') ? 'active' : ''); ?>">
                            <i class="fas fa-chalkboard-teacher"></i><span><?php echo e(__('admin.instructor_tasks')); ?></span>
                            <?php try { $pendingInstructorTasks = \App\Models\Task::whereIn('user_id', \App\Models\User::whereIn('role', ['instructor', 'teacher'])->pluck('id'))->where('status', 'pending')->count(); } catch (\Exception $e) { $pendingInstructorTasks = 0; } ?>
                            <?php if($pendingInstructorTasks > 0): ?><span class="sidebar-badge bg-amber-500 text-white"><?php echo e($pendingInstructorTasks); ?></span><?php endif; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.instructor-requests')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.instructor-requests.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.instructor-requests.*') ? 'active' : ''); ?>">
                            <i class="fas fa-inbox"></i><span><?php echo e(__('admin.instructor_requests_join')); ?></span>
                            <?php try { $pendingInstructorRequests = \App\Models\InstructorRequest::where('status', 'pending')->count(); } catch (\Exception $e) { $pendingInstructorRequests = 0; } ?>
                            <?php if($pendingInstructorRequests > 0): ?><span class="sidebar-badge bg-amber-500 text-white"><?php echo e($pendingInstructorRequests); ?></span><?php endif; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.leaves')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.leaves.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.leaves.*') ? 'active' : ''); ?>">
                            <i class="fas fa-calendar-alt"></i><span><?php echo e(__('admin.leaves')); ?></span>
                            <?php try { $pendingLeaves = \App\Models\LeaveRequest::where('status', 'pending')->count(); } catch (\Exception $e) { $pendingLeaves = 0; } ?>
                            <?php if($pendingLeaves > 0): ?><span class="sidebar-badge bg-amber-400 text-amber-900"><?php echo e($pendingLeaves); ?></span><?php endif; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>

            <?php endif; ?>

            <?php if($isFull || $u->hasPermission('view.statistics') || $u->hasPermission('manage.quality-control') || $u->hasPermission('manage.student-control')): ?>
            
            <?php $qualityControlOpen = request()->routeIs('admin.quality-control.*'); ?>
            <li x-data="{ open: <?php echo e($qualityControlOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" class="sidebar-group-btn">
                    <span class="flex items-center gap-3"><i class="fas fa-shield-alt w-5 text-center text-rose-400"></i><span><?php echo e(__('admin.quality_supervision')); ?></span></span>
                    <i class="fas fa-chevron-down chevron" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-1 mr-3 space-y-0.5 border-r border-slate-200 pr-3">
                    <?php if($isFull || $u->hasPermission('manage.quality-control') || $u->hasPermission('view.statistics')): ?>
                    <li><a href="<?php echo e(route('admin.quality-control.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.quality-control.index') ? 'active' : ''); ?>"><i class="fas fa-tachometer-alt"></i><span><?php echo e(__('admin.control_panel')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.quality-control') || $u->hasPermission('manage.student-control')): ?>
                    <li><a href="<?php echo e(route('admin.quality-control.students')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.quality-control.students') ? 'active' : ''); ?>"><i class="fas fa-user-graduate"></i><span><?php echo e(__('admin.student_control')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('manage.quality-control')): ?>
                    <li><a href="<?php echo e(route('admin.quality-control.instructors')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.quality-control.instructors') ? 'active' : ''); ?>"><i class="fas fa-chalkboard-teacher"></i><span><?php echo e(__('admin.instructor_control')); ?></span></a></li>
                    <li><a href="<?php echo e(route('admin.quality-control.employees')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.quality-control.employees') ? 'active' : ''); ?>"><i class="fas fa-user-tie"></i><span><?php echo e(__('admin.employee_control')); ?></span></a></li>
                    <li><a href="<?php echo e(route('admin.quality-control.operations')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.quality-control.operations') ? 'active' : ''); ?>"><i class="fas fa-cogs"></i><span><?php echo e(__('admin.operations_followup')); ?></span></a></li>
                    <?php endif; ?>
                </ul>
            </li>

            <?php endif; ?>

            <?php if($isFull || $u->hasPermission('manage.certificates') || $u->hasPermission('manage.roles') || $u->hasPermission('manage.permissions') || $u->hasPermission('manage.tasks') || $u->hasPermission('manage.messages') || $u->hasPermission('view.statistics') || $u->hasPermission('view.reports') || $u->hasPermission('view.financial-reports') || $u->hasPermission('view.academic-reports')): ?>
            <li class="sidebar-section-label">متقدم</li>
            <?php endif; ?>

            <?php if($isFull || $u->hasPermission('manage.certificates')): ?>
            
            <?php $certificatesManagementOpen = request()->routeIs('admin.certificates.*'); ?>
            <li x-data="{ open: <?php echo e($certificatesManagementOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" class="sidebar-group-btn">
                    <span class="flex items-center gap-3"><i class="fas fa-certificate w-5 text-center text-yellow-400"></i><span><?php echo e(__('admin.certificates_control')); ?></span></span>
                    <i class="fas fa-chevron-down chevron" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-1 mr-3 space-y-0.5 border-r border-slate-200 pr-3">
                    <li>
                        <a href="<?php echo e(route('admin.certificates.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.certificates.index') ? 'active' : ''); ?>">
                            <i class="fas fa-list"></i><span><?php echo e(__('admin.certificates_list')); ?></span>
                            <?php try { $totalCertificates = \App\Models\Certificate::count(); } catch (\Exception $e) { $totalCertificates = 0; } ?>
                            <?php if($totalCertificates > 0): ?><span class="sidebar-badge bg-indigo-400 text-white"><?php echo e($totalCertificates); ?></span><?php endif; ?>
                        </a>
                    </li>
                    <li><a href="<?php echo e(route('admin.certificates.create')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.certificates.create') ? 'active' : ''); ?>"><i class="fas fa-plus-circle"></i><span><?php echo e(__('admin.issue_certificate')); ?></span></a></li>
                    <?php
                        $pendingCertificates = \App\Models\Certificate::where(function($q) {
                            $q->where('status', 'pending')->orWhere('is_verified', false);
                        })->count();
                    ?>
                    <?php if($pendingCertificates > 0): ?>
                    <li>
                        <a href="<?php echo e(route('admin.certificates.index', ['status' => 'pending'])); ?>" class="sidebar-sub-link <?php echo e(request()->get('status') == 'pending' ? 'active' : ''); ?>">
                            <i class="fas fa-clock"></i><span><?php echo e(__('admin.pending_certificates')); ?></span>
                            <span class="sidebar-badge bg-amber-400 text-amber-900"><?php echo e($pendingCertificates); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>

            <?php endif; ?>

            
            <?php $permissionsOpen = request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.user-permissions.*'); ?>
            <?php
                $canManagePermissions = auth()->check() && (
                    auth()->user()->hasPermission('users.permissions')
                    || auth()->user()->hasPermission('manage.roles')
                    || auth()->user()->hasPermission('manage.permissions')
                    || auth()->user()->hasPermission('manage.user-permissions')
                );
            ?>
            <?php if($canManagePermissions): ?>
            <li x-data="{ open: <?php echo e($permissionsOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" class="sidebar-group-btn">
                    <span class="flex items-center gap-3"><i class="fas fa-shield-alt w-5 text-center"></i><span><?php echo e(__('admin.permissions_roles')); ?></span></span>
                    <i class="fas fa-chevron-down chevron" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-1 mr-3 space-y-0.5 border-r border-slate-200 pr-3">
                    <li><a href="<?php echo e(route('admin.roles.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.roles.*') ? 'active' : ''); ?>"><i class="fas fa-user-tag"></i><span><?php echo e(__('admin.roles')); ?></span></a></li>
                    <li><a href="<?php echo e(route('admin.permissions.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.permissions.*') ? 'active' : ''); ?>"><i class="fas fa-key"></i><span><?php echo e(__('admin.permissions')); ?></span></a></li>
                    <li><a href="<?php echo e(route('admin.user-permissions.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.user-permissions.*') ? 'active' : ''); ?>"><i class="fas fa-user-shield"></i><span><?php echo e(__('admin.user_permissions')); ?></span></a></li>
                </ul>
            </li>
            <?php endif; ?>
            

            
            <?php $tasksActive = request()->routeIs('admin.tasks.*'); ?>
            <?php if($isFull || $u->hasPermission('manage.tasks') || $u->hasPermission('view.tasks')): ?>
            <li>
                <a href="<?php echo e(route('admin.tasks.index')); ?>" class="sidebar-link <?php echo e($tasksActive ? 'active' : ''); ?>">
                    <i class="fas fa-list-check"></i>
                    <span><?php echo e(__('admin.tasks')); ?></span>
                </a>
            </li>
            <?php endif; ?>

            <?php if($isFull || $u->hasPermission('manage.messages')): ?>
            
            <?php $messagesActive = request()->routeIs('admin.messages.*'); ?>
            <li>
                <a href="<?php echo e(route('admin.messages.index')); ?>" class="sidebar-link <?php echo e($messagesActive ? 'active' : ''); ?>">
                    <i class="fas fa-envelope"></i>
                    <span><?php echo e(__('admin.messages')); ?></span>
                </a>
            </li>
            <?php endif; ?>

            <?php if($isFull || $u->hasPermission('view.statistics') || $u->hasPermission('view.reports') || $u->hasPermission('view.financial-reports') || $u->hasPermission('view.academic-reports')): ?>
            
            <?php $reportsOpen = request()->routeIs('admin.reports.*'); ?>
            <li x-data="{ open: <?php echo e($reportsOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" class="sidebar-group-btn">
                    <span class="flex items-center gap-3"><i class="fas fa-file-excel w-5 text-center text-emerald-400"></i><span><?php echo e(__('admin.comprehensive_reports')); ?></span></span>
                    <i class="fas fa-chevron-down chevron" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-1 mr-3 space-y-0.5 border-r border-slate-200 pr-3">
                    <?php if($isFull || $u->hasPermission('view.reports') || $u->hasPermission('view.statistics')): ?>
                    <li><a href="<?php echo e(route('admin.reports.index')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.reports.index') ? 'active' : ''); ?>"><i class="fas fa-chart-pie"></i><span><?php echo e(__('admin.reports_dashboard')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('view.reports') || $u->hasPermission('manage.users')): ?>
                    <li><a href="<?php echo e(route('admin.reports.users')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.reports.users') ? 'active' : ''); ?>"><i class="fas fa-users"></i><span><?php echo e(__('admin.user_reports')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('view.reports') || $u->hasPermission('manage.courses')): ?>
                    <li><a href="<?php echo e(route('admin.reports.courses')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.reports.courses') ? 'active' : ''); ?>"><i class="fas fa-graduation-cap"></i><span><?php echo e(__('admin.course_reports')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('view.financial-reports') || $u->hasPermission('manage.invoices')): ?>
                    <li><a href="<?php echo e(route('admin.reports.financial')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.reports.financial') ? 'active' : ''); ?>"><i class="fas fa-money-bill-wave"></i><span><?php echo e(__('admin.financial_reports')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('view.academic-reports') || $u->hasPermission('manage.courses')): ?>
                    <li><a href="<?php echo e(route('admin.reports.academic')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.reports.academic') ? 'active' : ''); ?>"><i class="fas fa-book"></i><span><?php echo e(__('admin.academic_reports')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('view.reports') || $u->hasPermission('view.activity-log')): ?>
                    <li><a href="<?php echo e(route('admin.reports.activities')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.reports.activities') ? 'active' : ''); ?>"><i class="fas fa-history"></i><span><?php echo e(__('admin.activity_reports')); ?></span></a></li>
                    <?php endif; ?>
                    <?php if($isFull || $u->hasPermission('view.reports') || $u->hasPermission('view.statistics')): ?>
                    <li><a href="<?php echo e(route('admin.reports.comprehensive')); ?>" class="sidebar-sub-link <?php echo e(request()->routeIs('admin.reports.comprehensive') ? 'active' : ''); ?>"><i class="fas fa-file-alt"></i><span><?php echo e(__('admin.comprehensive_report')); ?></span></a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Collapse Toggle (desktop only) -->
    <div class="hidden lg:flex px-3 py-2 flex-shrink-0 border-t border-slate-200 dark:border-slate-600">
        <button @click="sidebarCollapsed = !sidebarCollapsed" class="sidebar-collapse-btn w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all text-xs">
            <i class="fas fa-chevron-right transition-transform duration-300" :class="sidebarCollapsed ? '' : 'rotate-180'"></i>
            <span class="sidebar-logo-text">تصغير</span>
        </button>
    </div>

    <!-- User Info -->
    <div class="px-3 py-3 flex-shrink-0 border-t border-slate-200 dark:border-slate-600">
        <div class="sidebar-user-wrap flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-700/50 dark:hover:bg-slate-700 transition-colors">
            <?php if(auth()->user()->profile_image): ?>
                <img src="<?php echo e(auth()->user()->profile_image_url); ?>" alt="<?php echo e(auth()->user()->name); ?>" class="w-8 h-8 rounded-lg object-cover ring-1 ring-slate-200 flex-shrink-0" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg hidden flex items-center justify-center text-white font-bold text-xs flex-shrink-0"><?php echo e(mb_substr(auth()->user()->name, 0, 1)); ?></div>
            <?php else: ?>
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                    <?php echo e(mb_substr(auth()->user()->name, 0, 1)); ?>

                </div>
            <?php endif; ?>
            <div class="sidebar-user-info flex-1 min-w-0">
                <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate leading-tight"><?php echo e(auth()->user()->name); ?></p>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate leading-tight"><?php echo e(auth()->user()->phone); ?></p>
            </div>
            <div class="sidebar-user-info w-1.5 h-1.5 bg-emerald-400 rounded-full ring-2 ring-emerald-400/20 flex-shrink-0"></div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views/layouts/admin-sidebar.blade.php ENDPATH**/ ?>