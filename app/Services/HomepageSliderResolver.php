<?php

namespace App\Services;

use App\Models\AdvancedCourse;
use App\Models\HomepageSlider;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class HomepageSliderResolver
{
    private const STOCK_BACKGROUNDS = [
        'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=2400&q=82',
        'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=2400&q=82',
        'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=2400&q=82',
    ];

    /**
     * @param  Collection<int, AdvancedCourse>  $featuredCourses
     * @param  Collection<int, object>  $landingPaths
     * @return array<int, array<string, string>>
     */
    public function resolve(Collection $featuredCourses, Collection $landingPaths): array
    {
        $configured = HomepageSlider::query()
            ->activeNow()
            ->ordered()
            ->with(['course', 'academicYear'])
            ->get();

        if ($configured->isNotEmpty()) {
            $slides = $configured->map(fn (HomepageSlider $s, int $i) => $s->toHeroSpotlightArray($i))->values()->all();

            return $this->applyAccentBackgrounds($slides);
        }

        return $this->applyAccentBackgrounds(
            $this->buildLegacyFallback($featuredCourses, $landingPaths)
        );
    }

    /**
     * @param  Collection<int, AdvancedCourse>  $featuredCourses
     * @param  Collection<int, object>  $landingPaths
     * @return array<int, array<string, string>>
     */
    private function buildLegacyFallback(Collection $featuredCourses, Collection $landingPaths): array
    {
        $a = 'landing.academy';
        $featuredList = $featuredCourses->take(24);
        $heroSpotlight = [];

        $courseHeroBg = function ($course) {
            if (! $course) {
                return '';
            }

            return storage_public_url_stable($course->thumbnail) ?? '';
        };

        if ($featuredList->isNotEmpty()) {
            $c0 = $featuredList->get(0);
            $heroSpotlight[] = [
                'kicker' => __($a.'.stream_badge_course'),
                'title' => $c0->title,
                'sub' => Str::limit(strip_tags((string) ($c0->description ?? '')), 190) ?: __($a.'.stream_fallback_sub'),
                'bg' => $courseHeroBg($c0),
                'primary_url' => route('public.course.show', $c0->id),
                'primary_label' => __($a.'.stream_primary_play'),
                'secondary_url' => url('/').'#stream-paths',
                'secondary_label' => __($a.'.stream_explore_paths'),
            ];
        }

        if ($featuredList->count() > 1) {
            $c1 = $featuredList->get(1);
            $heroSpotlight[] = [
                'kicker' => __($a.'.stream_badge_trending'),
                'title' => $c1->title,
                'sub' => Str::limit(strip_tags((string) ($c1->description ?? '')), 190) ?: __($a.'.stream_fallback_sub'),
                'bg' => $courseHeroBg($c1),
                'primary_url' => route('public.course.show', $c1->id),
                'primary_label' => __($a.'.stream_primary_play'),
                'secondary_url' => route('public.courses'),
                'secondary_label' => __($a.'.stream_explore_paths'),
            ];
        }

        $pHero = $landingPaths->first();
        if ($pHero && count($heroSpotlight) < 3) {
            $heroSpotlight[] = [
                'kicker' => __($a.'.stream_badge_series'),
                'title' => $pHero->name,
                'sub' => Str::limit(strip_tags((string) ($pHero->description ?? '')), 190) ?: __($a.'.stream_paths_sub'),
                'bg' => $pHero->image_url ?? '',
                'primary_url' => route('public.courses'),
                'primary_label' => __($a.'.stream_continue'),
                'secondary_url' => route('register'),
                'secondary_label' => __($a.'.stream_join'),
            ];
        }

        return array_slice($heroSpotlight, 0, 12);
    }

    /**
     * @param  array<int, array<string, string>>  $slides
     * @return array<int, array<string, string>>
     */
    private function applyAccentBackgrounds(array $slides): array
    {
        foreach ($slides as $i => &$slide) {
            $slide['accent_bg'] = self::STOCK_BACKGROUNDS[$i % count(self::STOCK_BACKGROUNDS)];
            if (empty($slide['bg'])) {
                $slide['bg'] = $slide['accent_bg'];
            }
        }
        unset($slide);

        return $slides;
    }
}
