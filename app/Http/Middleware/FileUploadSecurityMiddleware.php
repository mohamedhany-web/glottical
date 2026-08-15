<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Services\SecurityService;
use Symfony\Component\HttpFoundation\Response;

class FileUploadSecurityMiddleware
{
    protected $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Handle an incoming request.
     * التحقق من أمان الملفات المرفوعة
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasFile('*')) {
            $files = $request->allFiles();
            
            foreach ($files as $key => $file) {
                // معالجة ملف واحد أو مصفوفة
                $fileArray = is_array($file) ? $file : [$file];
                
                foreach ($fileArray as $uploadedFile) {
                    if ($uploadedFile && $uploadedFile->isValid()) {
                        // التحقق من نوع الملف المسموح
                        $allowedMimes = $this->getAllowedMimes($key);
                        $maxSize = $this->getMaxSize($request, $key);
                        
                        $validation = $this->securityService->validateUploadedFile(
                            $uploadedFile,
                            $allowedMimes,
                            $maxSize
                        );

                        if (!$validation['valid']) {
                            $this->securityService->logSuspiciousActivity(
                                'Invalid File Upload',
                                $request,
                                'File: ' . $uploadedFile->getClientOriginalName() . ' - Errors: ' . implode(', ', $validation['errors'])
                            );
                            
                            return back()
                                ->withErrors([$key => $validation['errors']])
                                ->withInput();
                        }
                    }
                }
            }
        }

        return $next($request);
    }

    /**
     * الحصول على أنواع الملفات المسموحة حسب نوع الحقل
     */
    private function getAllowedMimes(string $fieldName): array
    {
        // أسماء حقول دقيقة أولاً — تجنّب مطابقة جزئية خاطئة (مثل id_document → document)
        $exactMap = [
            'photo' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'jpg', 'jpeg', 'png', 'gif', 'webp'],
            'id_document' => [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf',
                'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf',
            ],
            'certificate' => [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf',
                'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf',
            ],
            'intro_video' => ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime', 'mp4', 'webm', 'ogg', 'mov'],
        ];

        if (isset($exactMap[$fieldName])) {
            return $exactMap[$fieldName];
        }

        $mimeMap = [
            'hiring_upload' => [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf',
                'video/mp4', 'video/webm', 'video/ogg', 'video/quicktime', 'application/octet-stream',
                'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'mp4', 'webm', 'ogg', 'mov',
            ],
            // ملفات الموارد والرفع العام (تشمل Excel و Word و PDF والصور) + امتدادات للتحقق عند اختلاف MIME
            'file' => [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
                'application/pdf',
                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/octet-stream',
                'text/csv', 'text/plain', 'text/html', 'application/xhtml+xml',
                'application/zip', 'application/vnd.rar',
                'video/mp4', 'video/webm', 'video/ogg', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska',
                'audio/mpeg', 'audio/mp3',
                'xlsx', 'xls', 'csv', 'doc', 'docx', 'pdf', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'zip', 'rar', 'html', 'htm',
                'mp4', 'webm', 'mov', 'avi', 'mkv', 'm4v', 'mp3',
            ],
            'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'video' => ['video/mp4', 'video/webm', 'video/ogg'],
            // تسجيل شاشة المتصفح غالباً يخرج WebM وأحياناً MIME غير قياسي حسب المتصفح.
            'recording' => [
                'video/webm', 'video/mp4', 'video/ogg', 'audio/webm', 'audio/ogg', 'application/octet-stream',
                'webm', 'mp4', 'ogg',
            ],
            'document' => [
                'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/octet-stream',
                'text/csv',
            ],
            // ملفات HTML التعليمية (مناهج)
            'html' => ['text/html', 'application/xhtml+xml', 'html', 'htm'],
            'avatar' => ['image/jpeg', 'image/png', 'image/gif'],
            'thumbnail' => ['image/jpeg', 'image/png', 'image/gif'],
        ];

        foreach ($mimeMap as $key => $mimes) {
            if (stripos($fieldName, $key) !== false) {
                return $mimes;
            }
        }

        // أنواع افتراضية آمنة (تشمل Excel)
        return [
            'image/jpeg', 'image/png', 'image/gif', 'application/pdf',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/octet-stream',
            'text/csv', 'text/html', 'ppt', 'pptx',
        ];
    }

    /**
     * الحد الأعلى المسموح به في PHP (upload_max_filesize / post_max_size) — بدون سقف إضافي من التطبيق.
     */
    private function phpIniUploadMaxBytes(): int
    {
        $max = (int) UploadedFile::getMaxFilesize();

        return $max > 0 ? $max : 1073741824;
    }

    /**
     * الحصول على الحد الأقصى لحجم الملف
     */
    private function getMaxSize(Request $request, string $fieldName): int
    {
        $maxBytes = (int) config('upload_limits.max_upload_bytes', 40 * 1024 * 1024);

        $sizeMap = [
            'video' => 524288000,   // 500 MB
            'recording' => 1073741824, // 1 GB
            'image' => $maxBytes,
            'document' => $maxBytes,
            'file' => max($maxBytes, 200 * 1024 * 1024),
            'avatar' => $maxBytes,
            'thumbnail' => $maxBytes,
        ];

        foreach ($sizeMap as $key => $size) {
            if (stripos($fieldName, $key) !== false) {
                return $size;
            }
        }

        return $maxBytes;
    }
}
