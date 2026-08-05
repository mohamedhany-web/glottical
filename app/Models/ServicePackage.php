<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ServicePackage extends Model
{
    public const SCOPE_GLOBAL = 'global';

    public const SCOPE_TUTORING_INDIVIDUAL = 'tutoring_individual';

    public const SCOPE_TUTORING_COLLECTIVE = 'tutoring_collective';

    public const SCOPE_PRIVATE_LESSONS = 'private_lessons';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'badge',
        'scope',
        'tutoring_group_id',
        'units_count',
        'session_minutes',
        'duration_days',
        'price',
        'original_price',
        'currency',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'units_count' => 'integer',
            'session_minutes' => 'integer',
            'duration_days' => 'integer',
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function tutoringGroup(): BelongsTo
    {
        return $this->belongsTo(TutoringGroup::class);
    }

    public function entitlements(): HasMany
    {
        return $this->hasMany(StudentServiceEntitlement::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'service_package_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopePublicCatalog(Builder $query): Builder
    {
        return $query->active()->whereNull('tutoring_group_id');
    }

    public function label(): string
    {
        return match ($this->scope) {
            self::SCOPE_TUTORING_INDIVIDUAL => 'حصص فردية',
            self::SCOPE_TUTORING_COLLECTIVE => 'حصص جماعية / مدرسة',
            self::SCOPE_PRIVATE_LESSONS => 'حصص خاصة 1:1',
            default => 'عام (كل التدريس)',
        };
    }

    public function currencyCode(): string
    {
        return $this->currency ?: 'EGP';
    }

    public function formattedPrice(): string
    {
        if ((float) $this->price <= 0) {
            return app()->getLocale() === 'ar' ? 'مجاناً' : 'Free';
        }

        if ($this->currencyCode() === 'USD') {
            return '$'.number_format((float) $this->price, 2).' USD';
        }

        return number_format((float) $this->price, 2).' '.$this->currencyCode();
    }

    public function formattedOriginalPrice(): ?string
    {
        if (! $this->original_price || (float) $this->original_price <= (float) $this->price) {
            return null;
        }

        if ($this->currencyCode() === 'USD') {
            return '$'.number_format((float) $this->original_price, 2).' USD';
        }

        return number_format((float) $this->original_price, 2).' '.$this->currencyCode();
    }

    public function savingsAmount(): float
    {
        if (! $this->original_price) {
            return 0.0;
        }

        return max(0, (float) $this->original_price - (float) $this->price);
    }

    public function savingsPercent(): int
    {
        if (! $this->original_price || (float) $this->original_price <= 0) {
            return 0;
        }

        return (int) round($this->savingsAmount() / (float) $this->original_price * 100);
    }

    public function sessionMinutes(): int
    {
        return max(15, (int) ($this->session_minutes ?: 60));
    }

    public function totalMinutes(): int
    {
        return $this->sessionMinutes() * max(1, (int) $this->units_count);
    }

    /**
     * Total teaching hours in the pack, e.g. 8 sessions × 60min = 8 hours.
     */
    public function totalHoursLabel(): string
    {
        $hours = $this->totalMinutes() / 60;
        $value = fmod($hours, 1.0) === 0.0 ? (string) (int) $hours : number_format($hours, 1);

        return app()->getLocale() === 'ar' ? $value.' ساعة' : $value.' hours';
    }

    public function pricePerUnit(): float
    {
        $units = max(1, (int) $this->units_count);

        return round((float) $this->price / $units, 2);
    }

    public function formattedPricePerUnit(): string
    {
        if ((float) $this->price <= 0) {
            return app()->getLocale() === 'ar' ? 'مجاناً' : 'Free';
        }

        if ($this->currencyCode() === 'USD') {
            return '$'.number_format($this->pricePerUnit(), 2).' USD';
        }

        return number_format($this->pricePerUnit(), 2).' '.$this->currencyCode();
    }

    /**
     * Human validity window, e.g. "60 يوم (شهران)".
     */
    public function validityLabel(): string
    {
        $isRtl = app()->getLocale() === 'ar';

        if (! $this->duration_days) {
            return $isRtl ? 'بدون تاريخ انتهاء' : 'No expiry';
        }

        $days = (int) $this->duration_days;
        $months = (int) round($days / 30);

        if ($days < 30 || $months < 1) {
            return $isRtl ? $days.' يوم' : $days.' days';
        }

        $monthsLabel = $isRtl
            ? match (true) {
                $months === 1 => 'شهر',
                $months === 2 => 'شهران',
                $months <= 10 => $months.' أشهر',
                default => $months.' شهراً',
            }
        : $months.' months';

        return $isRtl
            ? $days.' يوم ('.$monthsLabel.')'
            : $days.' days ('.$monthsLabel.')';
    }

    /**
     * Suggested pace so the pack finishes before expiry, e.g. 4 sessions / month.
     */
    public function sessionsPerMonth(): ?float
    {
        if (! $this->duration_days || (int) $this->duration_days < 7) {
            return null;
        }

        $months = max(1, (int) round((int) $this->duration_days / 30));

        return round(max(1, (int) $this->units_count) / $months, 1);
    }

    public function scopeUsageHint(): string
    {
        $isRtl = app()->getLocale() === 'ar';

        return match ($this->scope) {
            self::SCOPE_TUTORING_INDIVIDUAL => $isRtl
                ? 'تُستخدم في حجز الحصص الفردية مع المعلم'
                : 'Used for one-on-one tutoring bookings',
            self::SCOPE_TUTORING_COLLECTIVE => $isRtl
                ? 'تُستخدم في حصص المجموعات ومواد المدرسة'
                : 'Used for group classes and school subjects',
            self::SCOPE_PRIVATE_LESSONS => $isRtl
                ? 'تُستخدم في الحصص الخاصة 1:1 داخل الكورسات'
                : 'Used for private 1:1 course sessions',
            default => $isRtl
                ? 'تُستخدم في أي خدمة تدريس: فردي، جماعي، أو خاص'
                : 'Works with any tutoring service: individual, group or private',
        };
    }

    public static function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = Str::slug($base) ?: 'package';
        $original = $slug;
        $i = 2;
        while (static::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $original.'-'.$i;
            $i++;
        }

        return $slug;
    }

    public static function scopes(): array
    {
        return [
            self::SCOPE_GLOBAL => 'عام (كل التدريس)',
            self::SCOPE_TUTORING_INDIVIDUAL => 'حصص فردية',
            self::SCOPE_TUTORING_COLLECTIVE => 'حصص جماعية / مدرسة',
            self::SCOPE_PRIVATE_LESSONS => 'حصص خاصة 1:1',
        ];
    }
}
