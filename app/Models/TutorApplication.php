<?php

namespace App\Models;

use App\Services\TutorApplicationStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutorApplication extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_ACTIVATED = 'activated';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'nationality',
        'city',
        'gender',
        'headline',
        'bio',
        'experience',
        'education',
        'years_experience',
        'photo_path',
        'id_document_path',
        'certificate_path',
        'intro_video_path',
        'intro_video_url',
        'status',
        'admin_notes',
        'reviewed_at',
        'reviewed_by',
        'user_id',
        'activated_at',
        'activated_by',
    ];

    protected function casts(): array
    {
        return [
            'years_experience' => 'integer',
            'reviewed_at' => 'datetime',
            'activated_at' => 'datetime',
        ];
    }

    public function reviewedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function activatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeAwaitingActivation($query)
    {
        return $query->where('status', self::STATUS_APPROVED)->whereNotNull('user_id');
    }

    public function scopeActivated($query)
    {
        return $query->where('status', self::STATUS_ACTIVATED);
    }

    public function isActivated(): bool
    {
        return $this->status === self::STATUS_ACTIVATED;
    }

    public function canActivateAccount(): bool
    {
        return $this->status === self::STATUS_APPROVED
            && $this->user_id !== null;
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT => 'مسودة — لم يُكمل البيانات',
            self::STATUS_PENDING => 'قيد المراجعة',
            self::STATUS_APPROVED => 'مقبول — بانتظار التفعيل العام',
            self::STATUS_ACTIVATED => 'مفعّل (ظاهر للعامة)',
            self::STATUS_REJECTED => 'مرفوض',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function photoUrl(): ?string
    {
        return TutorApplicationStorage::publicUrl($this->photo_path);
    }

    public function idDocumentUrl(): ?string
    {
        return TutorApplicationStorage::publicUrl($this->id_document_path);
    }

    public function certificateUrl(): ?string
    {
        return TutorApplicationStorage::publicUrl($this->certificate_path);
    }

    public function introVideoFileUrl(): ?string
    {
        return TutorApplicationStorage::publicUrl($this->intro_video_path);
    }

    public function introVideoDisplayUrl(): ?string
    {
        if (filled($this->intro_video_url)) {
            return $this->intro_video_url;
        }

        return $this->introVideoFileUrl();
    }

    public function idDocumentIsPdf(): bool
    {
        return TutorApplicationStorage::isPdf($this->id_document_path);
    }

    public function certificateIsPdf(): bool
    {
        return TutorApplicationStorage::isPdf($this->certificate_path);
    }
}
