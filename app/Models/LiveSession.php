<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LiveSession extends Model
{
    protected $fillable = [
        'course_id', 'instructor_id', 'server_id', 'title', 'description',
        'room_name', 'status', 'scheduled_at', 'started_at', 'ended_at',
        'duration_minutes', 'max_participants', 'is_recorded', 'allow_chat',
        'allow_screen_share', 'require_enrollment', 'mute_on_join',
        'video_off_on_join', 'password', 'settings',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_recorded' => 'boolean',
        'allow_chat' => 'boolean',
        'allow_screen_share' => 'boolean',
        'require_enrollment' => 'boolean',
        'mute_on_join' => 'boolean',
        'video_off_on_join' => 'boolean',
        'settings' => 'array',
        'max_participants' => 'integer',
        'duration_minutes' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (LiveSession $session) {
            if (empty($session->room_name)) {
                $session->room_name = static::generateRoomName($session->title);
            }
        });
    }

    public static function generateRoomName(?string $title = null): string
    {
        $slug = $title ? Str::slug($title, '-') : 'session';

        return Str::limit($slug, 30, '').'-'.Str::random(8);
    }

    // ======================== Relationships ========================

    public function course()
    {
        return $this->belongsTo(AdvancedCourse::class, 'course_id');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function server()
    {
        return $this->belongsTo(LiveServer::class, 'server_id');
    }

    public function attendance()
    {
        return $this->hasMany(SessionAttendance::class, 'session_id');
    }

    public function recordings()
    {
        return $this->hasMany(LiveRecording::class, 'session_id');
    }

    // ======================== Scopes ========================

    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeEnded($query)
    {
        return $query->where('status', 'ended');
    }

    public function scopeForCourse($query, int $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeForInstructor($query, int $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    /**
     * جلسات البث الظاهرة للطالب (كورسات / بث عام) — بدون بث الإدارة الداخلي.
     */
    public function scopeVisibleToStudents($query)
    {
        return $query
            ->where(function ($q) {
                $q->whereNull('description')
                    ->orWhere('description', 'not like', '%أنشأتها الإدارة%');
            })
            ->where('title', 'not like', 'بث إداري%')
            ->where(function ($q) {
                $q->whereNull('settings')
                    ->orWhere('settings', 'not like', '%"admin_only":true%')
                    ->orWhere('settings', 'not like', '%"admin_only": true%');
            });
    }

    // ======================== Helpers ========================

    /**
     * بث فوري/داخلي من لوحة الإدارة — ليس لواجهة الطالب.
     */
    public function isAdminOnlyBroadcast(): bool
    {
        if ((bool) data_get($this->settings, 'admin_only', false)) {
            return true;
        }

        $description = (string) ($this->description ?? '');
        if (str_contains($description, 'أنشأتها الإدارة')) {
            return true;
        }

        return str_starts_with(trim((string) ($this->title ?? '')), 'بث إداري');
    }

    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isEnded(): bool
    {
        return $this->status === 'ended';
    }

    public function start(): void
    {
        $this->update([
            'status' => 'live',
            'started_at' => now(),
        ]);
    }

    public function end(): void
    {
        $duration = 0;

        if ($this->started_at) {
            // نضمن دائماً مدة موجبة أو صفر (بدون قيم سالبة)
            $minutes = (int) $this->started_at->diffInMinutes(now());
            $duration = max(0, $minutes);
        }

        $this->update([
            'status' => 'ended',
            'ended_at' => now(),
            'duration_minutes' => $duration,
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function getLiveRoomUrl(): string
    {
        $domain = $this->server?->normalized_domain ?: LiveSetting::getLiveKitHost();

        return 'https://'.$domain.'/'.$this->room_name;
    }

    public function getAttendeeCount(): int
    {
        return $this->attendance()->distinct('user_id')->count('user_id');
    }

    public function getDurationForHumansAttribute(): string
    {
        if (! $this->duration_minutes) {
            return '—';
        }
        $h = intdiv($this->duration_minutes, 60);
        $m = $this->duration_minutes % 60;

        return ($h > 0 ? "{$h} ساعة " : '').($m > 0 ? "{$m} دقيقة" : '');
    }

    public function canUserJoin(User $user): bool
    {
        if ($user->id === $this->instructor_id) {
            return true;
        }
        if ($this->isAdminOnlyBroadcast()) {
            return false;
        }
        if (! $this->require_enrollment || ! $this->course_id) {
            return true;
        }

        return $this->course
            ->enrollments()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();
    }

    /** سبورة الطلاب في البث: قلم + ممحاة (ويد للتحريك) عند تفعيل المدرب */
    public function allowsStudentWhiteboard(): bool
    {
        return (bool) data_get($this->settings, 'allow_student_whiteboard', false);
    }
}
