<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CurriculumPresentationDerivative extends Model
{
    public const SOURCE_MATERIAL = 'material';

    public const SOURCE_FILE = 'file';

    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_READY = 'ready';

    public const STATUS_FAILED = 'failed';

    public const STATUS_UNAVAILABLE = 'unavailable';

    public const STATUS_STALE = 'stale';

    protected $fillable = [
        'source_type',
        'source_id',
        'storage_disk',
        'manifest_path',
        'status',
        'slide_count',
        'width',
        'height',
        'version',
        'source_checksum',
        'error_message',
        'engine',
        'ready_at',
    ];

    protected $casts = [
        'slide_count' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'version' => 'integer',
        'ready_at' => 'datetime',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(CurriculumLibraryMaterial::class, 'source_id');
    }

    public function isReady(): bool
    {
        return $this->status === self::STATUS_READY;
    }

    public function isRetryable(): bool
    {
        return in_array($this->status, [
            self::STATUS_FAILED,
            self::STATUS_UNAVAILABLE,
            self::STATUS_STALE,
        ], true);
    }

    public static function safeErrorSummary(?string $message): ?string
    {
        $message = trim((string) $message);
        if ($message === '') {
            return null;
        }

        $message = preg_replace('~https?://\S+~iu', '[رابط محجوب]', $message) ?? $message;
        $message = preg_replace('~(?:[A-Za-z]:\\\\|/)(?:[^\s:]+[/\\\\])+[^\s:]*~u', '[مسار محجوب]', $message) ?? $message;
        $message = preg_replace('~curriculum-library/(?:materials|derivatives)/\S+~iu', '[مسار محجوب]', $message) ?? $message;
        $message = preg_replace('/\s+/u', ' ', $message) ?? $message;

        return Str::limit($message, 180, '…');
    }

    public function derivativePrefix(): string
    {
        return sprintf(
            'curriculum-library/derivatives/%s/%d/v%d',
            $this->source_type,
            (int) $this->source_id,
            max(1, (int) $this->version)
        );
    }
}
