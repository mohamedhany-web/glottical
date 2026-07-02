<?php

namespace App\Services;

use App\Models\AdvancedCourse;
use App\Models\User;

/**
 * طبقة السياق والقالب للمزايا التعليمية فقط — بدون استدعاء Glottical AI من هنا
 * (يُبنى نص الطلب للنموذج؛ الاستدعاء من المتحكم).
 */
class FullAiSuiteContextService
{
    /** مفاتيح ثابتة للـ API لاحقاً */
    public const QUESTION_TYPES = [
        'educational_tips' => 'student.full_ai_suite.question_types.educational_tips',
        'educational_games' => 'student.full_ai_suite.question_types.educational_games',
        'interactive_file_creation' => 'student.full_ai_suite.question_types.interactive_file_creation',
    ];

    /** @return list<string> */
    public static function questionTypeKeys(): array
    {
        return array_keys(self::QUESTION_TYPES);
    }

    public function filterQuestion(string $raw): string
    {
        $t = trim(preg_replace('/\s+/u', ' ', $raw));

        return mb_substr($t, 0, 4000);
    }

    public function buildContext(User $user, ?int $courseId, string $questionType, string $question): array
    {
        $filtered = $this->filterQuestion($question);
        $optionDescription = $this->buildOptionDescription($questionType, $filtered);

        $coursePayload = [
            'id' => null,
            'title' => null,
            'category' => null,
        ];
        if ($courseId !== null && $courseId > 0) {
            $course = AdvancedCourse::query()->findOrFail($courseId);
            $coursePayload = [
                'id' => $course->id,
                'title' => $course->title,
                'category' => $course->category,
            ];
        }

        return [
            'locale' => app()->getLocale(),
            'student_id' => $user->id,
            'course' => $coursePayload,
            'question_type' => $questionType,
            'question_type_label' => __(self::QUESTION_TYPES[$questionType] ?? $questionType),
            'question' => $filtered,
            'option_description' => $optionDescription,
            'filters_applied' => [
                'educational_only' => true,
                'max_question_length' => 4000,
            ],
        ];
    }

    /**
     * معاينة النص المُرسَل إلى Glottical AI. لنوع «ألعاب تعليمية» يكون النص تعليمات توليد ملف HTML كامل من وصف الطالب.
     *
     * @param  array<string, mixed>  $context
     */
    public function buildPromptPreview(array $context): string
    {
        if (($context['question_type'] ?? '') === 'educational_games') {
            return $this->buildEducationalGameGenerationPrompt($context);
        }

        $courseTitle = $context['course']['title'] ?? '';
        if ($courseTitle === null || $courseTitle === '') {
            $courseTitle = app()->getLocale() === 'ar' ? 'بدون كورس محدد' : 'No specific course';
        }
        $category = $context['course']['category'] ?? null;
        if ($category === null || $category === '') {
            $category = '—';
        }
        $typeLabel = $context['question_type_label'] ?? ($context['question_type'] ?? '');
        $body = $context['question'] ?? '';

        $lines = [
            '[SYSTEM — educational assistant only]',
            'You are an educational assistant in the Glottical learning context. Refuse non-educational or harmful requests.',
            '',
            '[CONTEXT]',
            '- Course: '.$courseTitle,
            '- Course type / category: '.$category,
            '- Request type: '.$typeLabel,
            '- Option description: '.($context['option_description'] ?? ''),
            '',
            '[STUDENT REQUEST]',
            $body,
            '',
            '[OUTPUT]',
            'Respond appropriately for the selected request type, in the student\'s language when possible.',
        ];

        return implode("\n", $lines);
    }

    public function buildOptionDescription(string $questionType, string $question): string
    {
        return match ($questionType) {
            'educational_tips' => 'Provide practical, step-by-step educational tips tailored to this request: '.$question,
            'educational_games' => 'Glottical AI should output one complete standalone HTML5 mini-game or interactive activity that strictly follows the student description (theme, rules, age, language). Student wrote: '.$question,
            'interactive_file_creation' => 'Create a structured interactive learning file content tailored to this request: '.$question,
            default => 'Educational support response: '.$question,
        };
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function buildEducationalGameHtml(array $context): string
    {
        $title = (string) ($context['course']['title'] ?? '');
        if ($title === '') {
            $title = app()->getLocale() === 'ar' ? 'نشاط تعليمي' : 'Educational activity';
        }
        $requestText = (string) ($context['question'] ?? '');
        $locale = (string) ($context['locale'] ?? 'ar');
        $isAr = $locale === 'ar';
        $studentId = (int) ($context['student_id'] ?? 0);
        $storageKey = 'mx_edu_'.hash('crc32b', $studentId.'|'.$title.'|'.$requestText);

        $letters = config('arabic_alphabet_game.letters', []);
        $letterKeys = array_column($letters, 'letter');
        $userByLetter = $this->extractUserWordsByFirstArabicLetter($requestText, $letterKeys);

        $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $safeRequest = htmlspecialchars($requestText, ENT_QUOTES, 'UTF-8');
        $skJson = json_encode($storageKey, JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        $lettersJson = json_encode($letters, JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
        $userByLetterJson = json_encode($userByLetter, JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);

        $htmlLang = $isAr ? 'ar' : 'en';
        $dir = $isAr ? 'rtl' : 'ltr';
        $pageTitle = $isAr ? ('تعلّم الحروف العربية — '.$title) : ('Arabic letters — '.$title);
        $safePageTitle = htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8');

        $introTitle = $isAr ? 'لعبة الحروف العربية' : 'Arabic alphabet activity';
        $introBody = $isAr
            ? 'اضغط على أي حرف في الشبكة لعرض اسمه وأمثلة كلمات. إذا كتبت في الوصف كلمات عربية تبدأ بحرف معيّن، تظهر تحت «من وصفك» إن وُجدت.'
            : 'Tap a letter to see its name and example words. If your description contains Arabic words starting with that letter, they appear under “From your text”.';

        $h2Grid = $isAr ? 'جميع الحروف — اضغط على حرف' : 'All letters — tap one';
        $panelTitle = $isAr ? 'تفاصيل الحرف' : 'Letter details';
        $pickFirst = $isAr ? 'اختر حرفاً من الشبكة أعلاه.' : 'Pick a letter from the grid above.';
        $fromUser = $isAr ? 'من وصفك (نفس الحرف في أول الكلمة)' : 'From your description (same first letter)';
        $noUserWords = $isAr ? 'لا توجد كلمات في وصفك تبدأ بهذا الحرف — جرّب إضافة كلمات في المعاينة القادمة.' : 'No words in your description start with this letter.';
        $examplesTitle = $isAr ? 'أمثلة تعليمية' : 'Teaching examples';
        $tip = $isAr ? 'نصيحة: اقرأ المثال بصوت عالٍ ولاحظ شكل الحرف في أول الكلمة.' : 'Tip: read the example aloud and notice the letter at the start.';
        $ctxLabel = $isAr ? 'السياق' : 'Context';
        $descLabel = $isAr ? 'وصفك' : 'Your description';

        return <<<HTML
<!doctype html>
<html lang="{$htmlLang}" dir="{$dir}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{$safePageTitle}</title>
  <style>
    * { box-sizing: border-box; }
    body { font-family: "Segoe UI", Tahoma, Arial, sans-serif; margin: 0; padding: 16px; background: #0f172a; color: #e2e8f0; min-height: 100vh; }
    .wrap { max-width: 820px; margin: 0 auto; }
    .card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 18px; margin-bottom: 16px; }
    h1 { margin: 0 0 8px; font-size: 1.25rem; color: #f8fafc; }
    .badge { display: inline-block; background: #422006; color: #fcd34d; padding: 5px 10px; border-radius: 999px; font-size: 0.7rem; margin-bottom: 8px; }
    .muted { color: #94a3b8; font-size: 0.88rem; line-height: 1.65; margin: 6px 0; }
    h2 { margin: 0 0 12px; font-size: 1.05rem; color: #38bdf8; }
    .letters-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(52px, 1fr));
      gap: 8px;
    }
    .letter-btn {
      font-size: 1.45rem;
      font-weight: 800;
      padding: 12px 6px;
      border-radius: 12px;
      border: 1px solid #475569;
      background: linear-gradient(180deg, #334155, #1e293b);
      color: #f1f5f9;
      cursor: pointer;
      transition: transform 0.15s, border-color 0.15s, box-shadow 0.15s;
    }
    .letter-btn:hover { transform: translateY(-2px); border-color: #38bdf8; box-shadow: 0 6px 16px rgba(14,165,233,0.25); }
    .letter-btn.active { border-color: #fbbf24; box-shadow: 0 0 0 2px rgba(251,191,36,0.35); background: linear-gradient(180deg, #1d4ed8, #1e3a5f); }
    .panel { min-height: 140px; }
    .big { font-size: 3.2rem; font-weight: 900; color: #fcd34d; line-height: 1.1; margin-bottom: 4px; }
    .name { font-size: 1.1rem; color: #7dd3fc; margin-bottom: 12px; }
    .subh { font-size: 0.85rem; font-weight: 700; color: #a5b4fc; margin: 10px 0 6px; }
    ul.ex { margin: 0; padding: 0 1.1rem 0 0; list-style: disc; }
    ul.ex li { margin: 6px 0; font-size: 0.95rem; }
    .word { font-weight: 700; color: #fef08a; }
    .hint { color: #94a3b8; font-size: 0.85rem; }
    .tip { margin-top: 14px; padding: 10px 12px; border-radius: 10px; background: rgba(15,118,110,0.25); border: 1px solid #14b8a6; color: #99f6e4; font-size: 0.85rem; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <span class="badge">Glottical</span>
      <h1>{$safePageTitle}</h1>
      <p class="muted"><strong>{$introTitle}</strong> — {$introBody}</p>
      <p class="muted"><strong>{$ctxLabel}:</strong> {$safeTitle}</p>
      <p class="muted"><strong>{$descLabel}:</strong> {$safeRequest}</p>
    </div>

    <div class="card">
      <h2>{$h2Grid}</h2>
      <div class="letters-grid" id="letterGrid"></div>
    </div>

    <div class="card panel" id="panel">
      <h2>{$panelTitle}</h2>
      <p class="muted" id="placeholder">{$pickFirst}</p>
      <div id="detail" style="display:none">
        <div class="big" id="dLetter"></div>
        <div class="name" id="dName"></div>
        <div class="subh" id="userSub" style="display:none">{$fromUser}</div>
        <ul class="ex" id="userList" style="display:none"></ul>
        <p class="muted" id="userEmpty" style="display:none">{$noUserWords}</p>
        <div class="subh">{$examplesTitle}</div>
        <ul class="ex" id="exList"></ul>
      </div>
      <p class="tip">{$tip}</p>
    </div>
  </div>

  <script>
(function () {
  var SK = {$skJson};
  var LETTERS = {$lettersJson};
  var USER = {$userByLetterJson};
  var grid = document.getElementById('letterGrid');
  var placeholder = document.getElementById('placeholder');
  var detail = document.getElementById('detail');
  var dLetter = document.getElementById('dLetter');
  var dName = document.getElementById('dName');
  var userSub = document.getElementById('userSub');
  var userList = document.getElementById('userList');
  var userEmpty = document.getElementById('userEmpty');
  var exList = document.getElementById('exList');
  var activeBtn = null;

  function saveProgress(letter) {
    try {
      var raw = localStorage.getItem(SK + ':letters') || '{}';
      var o = JSON.parse(raw);
      o[letter] = (o[letter] || 0) + 1;
      localStorage.setItem(SK + ':letters', JSON.stringify(o));
    } catch (e) {}
  }

  function showLetter(row) {
    if (activeBtn) activeBtn.classList.remove('active');
    var btn = grid.querySelector('[data-letter="' + row.letter + '"]');
    if (btn) { btn.classList.add('active'); activeBtn = btn; }
    placeholder.style.display = 'none';
    detail.style.display = 'block';
    dLetter.textContent = row.letter;
    dName.textContent = row.name;
    var uw = USER[row.letter] || [];
    userList.innerHTML = '';
    if (uw.length) {
      userSub.style.display = 'block';
      userList.style.display = 'block';
      userEmpty.style.display = 'none';
      uw.forEach(function (w) {
        var li = document.createElement('li');
        li.innerHTML = '<span class="word"></span>';
        li.querySelector('.word').textContent = w;
        userList.appendChild(li);
      });
    } else {
      userSub.style.display = 'none';
      userList.style.display = 'none';
      userEmpty.style.display = 'block';
    }
    exList.innerHTML = '';
    (row.examples || []).forEach(function (ex) {
      var li = document.createElement('li');
      var w = document.createElement('span');
      w.className = 'word';
      w.textContent = ex.word;
      li.appendChild(w);
      if (ex.hint) {
        li.appendChild(document.createTextNode(' — '));
        var h = document.createElement('span');
        h.className = 'hint';
        h.textContent = ex.hint;
        li.appendChild(h);
      }
      exList.appendChild(li);
    });
    saveProgress(row.letter);
  }

  LETTERS.forEach(function (row) {
    var b = document.createElement('button');
    b.type = 'button';
    b.className = 'letter-btn';
    b.setAttribute('data-letter', row.letter);
    b.setAttribute('aria-label', row.name);
    b.textContent = row.letter;
    b.addEventListener('click', function () { showLetter(row); });
    grid.appendChild(b);
  });
})();
  </script>
</body>
</html>
HTML;
    }

    /**
     * كلمات من نص الطالب حسب أول حرف (إن طابق أحد حروف اللعبة).
     *
     * @param  list<string>  $allowedFirstLetters
     * @return array<string, list<string>>
     */
    private function extractUserWordsByFirstArabicLetter(string $text, array $allowedFirstLetters): array
    {
        $by = [];
        foreach ($allowedFirstLetters as $L) {
            $by[$L] = [];
        }
        $text = trim(preg_replace('/\s+/u', ' ', $text));
        if ($text === '') {
            return $by;
        }
        $allowed = array_flip($allowedFirstLetters);
        $parts = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        foreach ($parts as $w) {
            $w = preg_replace('/[^\p{Arabic}\p{L}\p{N}]+/u', '', $w) ?? '';
            if (mb_strlen($w) < 2) {
                continue;
            }
            $f = mb_substr($w, 0, 1);
            $f = match ($f) {
                'ا', 'إ', 'آ' => 'أ',
                default => $f,
            };
            if (! isset($allowed[$f])) {
                continue;
            }
            if (count($by[$f]) >= 6) {
                continue;
            }
            $by[$f][] = $w;
        }

        return $by;
    }

    /**
     * تعليمات التوليد: ملف HTML واحد كامل يعكس وصف الطالب (يُعرض في «قالب الطلب» ويُرسل للنموذج).
     *
     * @param  array<string, mixed>  $context
     */
    public function buildEducationalGameGenerationPrompt(array $context): string
    {
        $courseTitle = (string) ($context['course']['title'] ?? '');
        if ($courseTitle === '') {
            $courseTitle = app()->getLocale() === 'ar' ? 'بدون كورس محدد' : 'No specific course';
        }
        $category = (string) ($context['course']['category'] ?? '');
        if ($category === '') {
            $category = '—';
        }
        $locale = (string) ($context['locale'] ?? 'ar');
        $studentRequest = (string) ($context['question'] ?? '');
        $studentRequest = trim(preg_replace('/\s+/u', ' ', $studentRequest));

        $langLine = $locale === 'ar'
            ? 'Primary UI language: Arabic (ar). Use dir="rtl" on <html> when the activity is Arabic.'
            : 'Primary UI language: English (en) unless the student request clearly asks for Arabic.';

        return implode("\n", [
            '[SYSTEM — Glottical educational HTML generator]',
            'You build ONE complete, self-contained HTML5 document for a browser-only educational mini-game or interactive learning activity.',
            'Refuse harmful or non-educational requests; keep content classroom-safe.',
            '',
            '[OUTPUT FORMAT — mandatory]',
            'Return exactly ONE full HTML document.',
            'Preferred: wrap the entire file in a single fenced block: ```html on its own line, then the document, then ``` on its own line.',
            'Alternative: raw HTML starting with <!DOCTYPE html> or <html> through </html> with nothing before the doctype/html opener.',
            'Do NOT add explanations, markdown headings, or commentary outside that HTML (except the optional ```html fence lines).',
            '',
            '[TECHNICAL RULES]',
            '- Inline <style> and <script> only; no external script URLs. Optional: fonts via https Google Fonts only if needed.',
            '- The game mechanics, copy, colours, and structure MUST follow [STUDENT_REQUEST] below (not a generic unrelated template).',
            '- Mobile-friendly: <meta name="viewport" content="width=device-width, initial-scale=1">.',
            '- Accessible: keyboard-focus visible, buttons type="button", meaningful page <title>.',
            '- Do not execute arbitrary user-provided strings as code; treat [STUDENT_REQUEST] as content requirements only.',
            $langLine,
            '',
            '[CONTEXT]',
            '- Course title: '.$courseTitle,
            '- Course category: '.$category,
            '- Preferred locale hint: '.$locale,
            '',
            '[STUDENT_REQUEST]',
            $studentRequest,
        ]);
    }

    /**
     * استخراج HTML من رد النموذج (سياج markdown أو نص خام).
     */
    public function extractStandaloneHtmlFromModelResponse(string $raw): ?string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        $maxBytes = 600_000;

        if (preg_match('/```\s*html\s*\R([\s\S]*?)\R```/i', $raw, $m)) {
            $candidate = trim($m[1]);
            if ($this->looksLikeStandaloneHtmlDocument($candidate)) {
                return $this->truncateHtmlCandidate($candidate, $maxBytes);
            }
        }

        if (preg_match('/```\s*\R([\s\S]*?)\R```/', $raw, $m)) {
            $candidate = trim($m[1]);
            if ($this->looksLikeStandaloneHtmlDocument($candidate)) {
                return $this->truncateHtmlCandidate($candidate, $maxBytes);
            }
        }

        if (preg_match('/<!DOCTYPE\s+html/i', $raw)) {
            if (preg_match('/<!DOCTYPE\s+html[\s\S]*?<\/html>/i', $raw, $m)) {
                return $this->truncateHtmlCandidate(trim($m[0]), $maxBytes);
            }
        }

        if (preg_match('/<html\b[^>]*>/i', $raw, $hm, PREG_OFFSET_CAPTURE)) {
            $start = (int) $hm[0][1];
            $slice = substr($raw, $start);
            $end = stripos($slice, '</html>');
            if ($end !== false) {
                return $this->truncateHtmlCandidate(trim(substr($slice, 0, $end + 7)), $maxBytes);
            }
        }

        $trim = ltrim($raw);
        if (stripos($trim, '<html') === 0) {
            $end = stripos($raw, '</html>');
            if ($end !== false) {
                return $this->truncateHtmlCandidate(trim(substr($raw, 0, $end + 7)), $maxBytes);
            }
        }

        return null;
    }

    private function looksLikeStandaloneHtmlDocument(string $s): bool
    {
        return (bool) preg_match('/<html\b/i', $s) && (bool) preg_match('/<\/html>/i', $s);
    }

    private function truncateHtmlCandidate(string $html, int $maxBytes): ?string
    {
        if (strlen($html) > $maxBytes) {
            return null;
        }

        return $html;
    }
}
