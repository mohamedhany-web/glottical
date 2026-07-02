<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\FullAiSuitePreviewRequest;
use App\Services\FullAiSuiteContextService;
use App\Services\MuallimxAiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubscriptionFeatureController extends Controller
{
    /**
     * عرض صفحة الميزة المرتبطة بالاشتراك.
     * يتحقق من أن المستخدم لديه الميزة في باقته النشطة ثم يعرض الصفحة.
     */
    public function show(Request $request, string $feature)
    {
        $user = Auth::user();
        $config = config('student_subscription_features', []);

        if (! isset($config[$feature])) {
            abort(404);
        }

        if (! $user->hasSubscriptionFeature($feature)) {
            abort(403, 'هذه الميزة غير متاحة في باقتك الحالية. يمكنك ترقية اشتراكك من صفحة التسعير.');
        }

        if ($feature === 'ai_tools' && $user->hasSubscriptionFeature('full_ai_suite')) {
            return redirect()->route('student.features.show', ['feature' => 'full_ai_suite']);
        }

        if ($feature === 'classroom_access') {
            return redirect()->route('student.classroom.index');
        }
        if ($feature === 'support') {
            return redirect()->route('student.support.index');
        }
        if ($feature === 'visible_to_academies') {
            return redirect()->route('student.academies.visibility');
        }
        if ($feature === 'can_apply_opportunities') {
            return redirect()->route('student.opportunities.index');
        }
        if ($feature === 'direct_support') {
            return redirect()->route('student.support.index');
        }
        if ($feature === 'teacher_evaluation') {
            return redirect()->route('student.support.index')
                ->with('info', 'تقييم المعلم يتم بالتنسيق مع فريق المنصة عبر تذاكر الدعم.');
        }
        if (in_array($feature, ['recommended_to_academies', 'priority_opportunities'], true)) {
            if ($user->hasSubscriptionFeature('visible_to_academies')) {
                return redirect()->route('student.academies.visibility');
            }
            if ($user->hasSubscriptionFeature('can_apply_opportunities')) {
                return redirect()->route('student.opportunities.index');
            }
        }

        $featureConfig = $config[$feature];
        $label = __('student.subscription_feature.'.$feature);
        $description = __('student.subscription_feature_desc.'.$feature);

        if (in_array($feature, ['full_ai_suite', 'ai_tools'], true)) {
            // يُعرض اختيار الكورس فقط عند وجود تسجيلات نشطة؛ وإلا يُسمح بالمعاينة بدون كورس (سياق عام)
            $courses = $feature === 'full_ai_suite'
                ? $user->activeCourses()
                    ->select('advanced_courses.id', 'advanced_courses.title', 'advanced_courses.category')
                    ->get()
                : collect();
            $requiresCourseSelection = $feature === 'full_ai_suite' && $courses->isNotEmpty();

            $pageHint = $feature === 'ai_tools'
                ? __('student.full_ai_suite.form_subtitle_ai_tools')
                : ($requiresCourseSelection
                    ? __('student.full_ai_suite.form_subtitle_full')
                    : __('student.full_ai_suite.form_subtitle_full_no_active_course'));

            return view('student.features.full-ai-suite', [
                'feature' => $feature,
                'label' => $label,
                'description' => $description,
                'featureConfig' => $featureConfig,
                'courses' => $courses,
                'requiresCourseSelection' => $requiresCourseSelection,
                'pageHint' => $pageHint,
            ]);
        }

        return view('student.features.show', [
            'feature' => $feature,
            'label' => $label,
            'description' => $description,
            'featureConfig' => $featureConfig,
        ]);
    }

    /**
     * التحقق من البيانات وبناء السياق، واستدعاء Glottical AI عند التفعيل في الإعدادات.
     */
    public function previewFullAiSuite(
        FullAiSuitePreviewRequest $request,
        FullAiSuiteContextService $service,
        MuallimxAiClient $muallimxAi
    ) {
        $validated = $request->validated();
        $courseId = ! empty($validated['advanced_course_id'])
            ? (int) $validated['advanced_course_id']
            : null;

        $ctx = $service->buildContext(
            $request->user(),
            $courseId,
            $validated['question_type'],
            $validated['question']
        );

        $prompt = $service->buildPromptPreview($ctx);

        $gameHtmlUrl = null;
        $gameStoragePath = null;
        $isEducationalGame = ($validated['question_type'] ?? '') === 'educational_games';

        $muallimxAiText = null;
        $muallimxAiError = null;

        if ($isEducationalGame) {
            if ($muallimxAi->isConfigured()) {
                try {
                    $maxTok = (int) config('muallimx_ai.max_output_tokens_educational_game', config('muallimx_ai.max_output_tokens'));
                    $raw = $muallimxAi->generateFromPrompt($prompt, $maxTok);
                    $html = $service->extractStandaloneHtmlFromModelResponse($raw);
                    if ($html !== null && $html !== '') {
                        $gameStoragePath = 'ai-games/student-'.$request->user()->id
                            .'/game-'.now()->format('Ymd-His').'-'.Str::lower(Str::random(6)).'.html';
                        Storage::disk('public')->put($gameStoragePath, $html);
                        $storedUrl = Storage::disk('public')->url($gameStoragePath);
                        $relativePath = parse_url($storedUrl, PHP_URL_PATH) ?: '/storage/'.ltrim($gameStoragePath, '/');
                        $gameHtmlUrl = $request->getSchemeAndHttpHost().$relativePath;
                        $muallimxAiText = __('student.full_ai_suite.game_html_generated_notice');
                    } else {
                        Log::warning('Glottical AI educational game: response had no extractable HTML', [
                            'user_id' => $request->user()?->id,
                            'raw_preview' => mb_substr($raw, 0, 400),
                        ]);
                        $muallimxAiError = __('student.full_ai_suite.game_html_extraction_failed');
                    }
                } catch (\Throwable $e) {
                    Log::warning('Glottical AI generateFromPrompt failed (educational game)', [
                        'user_id' => $request->user()?->id,
                        'message' => $e->getMessage(),
                    ]);
                    $muallimxAiError = $muallimxAi->userFacingErrorMessage($e);
                }
            }
            if ($gameHtmlUrl === null && ! $muallimxAi->isConfigured()) {
                $html = $service->buildEducationalGameHtml($ctx);
                $gameStoragePath = 'ai-games/student-'.$request->user()->id
                    .'/game-'.now()->format('Ymd-His').'-'.Str::lower(Str::random(6)).'.html';
                Storage::disk('public')->put($gameStoragePath, $html);
                $storedUrl = Storage::disk('public')->url($gameStoragePath);
                $relativePath = parse_url($storedUrl, PHP_URL_PATH) ?: '/storage/'.ltrim($gameStoragePath, '/');
                $gameHtmlUrl = $request->getSchemeAndHttpHost().$relativePath;
                $muallimxAiText = __('student.full_ai_suite.game_static_fallback_notice');
            }
        } elseif ($muallimxAi->isConfigured()) {
            try {
                $muallimxAiText = $muallimxAi->generateFromPrompt($prompt);
            } catch (\Throwable $e) {
                Log::warning('Glottical AI generateFromPrompt failed', [
                    'user_id' => $request->user()?->id,
                    'message' => $e->getMessage(),
                ]);
                $muallimxAiError = $muallimxAi->userFacingErrorMessage($e);
            }
        }

        return back()
            ->withInput($request->only(['advanced_course_id', 'question_type', 'question']))
            ->with('full_ai_preview', [
                'context' => $ctx,
                'prompt' => $prompt,
                'game_html_url' => $gameHtmlUrl,
                'game_storage_path' => $gameStoragePath,
                'muallimx_ai_text' => $muallimxAiText,
                'muallimx_ai_error' => $muallimxAiError,
            ]);
    }
}
