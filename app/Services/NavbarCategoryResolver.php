<?php

namespace App\Services;

use App\Models\AcademicSubject;
use App\Models\AdvancedCourse;
use App\Models\CourseCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * عناصر قائمة «التصنيفات» في النافبار — من قاعدة البيانات وليس نصوصاً ثابتة.
 */
class NavbarCategoryResolver
{
    /** @var array<int, string> */
    private const ICONS = [
        'fa-language',
        'fa-graduation-cap',
        'fa-book-open',
        'fa-chalkboard-teacher',
        'fa-laptop-code',
        'fa-briefcase',
        'fa-microphone-lines',
        'fa-palette',
        'fa-globe',
        'fa-certificate',
    ];

    /**
     * @return Collection<int, array{name: string, desc: string, icon: string, url: string, count: int, thumb_url: ?string}>
     */
    public static function megaMenuItems(int $limit = 8): Collection
    {
        $cacheKey = 'navbar_mega_categories_v2_'.app()->getLocale().'_'.$limit;

        return Cache::remember($cacheKey, 300, function () use ($limit) {
            $fromCategories = self::fromCourseCategories($limit, requireCourses: true);
            if ($fromCategories->isNotEmpty()) {
                return $fromCategories;
            }

            $fromSubjects = self::fromAcademicSubjects($limit);
            if ($fromSubjects->isNotEmpty()) {
                return $fromSubjects;
            }

            return self::fallbackItems($limit);
        });
    }

    /**
     * شبكة صفحة التصنيفات العامة — كل التصنيفات النشطة من الموقع.
     *
     * @return Collection<int, array{name: string, desc: string, icon: string, url: string, count: int, thumb_url: ?string}>
     */
    public static function catalogPageItems(int $limit = 48): Collection
    {
        $cacheKey = 'categories_page_catalog_v1_'.app()->getLocale().'_'.$limit;

        return Cache::remember($cacheKey, 300, function () use ($limit) {
            $fromCategories = self::fromCourseCategories($limit, requireCourses: false);
            if ($fromCategories->isNotEmpty()) {
                return $fromCategories;
            }

            $fromSubjects = self::fromAcademicSubjects($limit);
            if ($fromSubjects->isNotEmpty()) {
                return $fromSubjects;
            }

            return self::fallbackItems($limit);
        });
    }

    /** @return Collection<int, array{name: string, desc: string, icon: string, url: string, count: int, thumb_url: ?string}> */
    private static function fromCourseCategories(int $limit, bool $requireCourses = true): Collection
    {
        return CourseCategory::query()
            ->active()
            ->ordered()
            ->withCount([
                'advancedCourses as active_courses_count' => fn ($q) => $q->where('is_active', true),
            ])
            ->with([
                'advancedCourses' => fn ($q) => $q
                    ->where('is_active', true)
                    ->whereNotNull('thumbnail')
                    ->orderByDesc('is_featured')
                    ->orderByDesc('created_at')
                    ->limit(1)
                    ->select(['id', 'course_category_id', 'thumbnail']),
            ])
            ->get()
            ->when($requireCourses, fn (Collection $rows) => $rows->filter(
                fn (CourseCategory $cat) => (int) $cat->active_courses_count > 0
            ))
            ->take($limit)
            ->values()
            ->map(function (CourseCategory $cat, int $index) {
                $count = (int) $cat->active_courses_count;
                $sample = $cat->advancedCourses->first();
                $thumb = $sample?->thumbnail;

                return [
                    'name' => $cat->name,
                    'desc' => self::coursesCountLabel($count),
                    'icon' => self::iconForIndex($index),
                    'url' => route('public.courses', ['category' => $cat->id]),
                    'count' => $count,
                    'thumb_url' => $thumb ? storage_public_url($thumb) : null,
                ];
            });
    }

    /** @return Collection<int, array{name: string, desc: string, icon: string, url: string, count: int, thumb_url: ?string}> */
    private static function fromAcademicSubjects(int $limit): Collection
    {
        return AcademicSubject::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->withCount([
                'advancedCourses as active_courses_count' => fn ($q) => $q->where('is_active', true),
            ])
            ->with([
                'advancedCourses' => fn ($q) => $q
                    ->where('is_active', true)
                    ->whereNotNull('thumbnail')
                    ->orderByDesc('is_featured')
                    ->orderByDesc('created_at')
                    ->limit(1)
                    ->select(['id', 'academic_subject_id', 'thumbnail']),
            ])
            ->get()
            ->filter(fn (AcademicSubject $subject) => (int) $subject->active_courses_count > 0)
            ->take($limit)
            ->values()
            ->map(function (AcademicSubject $subject, int $index) {
                $count = (int) $subject->active_courses_count;
                $sample = $subject->advancedCourses->first();
                $thumb = $sample?->thumbnail;

                return [
                    'name' => $subject->name,
                    'desc' => self::coursesCountLabel($count),
                    'icon' => self::iconForIndex($index),
                    'url' => route('public.courses', ['subject' => $subject->id]),
                    'count' => $count,
                    'thumb_url' => $thumb ? storage_public_url($thumb) : null,
                ];
            });
    }

    /** @return Collection<int, array{name: string, desc: string, icon: string, url: string, count: int, thumb_url: ?string}> */
    private static function fallbackItems(int $limit): Collection
    {
        $total = AdvancedCourse::query()->where('is_active', true)->count();

        return collect([
            [
                'name' => __('public.nav_category_all_courses'),
                'desc' => self::coursesCountLabel($total),
                'icon' => 'fa-graduation-cap',
                'url' => route('public.courses'),
                'count' => $total,
                'thumb_url' => null,
            ],
        ])->take($limit);
    }

    private static function coursesCountLabel(int $count): string
    {
        return trans_choice('public.nav_category_courses_count', $count, ['count' => $count]);
    }

    private static function iconForIndex(int $index): string
    {
        return self::ICONS[$index % count(self::ICONS)];
    }
}
