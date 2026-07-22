# أنماط لوحة تحكم الأدمن (Atheer)

مرجع سريع لأي شاشة تحت `/admin/*`. التفاصيل البصرية العامة في `SKILL.md`.

## Shell (لا تغيّره لكل صفحة)

| قطعة | ملف |
|------|-----|
| Layout | `resources/views/layouts/admin.blade.php` |
| Sidebar | `resources/views/layouts/admin-sidebar.blade.php` |
| CSS | `public/css/admin-atheer.css` |
| مرجع التصميم | `site/admin/index.html` + باقي `site/admin/*.html` |

كل صفحة أدمن: `@extends('layouts.admin')` فقط — لا تبنِ layout موازي ولا ترجع للثيم القديم (navy/indigo/cyan).

## صفحة محتوى — قالب

```blade
@extends('layouts.admin')
@section('title', '…')
@section('page_title', '…')
@section('content')
<div class="space-y-5">
  <section class="flex flex-wrap items-end justify-between gap-4">
    <div>
      <p class="text-xs font-medium text-muted">kicker</p>
      <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink">العنوان</h2>
    </div>
    <a href="…" class="btn-press inline-flex h-9 items-center rounded-xl bg-accent px-4 text-sm font-medium text-white">إجراء</a>
  </section>

  <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
    …
  </article>
</div>
@endsection
```

## مكوّنات داخل الأدمن

| عنصر | نمط |
|------|-----|
| بطاقة / لوحة | `rounded-2xl border border-line bg-surface … shadow-soft` |
| أيقونة صغيرة | `size-9 rounded-xl bg-[#f2f5f4] text-accent` |
| زر أساسي | `bg-accent text-white hover:bg-[#0d4f4a]` + `rounded-xl` |
| زر ثانوي | `border border-line … hover:bg-accent-soft hover:text-accent` |
| حقل إدخال | `h-11 rounded-xl border border-line … focus:border-accent focus:ring-accent/20` |
| جداول | رأس هادئ `text-muted`، صفوف بـ `border-line`، بدون تدرجات ملونة |
| تنبيه نجاح | حدود success خفيفة + خلفية خضراء باهتة |
| شريط ink داخلي | مسموح للهيرو القصير فقط؛ باقي الصفحة canvas/surface |

## ممنوع في صفحات الأدمن

- تدرجات indigo/violet/blue/navy القديمة (`from-indigo-*`, `navy-*`, `cyan-*` كألوان رئيسية)
- `glass-panel` / بطاقات Netflix / أزرار برتقالية `#FB5607`
- سايدبار أو هيدر مخصص داخل الصفحة (استخدم الشِل)
- صفحة بأسلوب الواجهة العامة (Tailwind CDN standalone) داخل `/admin`

## تطابق مع الموقع

نفس التوكنات: canvas / surface / ink / accent / metal / line / muted.  
الفرق فقط في الشِل (سايدبار ثابت 280px + topbar) وليس في الهوية البصرية.
