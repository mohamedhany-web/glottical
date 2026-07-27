---
name: sana-public-page-design
description: >-
  Applies Sana public marketing design from designs/public-pages (purple/gold,
  sana-* classes, Cairo/Tajawal, RTL). Use when editing homepage, about, courses,
  pricing, contact, instructors, tutor-apply, auth login/register mirrors, landing
  CSS, or when the user mentions Sana, designs/, sana-theme, لاندنج, تصميم الصفحات
  العامة, or matching production public UI to the design folder.
---

# Sana Public Page Design

**Source of truth:** `designs/public-pages/` (HTML+CSS mirrors — not served as Laravel routes).

Open first: `designs/public-pages/01-home.html` + `README.md` + `CONTENT.md`.

For Blade implementation workflow, also load `sana-landing-blade-implement`.

## Non-negotiables

1. **Copy is literal** — text must match Blade / `lang/ar/*` (see `CONTENT.md`). Never invent marketing copy.
2. **Use production classes** — `sana-*` (and `geo-*` on auth). Do not invent a new design system or revive Atheer/teal on these pages.
3. **RTL first** — `lang="ar" dir="rtl"`. Auth geo uses **IBM Plex Sans Arabic**; marketing uses **Cairo** (display) + **Tajawal** (body).
4. **Do not use as base:** `assets/tokens.css`, `assets/shell.css` (legacy). Prefer `sana-theme.css` and page theme CSS.
5. Design HTML files **are not production routes** — they mirror visuals only.

## Brand tokens (marketing)

From `assets/sana-theme.css` `:root`:

| Role | CSS var | Hex |
|------|---------|-----|
| Purple | `--p` | `#6D28D9` |
| Purple dark | `--p-dark` | `#5B21B6` |
| Purple deep | `--p-deep` | `#4C1D95` |
| Purple light | `--p-light` | `#8B5CF6` |
| Gold CTA | `--gold` | `#FBBF24` |
| Gold dark | `--gold-dark` | `#F59E0B` |
| Canvas | `--bg` | `#F8F7FC` |
| Text | `--text` | `#1e1b4b` |
| Muted | `--muted` | `#64748b` |
| Radius | `--radius` | `24px` |
| Shadow | `--shadow` | purple soft lift |
| Max width | `.sana-container` | `1200px` |

`theme-color` on marketing pages: `#5B21B6`.

Highlight in titles: `<span class="hl">…</span>` (gold on purple heroes).

## Auth tokens (separate system)

Login/register use `assets/sana-auth-geo.css` (`body.geo-page`), not `sana-theme` alone:

| Role | Var | Hex |
|------|-----|-----|
| Primary blue | `--edu-primary` | `#1D4EDB` |
| Purple | `--edu-purple` | `#6A2CFF` |
| Accent yellow | `--edu-accent` | `#F4B000` |
| Font | `--edu-font` | IBM Plex Sans Arabic |

Classes: `geo-nav`, `geo-panel`, `geo-field`, `geo-cta`, `geo-role-switch`, …

## CSS stack per page

Always load Font Awesome 6.5 + Cairo/Tajawal (or IBM Plex for auth).

| File | CSS order |
|------|-----------|
| `01-home.html` | `sana-theme.css` |
| `02-about.html` | theme → `sana-courses-catalog-theme` → `sana-about-theme` |
| `03-courses.html` | theme → `sana-courses-catalog-theme` |
| `04-pricing.html` | theme → courses-catalog → `sana-pricing-theme` |
| `05-contact.html` | theme → courses-catalog → `sana-contact-theme` |
| `06/07 auth` | `sana-auth-geo.css` only |
| `08-tutor-apply.html` | `sana-theme.css` (+ light mirror styles) |
| `09-instructors.html` | theme → courses-catalog → `sana-instructors-catalog-theme` |
| FAQ/help/etc. | theme + `sana-subpages-theme.css` (not in numbered mirrors yet) |

JS: `assets/sana-scripts.js` — nav solid/hero toggle, mobile menu, `.sana-reveal`, counters, FAQ.

Body classes examples: `sana-home`, `sana-courses-page`, `sana-instructors-page`, `sana-about-page`, `sana-contact-page`, `sana-pricing-page`, `geo-page`.

## Shell pattern (marketing)

1. `#sana-scroll-progress`
2. `header#sana-nav.sana-nav` (+ `sana-nav--hero` on purple heroes; catalog pages force solid via JS)
3. Nav links: الرئيسية · للطلاب وأولياء الأمور · كيف تعمل سنا؟ · تواصل معنا
4. Actions: تسجيل الدخول · احجز تقييم مستوى (+ واتساب في الموبايل)
5. `#sana-mobile-backdrop`
6. `<main>` with page-specific sections
7. Footer from landing (blurb + links) when present in mirror

## Core components

| Pattern | Classes |
|---------|---------|
| Primary CTA | `.sana-btn.sana-btn--yellow` (gold on purple text) |
| Secondary purple | `.sana-btn.sana-btn--purple` |
| WhatsApp | `.sana-btn.sana-btn--wa` |
| Outline on dark | `.sana-btn.sana-btn--white-outline` |
| Large | `.sana-btn--lg` |
| CTA pair | `.sana-site-cta` |
| Section | `.sana-section` / `--white` / `--soft` |
| Section head | `.sana-head` + `__eyebrow` `__title` `__line` `__sub` |
| Course card | `.sana-course-card` + media/body/footer |
| Reveal | `.sana-reveal` → `.is-visible` |
| Paths band | `.sana-paths-band` (purple gradient + gold CTA) |

## Page → prefix map

See [pages.md](pages.md).

## Images

Prefer `public/img/sanua/...` (paths in mirrors: `../../public/img/sanua/...`).

## Anti-patterns

- Atheer tokens (`#0f5c57`, IBM Plex on marketing shells, `container-wide`, `atheer-head`)
- Old `acad-*` / navy-cyan academy leftovers on Sana pages
- Using `tokens.css` / `shell.css` as the design base
- Invented nav labels or CTAs not in `CONTENT.md`
- Flattening purple heroes into flat teal/ink Atheer heroes

## Before finishing

Compare the changed screen to the matching `designs/public-pages/0N-*.html` file side-by-side: same class names, same CTA pair (تقييم مستوى + واتساب), same purple/gold atmosphere.
