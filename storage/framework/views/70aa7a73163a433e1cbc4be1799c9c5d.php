<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(__('admin.recruitment_print_packet')); ?> — <?php echo e($presentation->display_code); ?></title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Arial, sans-serif; margin: 0; padding: 24px; color: #0f172a; line-height: 1.6; }
        .head { border-bottom: 3px solid #4f46e5; padding-bottom: 16px; margin-bottom: 24px; }
        .logo { font-size: 12px; color: #64748b; letter-spacing: .1em; }
        h1 { margin: 8px 0 0; font-size: 22px; }
        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px; margin-top: 20px; }
        .box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-top: 20px; }
        .label { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 6px; }
        .profile { white-space: pre-wrap; font-size: 14px; }
        .foot { margin-top: 32px; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 12px; }
        @media print { body { padding: 12px; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom:16px;">
        <button type="button" onclick="window.print()" style="padding:10px 20px;border-radius:8px;background:#4f46e5;color:#fff;border:0;cursor:pointer;font-weight:bold;">طباعة / حفظ PDF</button>
    </div>
    <div class="head">
        <div class="logo"><?php echo e(config('app.name')); ?> — <?php echo e(__('admin.recruitment_print_packet')); ?></div>
        <h1><?php echo e($opportunity->title); ?></h1>
        <p style="margin:4px 0 0;color:#475569;"><?php echo e($opportunity->organization_name); ?></p>
    </div>
    <div class="meta">
        <div><span class="label"><?php echo e(__('admin.recruitment_code')); ?></span><strong><?php echo e($presentation->display_code); ?></strong></div>
        <div><span class="label">الاسم المعروض للأكاديمية</span><strong><?php echo e($presentation->displayNameForAcademy()); ?></strong></div>
    </div>
    <div class="box">
        <div class="label"><?php echo e(__('admin.recruitment_curated_profile')); ?></div>
        <div class="profile"><?php echo e($presentation->curated_public_profile); ?></div>
    </div>
    <?php if($opportunity->requirements): ?>
    <div class="box">
        <div class="label">متطلبات الفرصة (مرجع)</div>
        <div class="profile"><?php echo e($opportunity->requirements); ?></div>
    </div>
    <?php endif; ?>
    <div class="foot">
        وثيقة داخلية معدّة للمشاركة مع شريك أكاديمي — لا تشمل بيانات حساسة إلا بموافقة صريحة من المنصة.
        <?php echo e(now()->format('Y-m-d H:i')); ?>

    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\recruitment-desk\print-packet.blade.php ENDPATH**/ ?>