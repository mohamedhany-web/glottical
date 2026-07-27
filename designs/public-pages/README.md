# تصميمات الصفحات العامة الخارجية — Sana

مجلد **مرآة CSS+HTML** لصفحات اللاندنج الإنتاجية (`landing/sana` + المستخرج من theme blades). ليس جزءاً من تشغيل Laravel ولا يُربط بالمسارات.

**افتح أولاً:** [01-home.html](01-home.html)

**قاعدة المحتوى:** النصوص والعناصر هنا **مرآة حرفية** لما يظهر في الإنتاج (Blade + `lang/ar/*`) — لا نص تسويقي مخترع. راجع [CONTENT.md](CONTENT.md).

## الصفحات

| ملف | الصفحة | CSS المحمّل | مصدر Blade |
|-----|--------|-------------|------------|
| [01-home.html](01-home.html) | الرئيسية | `sana-theme.css` | `welcome` + `landing/sana/*` |
| [02-about.html](02-about.html) | من نحن | `sana-theme` + `sana-courses-catalog-theme` + `sana-about-theme` | `public/about.blade.php` |
| [03-courses.html](03-courses.html) | كتالوج الكورسات | `sana-theme` + `sana-courses-catalog-theme` | `courses.blade.php` |
| [04-pricing.html](04-pricing.html) | الباقات | `sana-theme` + `sana-courses-catalog-theme` + `sana-pricing-theme` | `public/pricing.blade.php` |
| [05-contact.html](05-contact.html) | تواصل | `sana-theme` + `sana-courses-catalog-theme` + `sana-contact-theme` | `public/contact.blade.php` |
| [06-auth-login.html](06-auth-login.html) | دخول | `sana-auth-geo.css` | `auth/login.blade.php` |
| [07-auth-register.html](07-auth-register.html) | تسجيل | `sana-auth-geo.css` | `auth/register.blade.php` |
| [08-tutor-apply.html](08-tutor-apply.html) | تقديم معلم | `sana-theme.css` (+ أنماط مرآة خفيفة) | `tutor/apply.blade.php` |
| [09-instructors.html](09-instructors.html) | المعلمون | `sana-theme` + `sana-courses-catalog-theme` + `sana-instructors-catalog-theme` | `instructors/index.blade.php` |

## الأصول

| ملف | المصدر |
|-----|--------|
| `assets/sana-theme.css` | `landing/sana/theme.blade.php` |
| `assets/sana-about-theme.css` | `landing/sana/about-theme` |
| `assets/sana-contact-theme.css` | `landing/sana/contact-theme` |
| `assets/sana-pricing-theme.css` | `landing/sana/pricing-theme` |
| `assets/sana-courses-catalog-theme.css` | `landing/sana/courses-catalog-theme` |
| `assets/sana-instructors-catalog-theme.css` | `landing/sana/instructors-catalog-theme` |
| `assets/sana-subpages-theme.css` | صفحات فرعية (FAQ/help/…) — غير مستخدمة في المرايا أعلاه |
| `assets/sana-auth-geo.css` | `auth/partials/geometric-styles.blade.php` + brand-vars |
| `assets/sana-scripts.js` | سلوك النافبار / FAQ / reveal من الإنتاج |
| `assets/shell.css` / `tokens.css` | قديم — **لا تستخدم كأساس للتصميم** |

الصور: `../../public/img/sanua/...`

## Skills

- `.cursor/skills/sana-public-page-design/SKILL.md` — قواعد بصرية + توكنات + classes
- `.cursor/skills/sana-public-page-design/pages.md` — فهرس الصفحات/CSS
- `.cursor/skills/sana-landing-blade-implement/SKILL.md` — نقل المرآة إلى Blade
- `.cursor/skills/glottical-site-design/SKILL.md` — موجّه: عام=Sana · أدمن=Atheer

## قواعد

1. انسخ النص من Blade/lang أولاً — ثم رتّب الهيكل فقط
2. استخدم classes الإنتاج `sana-*` — لا تخترع نظام تصميم جديد
3. RTL، Cairo / Tajawal (أو IBM Plex لصفحات auth geo)
4. هذه الملفات **لا تُنشر** كمسارات إنتاج
