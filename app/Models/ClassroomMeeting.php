<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClassroomMeeting extends Model
{
    protected $fillable = [
        'user_id',
        'consultation_request_id',
        'one_to_one_session_id',
        'code',
        'room_name',
        'title',
        'scheduled_for',
        'planned_duration_minutes',
        'max_participants',
        'participants_peak',
        'started_at',
        'ended_at',
        'recording_disk',
        'recording_path',
        'recording_audio_path',
        'recording_mime_type',
        'recording_audio_mime_type',
        'recording_size',
        'recording_audio_size',
        'recording_duration_seconds',
        'recording_audio_duration_seconds',
        'recording_uploaded_at',
        'settings',
    ];

    protected $casts = [
        'max_participants' => 'integer',
        'participants_peak' => 'integer',
        'scheduled_for' => 'datetime',
        'planned_duration_minutes' => 'integer',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'recording_size' => 'integer',
        'recording_audio_size' => 'integer',
        'recording_duration_seconds' => 'integer',
        'recording_audio_duration_seconds' => 'integer',
        'recording_uploaded_at' => 'datetime',
        'settings' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function consultationRequest(): BelongsTo
    {
        return $this->belongsTo(ConsultationRequest::class, 'consultation_request_id');
    }

    public function oneToOneSession(): BelongsTo
    {
        return $this->belongsTo(OneToOneSession::class, 'one_to_one_session_id');
    }

    public function participants()
    {
        return $this->hasMany(ClassroomMeetingParticipant::class, 'classroom_meeting_id');
    }

    /** تقارير نصية عبر n8n مرتبطة بالتسجيل/التقرير الصوتي */
    public function aiReports(): HasMany
    {
        return $this->hasMany(ClassroomMeetingReport::class, 'classroom_meeting_id');
    }

    public function isLive(): bool
    {
        return $this->started_at && ! $this->ended_at;
    }

    /** سبورة الضيوف: قلم + ممحاة عند تفعيل منظم الاجتماع */
    public function allowsParticipantWhiteboard(): bool
    {
        return (bool) data_get($this->settings, 'allow_participant_whiteboard', false);
    }

    public static function generateCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public function hasBrowserRecording(): bool
    {
        return $this->hasRecordingMediaOnR2();
    }

    /** هل يوجد ملف تسجيل (فيديو و/أو صوت) مرفوع على قرص التسجيلات السحابي؟ */
    public function hasRecordingMediaOnR2(): bool
    {
        if ($this->recording_disk !== 'live_recordings_r2') {
            return false;
        }

        return ! empty($this->recording_path) || ! empty($this->recording_audio_path);
    }

    public function getRecordingDownloadUrlAttribute(): ?string
    {
        if (! $this->hasRecordingMediaOnR2() || empty($this->recording_path)) {
            return null;
        }

        try {
            return Storage::disk('live_recordings_r2')->temporaryUrl(
                $this->recording_path,
                now()->addHours(2)
            );
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getRecordingAudioDownloadUrlAttribute(): ?string
    {
        if (! $this->hasRecordingMediaOnR2() || empty($this->recording_audio_path)) {
            return null;
        }

        try {
            return Storage::disk('live_recordings_r2')->temporaryUrl(
                $this->recording_audio_path,
                now()->addHours(2)
            );
        } catch (\Throwable $e) {
            return null;
        }
    }
}
