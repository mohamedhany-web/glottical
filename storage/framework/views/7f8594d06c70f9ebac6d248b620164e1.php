
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          canvas: '#f3f5f7',
          'canvas-muted': '#e8ecf0',
          surface: '#ffffff',
          ink: '#0b1220',
          'ink-soft': '#1c2738',
          muted: '#5b6577',
          line: '#d7dde6',
          accent: '#0f5c57',
          'accent-soft': '#e6f2f1',
          metal: '#b08d57',
          danger: '#b42318',
          success: '#067647',
        },
        boxShadow: {
          soft: '0 8px 30px rgba(11, 18, 32, 0.06)',
          lift: '0 18px 50px rgba(11, 18, 32, 0.1)',
        },
        borderRadius: {
          xl: '1rem',
          '2xl': '1rem',
          '3xl': '1.5rem',
        },
        fontFamily: {
          sans: ['"IBM Plex Sans Arabic"', 'Segoe UI', 'Tahoma', 'sans-serif'],
        },
      },
    },
  };
</script>
<?php
    $atheerCssCandidates = [
        resource_path('css/atheer.css'),
        public_path('css/atheer.css'),
    ];
    $atheerCssInline = '';
    foreach ($atheerCssCandidates as $atheerCssPath) {
        if (is_file($atheerCssPath)) {
            $atheerCssInline = (string) file_get_contents($atheerCssPath);
            break;
        }
    }
    if ($atheerCssInline !== '') {
        $atheerCssInline = preg_replace('/@import\s+url\([^)]+\)\s*;?/i', '', $atheerCssInline) ?? $atheerCssInline;
    }
?>
<?php if($atheerCssInline !== ''): ?>
<style id="atheer-css"><?php echo $atheerCssInline; ?></style>
<?php endif; ?>

<link rel="stylesheet" href="<?php echo e(route('assets.atheer.css')); ?>?v=<?php echo e(@filemtime(resource_path('css/atheer.css')) ?: time()); ?>">
<link rel="stylesheet" href="<?php echo e(versioned_asset('css/atheer.css')); ?>">
<?php /**PATH /Users/cityphone/Documents/glottical/resources/views/partials/atheer-head.blade.php ENDPATH**/ ?>