---
name: glottical-site-design
description: >-
  Applies Glottical public landing design from designs/public-pages structure with
  academy blue #0B3D91 and yellow #F5B800 (sana-* CSS classes remapped). Use when
  editing homepage, about, courses, contact, public marketing, landing CSS, تصميم,
  لاندنج, or matching public UI. Admin stays on Atheer panel tokens unless asked.
---

# Glottical Public Landing (blue + yellow)

**Structure SoT:** `designs/public-pages/` (layout, sections, `sana-*` patterns)  
**Brand colors:** `config/academy-theme.php` — blue `#0B3D91` · yellow `#F5B800`  
**Live CSS:** `public/css/landing/*.css` (rebranded from public-pages assets)  
**Live partials:** `partials/landing/{head,navbar,footer}.blade.php`

Creatives: place brand art in `public/img/glottical/` (e.g. `hero.png`). Until then, pages fall back to Unsplash placeholders.

## قاعدة إلزامية

الان جميع الصفحات التي سوف نرسلها من لوحة تحكم الادمن لابد ان تكون في التصميم متكافئة مع تصميم لوحة التحكم والموقع كامل لا نري صفحة ان تختلف عن صفحة اخري

Public marketing pages must share this landing shell (blue/yellow). Admin `/admin` keeps Atheer admin chrome unless migrating later.

## When editing public pages

1. Open matching mirror in `designs/public-pages/` for structure.
2. Use `@include('partials.landing.head')` + navbar + footer — not Atheer teal storefront.
3. Keep Glottical lang/routes/business (free trial, courses, WhatsApp from `PublicFooterSettings`).
4. Do not invent Sana kids copy; use `lang/ar/landing.php` + `public.php`.
5. Class prefix stays `sana-*` (from design kit) with Glottical CSS variables.

## Tokens

| Role | Hex |
|------|-----|
| Blue `--p` | `#0B3D91` |
| Blue dark | `#072A66` |
| Yellow `--gold` | `#F5B800` |
| Canvas | `#F4F7FC` |
| Text | `#0B1220` |

Fonts: Cairo (display) + Tajawal (body). Auth can stay IBM Plex / auth-geo remapped.

## Rebrand CSS

```bash
# After copying fresh CSS from designs/public-pages/assets:
php public/css/landing/_rebrand.php
```

## Admin

See [admin.md](admin.md) — Atheer ink/teal panel until a separate admin redesign is requested.
