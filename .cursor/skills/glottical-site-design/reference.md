# Glottical site design — reference

Companion to `SKILL.md`. Read when implementing a new public section or auditing consistency against `site/index.html`.

## File map

| Path | Role |
|------|------|
| `site/index.html` | Canonical homepage structure (15 sections) |
| [pages.md](pages.md) | Full index of ready `site/` HTML pages + academy mapping |
| `site/assets/css/atheer.css` | Motion, containers, body wash |
| `site/assets/js/tailwind-config.js` | Color/shadow/radius/font tokens |
| `site/assets/js/storefront.js` | Header/footer markup patterns |
| `public/css/atheer.css` | App copy of CSS (keep in sync when changing system CSS) |
| `public/js/atheer-tailwind-config.js` | App copy of Tailwind tokens |
| `resources/views/partials/welcome-main-site.blade.php` | Live homepage main |
| `resources/views/partials/atheer-home-header.blade.php` | Live header |
| `resources/views/partials/atheer-home-footer.blade.php` | Live footer |
| `resources/views/partials/landing-course-card-site.blade.php` | Course card = product card |

## Homepage section order (from site/)

1. Full-bleed dark hero (`min-h-[92vh]`) — brand, one headline, one sub, two CTAs  
2. Dark rounded banner (assistant → free assessment)  
3. Categories grid (4 tall tiles)  
4. Featured collections on `bg-surface` (3 large tiles)  
5. Best sellers / trending course grid  
6. New arrivals grid  
7. Flash / limited offers (2 split cards + countdown)  
8. Trusted marks row (instructors as tiles)  
9. Recommended grid  
10. Inspiration gallery (3)  
11. Reviews (3 quotes on canvas cards)  
12. Benefits (4 icon cards)  
13. Accent promo band (+ optional phone mock)  
14. Newsletter / interest card  
15. FAQ accordion  

When editing the homepage, preserve this rhythm unless the user explicitly reorders.

## Section header snippet

```html
<div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
  <div class="max-w-2xl space-y-3">
    <p class="text-sm font-medium text-accent">…kicker…</p>
    <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl">…</h2>
    <p class="text-base leading-8 text-muted">…</p>
  </div>
  <a href="…" class="inline-flex h-10 shrink-0 items-center rounded-xl border border-line px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">عرض الكل</a>
</div>
```

## Course card rules

- Wrapper: `group card-lift … bg-surface shadow-soft rounded-2xl`
- Media: `aspect-[4/5]`, `img-zoom`
- Badges: `bg-accent-soft text-accent` or warm `#f4eadc` / `#7a5c2e` for discount/free
- Bottom actions: hover reveal; always visible `max-md`
- Primary action → course show / start learning (not “add to cart”)
- Meta: instructor (muted), title, metal star + rating, price

## Header pattern

- Top announce bar: `bg-ink text-white` + `text-metal` ✦  
- Sticky bar: `bg-surface/90 backdrop-blur-xl border-b border-line/80`  
- Logo bold ink; nav `nav-link`; search field `h-12 rounded-xl border-line`  
- Primary header CTA: solid `bg-accent text-white` (avoid soft gradient “AI pill” CTAs)

## Footer pattern

- `bg-ink text-white`, `mt-24`, multi-column links, quiet legal row

## Typography scale (public)

- Display brand in hero: `text-4xl md:text-6xl font-bold`
- Hero H1: `text-3xl md:text-5xl font-semibold`
- Section H2: `text-2xl md:text-3xl font-semibold`
- Body: `text-base leading-8 text-muted`
- Avoid `font-black` / ultra-heavy Netflix marketing type on storefront pages

## Responsive notes

- `container-wide`: tighter horizontal padding on xs (`1.25rem`), `2rem` from `sm`
- Hero CTAs: stack full-width on phones
- Course grids stay 2-col on mobile; tighten type (`text-xs` → `sm:text-sm`)
- Modals: bottom sheet on mobile
- Announce bar: truncate long copy on xs
- Prevent `overflow-x` on body

## Anti-patterns (reject in review)

| Avoid | Prefer |
|-------|--------|
| Dark Netflix rows / glass navy cards | Light surface cards |
| Yellow/cyan academy gradient CTAs on marketing | Accent teal CTAs |
| Purple glow / glassmorphism stacks | Flat soft shadows |
| Inter/Roboto/system as primary | IBM Plex Sans Arabic |
| Inset hero image card | Full-bleed hero |
| Cart / wishlist as primary nav | Courses, paths, assessment, account |
| Unstyled Tailwind token classes without fallback on critical modals | Explicit CSS for modal chrome |

## Sync rule

If you change tokens or motion in `public/css/atheer.css` or `public/js/atheer-tailwind-config.js`, mirror the same change into `site/assets/...` when the static reference should stay aligned (or note the drift in the PR).
