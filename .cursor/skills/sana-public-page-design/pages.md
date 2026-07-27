# فهرس `designs/public-pages`

مرجع حرفي للمرايا. عند التصميم أو التنفيذ: افتح ملف HTML أولاً، ثم CSS المدرج في الجدول.

| ملف | الصفحة | body / main | CSS | مصدر Blade المتوقع |
|-----|--------|-------------|-----|---------------------|
| `01-home.html` | الرئيسية | `sana-home` | `sana-theme.css` | `welcome` + `landing/sana/*` |
| `02-about.html` | من نحن / كيف تعمل سنا؟ | `sana-about-page` | theme + courses-catalog + `sana-about-theme` | `public/about` |
| `03-courses.html` | كتالوج الكورسات | `sana-home sana-courses-page` · `sana-cat-page` | theme + `sana-courses-catalog-theme` | `courses` |
| `04-pricing.html` | الباقات | `sana-pricing-page` · `sana-prx-*` | theme + catalog + `sana-pricing-theme` | `public/pricing` |
| `05-contact.html` | تواصل | `sana-contact-page` · `sana-ct-*` | theme + catalog + `sana-contact-theme` | `public/contact` |
| `06-auth-login.html` | دخول | `geo-page` | `sana-auth-geo.css` | `auth/login` |
| `07-auth-register.html` | تسجيل | `geo-page` | `sana-auth-geo.css` | `auth/register` |
| `08-tutor-apply.html` | تقديم معلم | `sana-home` + `ta-mirror` | `sana-theme.css` | `tutor/apply` |
| `09-instructors.html` | المعلمون | `sana-instructors-page` | theme + catalog + `sana-instructors-catalog-theme` | `instructors/index` |

## بادئات أقسام مهمة

| صفحة | بادئات classes |
|------|----------------|
| Home | `sana-hero`, `sana-paths-band`, `sana-audience`, `sana-course-card`, `sana-faq`, `sana-app-m`, `sana-achieve-box` |
| About | `sana-ab-hero`, `sana-ab-story`, `sana-ab-timeline`, `sana-ab-scene` |
| Courses | `sana-cat-hero`, `sana-cat-search`, `sana-cat-sticky`, `sana-course-card` |
| Pricing | `sana-prx-hero`, `sana-prx-plan`, `sana-prx-trust` |
| Contact | `sana-ct-hero`, `sana-ct-channel`, `sana-ct-form` |
| Instructors | كتالوج + بطاقات معلمين (انظر instructors-catalog CSS) |
| Auth | `geo-*` فقط |
| Subpages (FAQ/help…) | `sana-sub-*` في `sana-subpages-theme.css` |

## نصوص مشتركة (لا تغيّرها)

انظر `CONTENT.md`:

- Nav CTAs: تسجيل الدخول · احجز تقييم مستوى
- Hero CTAs الشائعة: احجز تقييم مستوى مجاني + تواصل عبر واتساب
- أدوار الدخول: طالب · ولي أمر

## أصول CSS

| ملف | دور |
|-----|-----|
| `sana-theme.css` | أساس العلامة + نافبار + أزرار + هوم |
| `sana-courses-catalog-theme.css` | كتالوج / هيرو فرعي مشترك |
| `sana-about-theme.css` | من نحن |
| `sana-pricing-theme.css` | باقات |
| `sana-contact-theme.css` | تواصل |
| `sana-instructors-catalog-theme.css` | قائمة معلمين |
| `sana-subpages-theme.css` | صفحات فرعية عامة |
| `sana-auth-geo.css` | دخول/تسجيل هندسي |
| `sana-scripts.js` | نافبار / reveal / عدّاد |
| `tokens.css` / `shell.css` | **قديم — لا أساس** |
