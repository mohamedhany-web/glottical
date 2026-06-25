<?php

namespace App\Helpers;

class VideoHelper
{
    /**
     * تحويل رابط الفيديو إلى رابط قابل للتضمين (Bunny، YouTube، Vimeo).
     */
    public static function getEmbedUrl($url)
    {
        if (empty($url)) {
            return null;
        }

        $url = trim((string) $url);

        // Bunny.net (Bunny Stream)
        if (preg_match('/(?:iframe|player)\.mediadelivery\.net\/(embed|play)\/(\d+)\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $mode = $matches[1];
            $libraryId = $matches[2];
            $videoId = $matches[3];
            $base = "https://iframe.mediadelivery.net/{$mode}/{$libraryId}/{$videoId}";
            $parsed = parse_url($url);
            $query = isset($parsed['query']) ? '?'.$parsed['query'] : '';

            return $base.$query;
        }

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1].'?rel=0&modestbranding=1&playsinline=1';
        }

        if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $url, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1].'?title=0&byline=0&portrait=0';
        }

        return null;
    }

    /**
     * رابط فيديو مباشر (mp4/webm) للتشغيل عبر <video>.
     */
    public static function getDirectVideoUrl($url): ?string
    {
        if (empty($url) || self::getEmbedUrl($url)) {
            return null;
        }

        $url = trim((string) $url);
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        if (preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $url)) {
            return $url;
        }

        return null;
    }

    /**
     * تحديد نوع مصدر الفيديو
     */
    public static function getVideoSource($url)
    {
        if (empty($url)) {
            return 'unknown';
        }

        if (str_contains($url, 'mediadelivery.net')) {
            return 'bunny';
        }
        if (preg_match('/youtube\.com|youtu\.be/', $url)) {
            return 'youtube';
        }
        if (str_contains($url, 'vimeo.com')) {
            return 'vimeo';
        }
        if (self::getDirectVideoUrl($url)) {
            return 'direct';
        }

        return 'other';
    }

    /**
     * الحصول على صورة مصغرة للفيديو
     */
    public static function getThumbnail($url)
    {
        if (empty($url)) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
            $videoId = $matches[1];

            return "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
        }

        return null;
    }

    /**
     * التحقق من صحة رابط الفيديو
     */
    public static function isValidVideoUrl($url)
    {
        if (empty($url)) {
            return false;
        }

        return self::getEmbedUrl($url) !== null || self::getDirectVideoUrl($url) !== null;
    }

    /**
     * إنشاء كود HTML لتضمين الفيديو
     */
    public static function generateEmbedHtml($url, $width = '100%', $height = '400px')
    {
        $embedUrl = self::getEmbedUrl($url);
        $source = self::getVideoSource($url);
        $direct = self::getDirectVideoUrl($url);

        if ($embedUrl) {
            return "<iframe src='{$embedUrl}' width='{$width}' height='{$height}' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen' allowfullscreen class='w-full h-full' style='border: none;'></iframe>";
        }

        if ($direct) {
            return "<video width='{$width}' height='{$height}' class='w-full h-full' controls playsinline preload='metadata'><source src='{$direct}' type='video/mp4'>متصفحك لا يدعم تشغيل الفيديو.</video>";
        }

        return '<div class="bg-red-100 text-red-700 p-4 rounded-lg">رابط الفيديو غير صحيح أو غير مدعوم</div>';
    }
}
