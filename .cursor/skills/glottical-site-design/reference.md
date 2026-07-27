# Glottical design — reference

## Public (Atheer academy)

| Path | Role |
|------|------|
| `designs/glottical-public/` | **Source of truth** for public/auth marketing |
| `designs/glottical-public/content.ar.json` | Arabic copy for generator |
| `designs/glottical-public/_generate.php` | Syncs HTML → designs + `site/` |
| `site/*.html` | Local static preview (same pages) |
| `resources/views/partials/atheer-head.blade.php` | Live head + inlined CSS |
| `resources/views/partials/atheer-home-header.blade.php` | Live header |
| `resources/views/partials/atheer-home-footer.blade.php` | Live footer |
| `resources/views/partials/welcome-main-site.blade.php` | Live homepage body |

## Admin

See [admin.md](admin.md). Tokens match public Atheer.

## Other

`designs/public-pages/` = **Sana** purple brand kit (do not use for Glottical public unless asked).
