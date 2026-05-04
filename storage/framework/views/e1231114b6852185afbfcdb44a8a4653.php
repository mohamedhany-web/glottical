<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($broadcast->subject); ?></title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial, Helvetica, sans-serif;">
    <div style="max-width:720px;margin:0 auto;padding:24px;">
        <div style="background:#0f172a;color:#fff;padding:18px 20px;border-radius:14px;">
            <div style="font-size:12px;opacity:.85"><?php echo e(config('app.name')); ?></div>
            <div style="font-size:18px;font-weight:700;margin-top:6px;"><?php echo e($broadcast->subject); ?></div>
        </div>

        <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;margin-top:14px;padding:18px 20px;line-height:1.9;color:#0f172a;white-space:pre-wrap;"><?php echo e($broadcast->body); ?></div>

        <div style="margin-top:14px;color:#64748b;font-size:12px;text-align:center;">
            تم إرسال هذه الرسالة إليك لأن بريدك مسجل في <?php echo e(config('app.name')); ?>.
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\emails\broadcast.blade.php ENDPATH**/ ?>