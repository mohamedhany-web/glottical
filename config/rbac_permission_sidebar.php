<?php

/**
 * ربط اسم صلاحية (جدول permissions) بصفحة دخول في لوحة الإدارة أو واجهة الموظف.
 * يُستخدم لبناء سايدبار الموظف ذي الدور RBAC — التسمية تُؤخذ من قاعدة البيانات.
 *
 * - dedupe_key: عدة صلاحيات تشترك نفس الرابط (مثلاً عرض/إدارة المحافظ).
 * - route_params: يُمرَّر إلى route() عند الحاجة.
 * - route_patterns: أنماط تفعيل الرابط في القائمة.
 */
return [
    'by_permission' => [
        'admin.access' => [
            'route' => 'admin.dashboard',
            'icon' => 'fas fa-door-open',
            'route_patterns' => ['admin.dashboard'],
        ],
        'view.dashboard' => [
            'route' => 'admin.dashboard',
            'icon' => 'fas fa-chart-pie',
            'route_patterns' => ['admin.dashboard'],
        ],
        'manage.users' => [
            'route' => 'admin.users.index',
            'icon' => 'fas fa-users',
            'route_patterns' => ['admin.users.*'],
        ],
        'manage.orders' => [
            'route' => 'admin.orders.index',
            'icon' => 'fas fa-shopping-bag',
            'route_patterns' => ['admin.orders.*'],
        ],
        'manage.notifications' => [
            'route' => 'admin.notifications.inbox',
            'icon' => 'fas fa-bell',
            'route_patterns' => ['admin.notifications.*'],
        ],
        'view.activity-log' => [
            'route' => 'admin.activity-log',
            'icon' => 'fas fa-history',
            'route_patterns' => ['admin.activity-log*'],
        ],
        'view.statistics' => [
            'route' => 'admin.statistics.index',
            'icon' => 'fas fa-chart-bar',
            'route_patterns' => ['admin.statistics.*'],
        ],
        'manage.roles' => [
            'route' => 'admin.roles.index',
            'icon' => 'fas fa-user-tag',
            'route_patterns' => ['admin.roles.*'],
        ],
        'manage.permissions' => [
            'route' => 'admin.permissions.index',
            'icon' => 'fas fa-key',
            'route_patterns' => ['admin.permissions.*'],
        ],
        'manage.user-permissions' => [
            'route' => 'admin.user-permissions.index',
            'icon' => 'fas fa-user-shield',
            'route_patterns' => ['admin.user-permissions.*'],
        ],
        'users.permissions' => [
            'route' => 'admin.user-permissions.index',
            'icon' => 'fas fa-user-shield',
            'route_patterns' => ['admin.user-permissions.*'],
        ],
        'manage.invoices' => [
            'route' => 'admin.invoices.index',
            'icon' => 'fas fa-file-invoice',
            'route_patterns' => ['admin.invoices.*'],
        ],
        'manage.payments' => [
            'route' => 'admin.payments.index',
            'icon' => 'fas fa-credit-card',
            'route_patterns' => ['admin.payments.*'],
        ],
        'manage.transactions' => [
            'route' => 'admin.transactions.index',
            'icon' => 'fas fa-exchange-alt',
            'route_patterns' => ['admin.transactions.*'],
        ],
        'manage.wallets' => [
            'route' => 'admin.wallets.index',
            'icon' => 'fas fa-wallet',
            'route_patterns' => ['admin.wallets.*'],
        ],
        'view.wallets' => [
            'route' => 'admin.wallets.index',
            'icon' => 'fas fa-wallet',
            'route_patterns' => ['admin.wallets.*'],
        ],
        'manage.subscriptions' => [
            'route' => 'admin.subscriptions.index',
            'icon' => 'fas fa-calendar-check',
            'dedupe_key' => 'admin.subscriptions.hub',
            'route_patterns' => ['admin.subscriptions.*', 'admin.portfolio-marketing-profiles.*'],
        ],
        'manage.student-control' => [
            'route' => 'admin.students-control.consumption',
            'icon' => 'fas fa-eye',
            'route_patterns' => ['admin.students-control.*'],
        ],
        'manage.installments' => [
            'route' => 'admin.installments.plans.index',
            'icon' => 'fas fa-calendar-check',
            'route_patterns' => ['admin.installments.*'],
        ],
        'manage.coupons' => [
            'route' => 'admin.coupons.index',
            'icon' => 'fas fa-ticket-alt',
            'dedupe_key' => 'admin.coupons.hub',
            'route_patterns' => ['admin.coupons.*', 'admin.coupon-commissions.*', 'admin.marketing.student-wallet-credit.*'],
        ],
        'manage.referrals' => [
            'route' => 'admin.referrals.index',
            'icon' => 'fas fa-gift',
            'route_patterns' => ['admin.referrals.*'],
        ],
        'manage.loyalty' => [
            'route' => 'admin.loyalty.index',
            'icon' => 'fas fa-star',
            'route_patterns' => ['admin.loyalty.*'],
        ],
        'manage.certificates' => [
            'route' => 'admin.certificates.index',
            'icon' => 'fas fa-certificate',
            'route_patterns' => ['admin.certificates.*'],
        ],
        'manage.achievements' => [
            'route' => 'admin.achievements.index',
            'icon' => 'fas fa-trophy',
            'route_patterns' => ['admin.achievements.*'],
        ],
        'manage.badges' => [
            'route' => 'admin.badges.index',
            'icon' => 'fas fa-medal',
            'route_patterns' => ['admin.badges.*'],
        ],
        'manage.reviews' => [
            'route' => 'admin.reviews.index',
            'icon' => 'fas fa-star-half-alt',
            'route_patterns' => ['admin.reviews.*'],
        ],
        'manage.academic-years' => [
            'route' => 'admin.academic-years.index',
            'icon' => 'fas fa-calendar',
            'route_patterns' => ['admin.academic-years.*'],
        ],
        'manage.academic-subjects' => [
            'route' => 'admin.academic-subjects.index',
            'icon' => 'fas fa-book',
            'route_patterns' => ['admin.academic-subjects.*'],
        ],
        'manage.courses' => [
            'route' => 'admin.advanced-courses.index',
            'icon' => 'fas fa-graduation-cap',
            'dedupe_key' => 'hub.advanced-courses',
            'route_patterns' => ['admin.advanced-courses.*', 'admin.academic-years.*', 'admin.academic-subjects.*', 'admin.learning-paths.*'],
        ],
        'manage.academic-years' => [
            'route' => 'admin.academic-years.index',
            'icon' => 'fas fa-calendar',
            'dedupe_key' => 'hub.advanced-courses',
            'route_patterns' => ['admin.academic-years.*'],
        ],
        'manage.academic-subjects' => [
            'route' => 'admin.academic-subjects.index',
            'icon' => 'fas fa-book',
            'dedupe_key' => 'hub.advanced-courses',
            'route_patterns' => ['admin.academic-subjects.*'],
        ],
        'manage.enrollments' => [
            'route' => 'admin.online-enrollments.index',
            'icon' => 'fas fa-user-graduate',
            'dedupe_key' => 'hub.online-enrollments',
            'route_patterns' => ['admin.online-enrollments.*', 'admin.learning-path-enrollments.*'],
        ],
        'manage.lectures' => [
            'route' => 'admin.lectures.index',
            'icon' => 'fas fa-video',
            'route_patterns' => ['admin.lectures.*'],
        ],
        'manage.assignments' => [
            'route' => 'admin.assignments.index',
            'icon' => 'fas fa-tasks',
            'route_patterns' => ['admin.assignments.*'],
        ],
        'manage.exams' => [
            'route' => 'admin.exams.index',
            'icon' => 'fas fa-clipboard-check',
            'route_patterns' => ['admin.exams.*'],
        ],
        'manage.question-bank' => [
            'route' => 'admin.question-bank.index',
            'icon' => 'fas fa-database',
            'route_patterns' => ['admin.question-bank.*'],
        ],
        'manage.attendance' => [
            'route' => 'admin.attendance.index',
            'icon' => 'fas fa-user-check',
            'route_patterns' => ['admin.attendance.*'],
        ],
        'manage.about-page' => [
            'route' => 'admin.about.index',
            'icon' => 'fas fa-info-circle',
            'route_patterns' => ['admin.about.*'],
        ],
        'manage.faq' => [
            'route' => 'admin.faq.index',
            'icon' => 'fas fa-question-circle',
            'route_patterns' => ['admin.faq.*'],
        ],
        'manage.contact-messages' => [
            'route' => 'admin.contact-messages.index',
            'icon' => 'fas fa-envelope-open-text',
            'route_patterns' => ['admin.contact-messages.*'],
        ],
        'manage.free-trial-bookings' => [
            'route' => 'admin.free-trial-bookings.index',
            'icon' => 'fas fa-calendar-check',
            'route_patterns' => ['admin.free-trial-bookings.*'],
        ],
        'manage.site-services' => [
            'route' => 'admin.site-services.index',
            'icon' => 'fas fa-concierge-bell',
            'route_patterns' => ['admin.site-services.*'],
        ],
        'manage.site-testimonials' => [
            'route' => 'admin.site-testimonials.index',
            'icon' => 'fas fa-quote-right',
            'route_patterns' => ['admin.site-testimonials.*'],
        ],
        'manage.homepage-sliders' => [
            'route' => 'admin.homepage-sliders.index',
            'icon' => 'fas fa-images',
            'route_patterns' => ['admin.homepage-sliders.*'],
        ],
        'manage.system-settings' => [
            'route' => 'admin.system-settings.edit',
            'icon' => 'fas fa-sliders-h',
            'route_patterns' => ['admin.system-settings.*'],
        ],
        'manage.tasks' => [
            'route' => 'admin.employee-tasks.index',
            'icon' => 'fas fa-list-check',
            'route_patterns' => ['admin.employee-tasks.*'],
        ],
        'view.tasks' => [
            'route' => 'employee.tasks.index',
            'icon' => 'fas fa-list',
            'route_patterns' => ['employee.tasks.*'],
        ],
        'manage.messages' => [
            'route' => 'admin.messages.index',
            'icon' => 'fas fa-envelope-open-text',
            'route_patterns' => ['admin.messages.*'],
        ],
        'view.calendar' => [
            'route' => 'employee.calendar',
            'icon' => 'fas fa-calendar-alt',
            'route_patterns' => ['employee.calendar*'],
        ],
        'manage.live-sessions' => [
            'route' => 'admin.live-sessions.index',
            'icon' => 'fas fa-broadcast-tower',
            'route_patterns' => ['admin.live-sessions.*', 'admin.live-recordings.*'],
        ],
        'manage.live-servers' => [
            'route' => 'admin.live-servers.index',
            'icon' => 'fas fa-server',
            'dedupe_key' => 'admin.live-servers.hub',
            'route_patterns' => ['admin.live-servers.*', 'admin.live-settings.*'],
        ],
        'manage.agreements' => [
            'route' => 'admin.agreements.index',
            'icon' => 'fas fa-handshake',
            'route_patterns' => ['admin.agreements.*'],
        ],
        'manage.withdrawals' => [
            'route' => 'admin.withdrawals.index',
            'icon' => 'fas fa-money-bill-wave',
            'route_patterns' => ['admin.withdrawals.*'],
        ],
        'manage.employee-agreements' => [
            'route' => 'admin.employee-agreements.index',
            'icon' => 'fas fa-file-contract',
            'route_patterns' => ['admin.employee-agreements.*'],
        ],
        'manage.salaries' => [
            'route' => 'admin.salaries.index',
            'icon' => 'fas fa-money-check-alt',
            'route_patterns' => ['admin.salaries.*'],
        ],
        'manage.expenses' => [
            'route' => 'admin.expenses.index',
            'icon' => 'fas fa-receipt',
            'route_patterns' => ['admin.expenses.*'],
        ],
        'manage.instructor-accounts' => [
            'route' => 'admin.accounting.instructor-accounts.index',
            'icon' => 'fas fa-user-tie',
            'route_patterns' => ['admin.accounting.instructor-accounts.*'],
        ],
        'manage.packages' => [
            'route' => 'admin.packages.index',
            'icon' => 'fas fa-tags',
            'route_patterns' => ['admin.packages.*'],
        ],
        'manage.video-providers' => [
            'route' => 'admin.video-providers.index',
            'icon' => 'fas fa-server',
            'route_patterns' => ['admin.video-providers.*'],
        ],
        'manage.teacher-features' => [
            'route' => 'admin.teacher-features.index',
            'icon' => 'fas fa-chalkboard-teacher',
            'route_patterns' => ['admin.teacher-features.*'],
        ],
        'manage.curriculum-library' => [
            'route' => 'admin.curriculum-library.index',
            'icon' => 'fas fa-book-open',
            'route_patterns' => ['admin.curriculum-library.*'],
        ],
        'manage.popup-ads' => [
            'route' => 'admin.popup-ads.index',
            'icon' => 'fas fa-bullhorn',
            'route_patterns' => ['admin.popup-ads.*'],
        ],
        'manage.personal-branding' => [
            'route' => 'admin.personal-branding.index',
            'icon' => 'fas fa-user-tie',
            'route_patterns' => ['admin.personal-branding.*'],
        ],
        'manage.quality-control' => [
            'route' => 'admin.quality-control.index',
            'icon' => 'fas fa-shield-alt',
            'route_patterns' => ['admin.quality-control.*'],
        ],
        'manage.performance' => [
            'route' => 'admin.performance.index',
            'icon' => 'fas fa-tachometer-alt',
            'route_patterns' => ['admin.performance.*'],
        ],
        'manage.email-broadcasts' => [
            'route' => 'admin.email-broadcasts.index',
            'route_params' => ['audience' => 'students'],
            'icon' => 'fas fa-envelope',
            'route_patterns' => ['admin.email-broadcasts.*'],
        ],
        'manage.two-factor-logs' => [
            'route' => 'admin.two-factor-logs.index',
            'icon' => 'fas fa-lock',
            'route_patterns' => ['admin.two-factor-logs.*'],
        ],
        'manage.leaves' => [
            'route' => 'admin.leaves.index',
            'icon' => 'fas fa-calendar-alt',
            'route_patterns' => ['admin.leaves.*'],
        ],
        'manage.instructor-requests' => [
            'route' => 'admin.instructor-requests.index',
            'icon' => 'fas fa-inbox',
            'route_patterns' => ['admin.instructor-requests.*'],
        ],
        'view.reports' => [
            'route' => 'admin.reports.index',
            'icon' => 'fas fa-file-excel',
            'dedupe_key' => 'admin.reports.index',
            'route_patterns' => ['admin.reports.*'],
        ],
        'view.financial-reports' => [
            'route' => 'admin.reports.financial',
            'icon' => 'fas fa-file-invoice-dollar',
            'route_patterns' => ['admin.reports.financial', 'admin.reports.export.financial'],
        ],
        'view.academic-reports' => [
            'route' => 'admin.reports.academic',
            'icon' => 'fas fa-book',
            'route_patterns' => ['admin.reports.academic', 'admin.reports.export.academic'],
        ],
        'manage.leads' => [
            'route' => 'admin.sales.leads.index',
            'icon' => 'fas fa-user-plus',
            'route_patterns' => ['admin.sales.leads.*'],
        ],
        'view.sales-analytics' => [
            'route' => 'admin.sales.index',
            'icon' => 'fas fa-chart-line',
            'route_patterns' => ['admin.sales.*'],
        ],
        'manage.support-tickets' => [
            'route' => 'admin.support-tickets.index',
            'icon' => 'fas fa-headset',
            'dedupe_key' => 'admin.support-tickets.hub',
            'route_patterns' => ['admin.support-tickets.*', 'admin.support-inquiry-categories.*'],
        ],
        'manage.consultations' => [
            'route' => 'admin.consultations.index',
            'icon' => 'fas fa-comments-dollar',
            'route_patterns' => ['admin.consultations.*'],
        ],
        'manage.hiring-academies' => [
            'route' => 'admin.hiring-academies.index',
            'icon' => 'fas fa-school',
            'dedupe_key' => 'admin.hiring-academies.hub',
            'route_patterns' => ['admin.hiring-academies.*', 'admin.academy-opportunities.*'],
        ],
        'manage.students-accounts' => [
            'route' => 'admin.students-accounts.index',
            'icon' => 'fas fa-user-circle',
            'route_patterns' => ['admin.students-accounts.*'],
        ],
        'academic_supervision.manage' => [
            'route' => 'admin.academic-supervision.index',
            'icon' => 'fas fa-user-graduate',
            'route_patterns' => ['admin.academic-supervision.*'],
        ],
    ],
];
