---
name: sana-landing-blade-implement
description: >-
  Implements or syncs Laravel Blade public/auth pages to match designs/public-pages
  Sana mirrors (sana-* / geo-* CSS, literal lang copy). Use when porting HTML from
  designs/ into welcome, public/*, courses, instructors, auth login/register,
  tutor/apply, landing/sana themes, or when the user asks to apply Sana design to
  production Blade.
---

# Sana Landing → Blade Implement

**Design input:** `designs/public-pages/*.html` + matching `assets/*.css`  
**Content rules:** `designs/public-pages/CONTENT.md`  
**Visual rules:** skill `sana-public-page-design`

## Workflow (required order)

1. Open the mirror HTML for the target page (`pages.md` in sana-public-page-design).
2. Open the live Blade (and `lang/ar/*` keys listed in CONTENT.md).
3. **Copy structure/classes from the HTML** — keep route helpers, `@csrf`, forms, and Eloquent loops from Blade.
4. Wire CSS the same stack as the mirror (theme blade / `@push` / extracted CSS files under `landing/sana` or public assets — match whatever the project already uses for Sana).
5. Ensure navbar/footer partials match shared nav copy in CONTENT.md.
6. Do not invent new strings; if UI needs a label, add/use a lang key first.

## Mapping

| Design file | Typical Blade |
|-------------|---------------|
| `01-home.html` | `welcome.blade.php` + `landing/sana/sections/*` |
| `02-about.html` | `public/about.blade.php` |
| `03-courses.html` | `courses.blade.php` |
| `04-pricing.html` | `public/pricing.blade.php` |
| `05-contact.html` | `public/contact.blade.php` |
| `06-auth-login.html` | `auth/login.blade.php` + geometric styles partial |
| `07-auth-register.html` | `auth/register.blade.php` |
| `08-tutor-apply.html` | `tutor/apply.blade.php` |
| `09-instructors.html` | `instructors/index.blade.php` |

CSS sources (when present in repo):

- `landing/sana/theme.blade.php` ↔ `sana-theme.css`
- `about-theme` / `contact-theme` / `pricing-theme` / `courses-catalog-theme` / `instructors-catalog-theme`
- Auth: `auth/partials/geometric-styles.blade.php` ↔ `sana-auth-geo.css`

If `landing/sana/*` is missing, create/update Blade to include the mirror CSS (or a single compiled public asset) and keep class names identical to the HTML.

## Rules

- Preserve Laravel: routes, auth, validation errors, old(), pagination, media URLs.
- Replace static `href="05-contact.html"` with `route(...)`.
- Images: `asset('img/sanua/...')` or existing storage helpers — not broken relative `../../public/...`.
- Marketing pages: Cairo + Tajawal + FA; auth geo: IBM Plex.
- Catalog/instructors: body classes `sana-courses-page` / `sana-instructors-page` so nav JS stays solid.
- Include scroll progress + mobile nav markup IDs expected by `sana-scripts.js` (`sana-nav`, `sana-mobile-toggle`, …).

## Syncing design CSS back

When production CSS changes, re-export into `designs/public-pages/assets/` so mirrors stay the source of visual truth. When design HTML changes first, port classes into Blade then refresh CSS mirrors.

## Done checklist

- [ ] Same section order and `sana-*` / `geo-*` classes as the mirror
- [ ] CTAs and nav labels match CONTENT.md / lang
- [ ] RTL + fonts + theme-color correct
- [ ] No Atheer/teal shell on the page
- [ ] Forms still post to correct routes
