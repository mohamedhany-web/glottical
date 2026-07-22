---
name: glottical-site-design
description: >-
  Applies Glottical Atheer design consistently across public site AND admin panel
  (same tokens: ink/teal/metal, IBM Plex, site/ + admin-atheer). Use when editing
  any Glottical UI — homepage, public pages, admin pages under /admin, dashboards,
  forms, tables, headers, sidebars, modals; when the user mentions تصميم, لوحة
  التحكم, admin, site/, أثير, storefront, ريسبونسيف, أنيميشن, or asks to match
  design across pages. Mandatory before shipping any new admin or public screen.
---

# Glottical Site + Admin Design System

Source of truth: `site/` (storefront + `site/admin/`).

Live assets:
- Public: `public/css/atheer.css`, `public/js/atheer-tailwind-config.js`, `partials/atheer-home-*`
- Admin: `public/css/admin-atheer.css`, `layouts/admin.blade.php`, `layouts/admin-sidebar.blade.php`

This is an **academy**. Match the **visual system** of `site/`; never reintroduce e-commerce cart UX as the product model.

## قاعدة إلزامية (حرفية)

الان جميع الصفحات التي سوف نرسلها من لوحة تحكم الادمن لابد ان تكون في التصميم متكافئة مع تصميم لوحة التحكم والموقع كامل لا نري صفحة ان تختلف عن صفحة اخري

**Meaning for the agent:** every new or redesigned screen — public or `/admin` — must use the same Atheer tokens and patterns. No orphan pages with navy/indigo/cyan, Tajawal-only marketing leftovers, or one-off palettes. If a page looks like a different product, it is wrong.

## When starting any design / admin UI task

1. Read this skill fully.
2. Check [pages.md](pages.md) for a ready `site/` page; open it as the first reference.
3. For `/admin/*` also read [admin.md](admin.md).
4. Prefer existing tokens/classes over new one-off styles.
5. Keep Arabic RTL first-class; LTR must still work.
6. Before finishing: visually compare to dashboard (`admin/dashboard`) or a recent Atheer public page (`about`, `groups`, `contact`) — same family.

## Design tokens (required everywhere)

| Token | Hex | Tailwind |
|-------|-----|----------|
| Canvas | `#f3f5f7` | `bg-canvas` |
| Surface | `#ffffff` | `bg-surface` |
| Ink | `#0b1220` | `text-ink` / `bg-ink` |
| Ink soft | `#1c2738` | `text-ink-soft` |
| Muted | `#5b6577` | `text-muted` |
| Line | `#d7dde6` | `border-line` |
| Accent | `#0f5c57` | `bg-accent` / `text-accent` |
| Accent soft | `#e6f2f1` | `bg-accent-soft` |
| Metal | `#b08d57` | `text-metal` |
| Danger | `#b42318` | `text-danger` |
| Success | `#067647` | `text-success` |

Shadows: `shadow-soft`, `shadow-lift`  
Radius: `rounded-xl` / `rounded-2xl` / `rounded-3xl`  
Font: **IBM Plex Sans Arabic** on public + admin Atheer surfaces.

## Two surfaces, one identity

| Surface | Layout | Still same tokens? |
|---------|--------|--------------------|
| Public marketing | Standalone HTML + `atheer-home-header/footer` OR legacy `layouts.public` only if not yet migrated | Yes — migrate leftovers to Atheer when touching the page |
| Admin | **Only** `@extends('layouts.admin')` | Yes — see [admin.md](admin.md) |

Do not mix: no public standalone shell inside `/admin`, no old `acad-*` / indigo dashboard chrome on new admin pages.

## Layout rules (public)

- Width: `container-wide`
- Section spacing: `py-20 md:py-24` (tight: `py-8 md:py-12`)
- Section header: kicker `text-accent` → title `text-ink` → sub `text-muted`

## Components

### Primary CTA
`rounded-xl bg-accent text-white` hover `#0d4f4a`, optional `btn-press`.

### Secondary (light)
`border border-line … hover:bg-accent-soft hover:text-accent`.

### Cards / panels
`bg-surface border-line shadow-soft rounded-2xl`. Interaction containers may use this; decorative card spam is discouraged on marketing heroes.

### Dark ink bands
Hero/promo: `bg-ink` + soft accent/metal radials — not neon cyan/yellow.

### Admin forms / tables
See [admin.md](admin.md). Inputs: `border-line`, focus `accent`.

## Motion

Only `atheer.css` / admin system motions: `page-enter`, `card-lift`, `btn-press`, `img-zoom`. Honor `prefers-reduced-motion`.

## Content mapping (academy)

| site/ | Glottical |
|-------|-----------|
| أثير | Glottical |
| Products | Courses |
| Collections | Learning paths / Groups explainer |
| Brands | Instructors |
| Cart / wishlist | Omit |
| AI assistant CTA | Free level assessment |
| `site/admin/*` | Admin academy CRUD (keep Laravel routes/RBAC) |

## Hard do-nots

- No purple/indigo AI gradients, cream+terracotta defaults, broadsheet layouts.
- No returning to Netflix dark marketing homepage.
- No parallel palette (`acad-yellow`, `#FB5607`, navy glass) on pages you touch — restyle to Atheer.
- No inventing a second admin theme “just for this page”.
- Do not replace admin sidebar with the short ecommerce nav from `site/admin`.

## Checklist before finishing

- [ ] Tokens from the table only
- [ ] Public ↔ admin feel like one product
- [ ] Admin page uses `layouts.admin` + patterns in [admin.md](admin.md)
- [ ] Matched a `site/` reference when one exists ([pages.md](pages.md))
- [ ] Mobile: no horizontal overflow; primary CTAs usable
- [ ] Motions from system only
- [ ] Academy content (not fake shop copy) unless editing `site/` itself

## More detail

- Ready pages index: [pages.md](pages.md)
- Admin patterns: [admin.md](admin.md)
- Snippets / anti-patterns: [reference.md](reference.md)
