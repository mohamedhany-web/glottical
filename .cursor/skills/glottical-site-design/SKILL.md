---
name: glottical-site-design
description: >-
  Applies Glottical public landing design from designs/public-pages structure with
  platform palette #4B3A78 · #B77CFF · #FFFBE6 · #C9FFD8 · #FFB7A5 (sana-* CSS classes).
  Use when editing homepage, about, courses, contact, public marketing, landing CSS,
  تصميم, لاندنج, or matching public UI. Admin stays on Atheer panel tokens unless asked.
---

# Glottical Public Landing (purple platform palette)

**Structure SoT:** `designs/public-pages/` (layout, sections, `sana-*` patterns)  
**Brand colors:** `config/academy-theme.php`  
**Live CSS:** `public/css/landing/*.css`  
**Live partials:** `partials/landing/{head,navbar,footer}.blade.php`

Creatives: place brand art in `public/img/glottical/` (e.g. `hero.png`). Until then, pages fall back to Unsplash placeholders.

## قاعدة إلزامية

الان جميع الصفحات التي سوف نرسلها من لوحة تحكم الادمن لابد ان تكون في التصميم متكافئة مع تصميم لوحة التحكم والموقع كامل لا نري صفحة ان تختلف عن صفحة اخري

Public marketing pages must share this landing shell. Admin `/admin` keeps Atheer admin chrome unless migrating later.

## When editing public pages

1. Open matching mirror in `designs/public-pages/` for structure.
2. Use `@include('partials.landing.head')` + navbar + footer — not Atheer teal storefront.
3. Keep Glottical lang/routes/business (free trial, courses, WhatsApp from `PublicFooterSettings`).
4. Do not invent Sana kids copy; use `lang/ar/landing.php` + `public.php`.
5. Class prefix stays `sana-*` (from design kit) with Glottical CSS variables.

## Tokens

| Role | Hex | CSS |
|------|-----|-----|
| Primary | `#4B3A78` | `--p` |
| Primary dark | `#3A2C5C` | `--p-dark` |
| Lavender | `#B77CFF` | `--p-light` / `--lavender` |
| Cream canvas | `#FFFBE6` | `--bg` / `--cream` |
| Mint | `#C9FFD8` | `--mint` |
| Peach accent | `#FFB7A5` | `--gold` / `--peach` |
| Ink | `#2E234A` | `--text` |

Fonts: Cairo (display) + Tajawal (body). Auth can stay IBM Plex / auth-geo remapped.

## Rebrand CSS

```bash
# After copying fresh CSS from designs/public-pages/assets:
php public/css/landing/_rebrand.php
```

## Admin

See [admin.md](admin.md) — Atheer ink/teal panel until a separate admin redesign is requested.
