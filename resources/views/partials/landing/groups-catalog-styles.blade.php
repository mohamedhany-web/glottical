{{-- Shared styles + card grid for /groups/courses and /groups/one-to-one --}}
<style>
  .gl-gc-page { background: var(--bg, #F4F7FC); }
  .gl-gc-page .sana-cat-hero {
    padding: clamp(28px, 4.5vw, 44px) 0 clamp(32px, 5vw, 48px);
  }
  .gl-gc-page .sana-cat-hero__desc { margin-bottom: 16px; }
  .gl-gc-body {
    padding-top: clamp(24px, 3.5vw, 36px);
    padding-bottom: clamp(40px, 6vw, 64px);
  }
  .gl-gc-grid {
    display: grid;
    gap: .9rem;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    margin-bottom: 1.5rem;
  }
  .gl-gc-card {
    display: flex;
    flex-direction: column;
    background: #fff;
    border: 1.5px solid #D7DDE6;
    border-radius: 16px;
    overflow: hidden;
    text-decoration: none !important;
    color: inherit;
    box-shadow: 0 10px 28px -18px rgba(11,61,145,.3);
    transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
  }
  .gl-gc-card:hover {
    transform: translateY(-3px);
    border-color: rgba(11,61,145,.28);
    box-shadow: 0 18px 40px -16px rgba(11,61,145,.35);
  }
  .gl-gc-card__media {
    position: relative;
    aspect-ratio: 16/10;
    background: #E8EEF8;
    overflow: hidden;
  }
  .gl-gc-card__media img {
    width: 100%; height: 100%; object-fit: cover; display: block;
    transition: transform .4s ease;
  }
  .gl-gc-card:hover .gl-gc-card__media img { transform: scale(1.04); }
  .gl-gc-card__badge {
    position: absolute; top: 10px; inset-inline-start: 10px; z-index: 1;
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 9px; border-radius: 999px;
    background: rgba(11,61,145,.92); color: #fff;
    font-size: .66rem; font-weight: 800;
  }
  .gl-gc-card__badge--gold {
    background: linear-gradient(180deg, #FFD24D, #F5B800);
    color: #0B1220;
  }
  .gl-gc-card__body {
    display: flex; flex-direction: column; gap: .35rem;
    padding: .85rem .9rem 1rem; flex: 1;
  }
  .gl-gc-card__body h2 {
    margin: 0;
    font-size: .92rem; font-weight: 900; color: #0B1220; line-height: 1.35;
  }
  .gl-gc-card__meta {
    margin: 0;
    font-size: .74rem; color: #5B6577; font-weight: 600;
  }
  .gl-gc-card__meta i { color: #0B3D91; margin-inline-end: 4px; }
  .gl-gc-card__foot {
    display: flex; align-items: center; justify-content: space-between;
    gap: .5rem; margin-top: auto; padding-top: .55rem;
    border-top: 1px solid #E8EEF8;
  }
  .gl-gc-card__price {
    font-size: .88rem; font-weight: 900; color: #0B3D91;
  }
  .gl-gc-card__price--free { color: #047857; }
  .gl-gc-card__cta {
    font-size: .72rem; font-weight: 800; color: #0B3D91;
    display: inline-flex; align-items: center; gap: 5px;
  }
  .gl-gc-empty {
    text-align: center;
    padding: 2rem 1.25rem;
    border-radius: 16px;
    background: #fff;
    border: 1.5px dashed #D7DDE6;
    color: #5B6577;
    font-weight: 700;
  }
  .gl-gc-empty p { margin: 0 0 1rem; }
  .gl-gc-pager {
    display: flex; justify-content: center; margin-top: 1rem;
  }
  .gl-gc-pager nav { display: flex; flex-wrap: wrap; gap: .35rem; justify-content: center; }
  .gl-gc-band {
    margin-top: 1.75rem;
    border-radius: 18px;
    padding: clamp(1.2rem, 3vw, 1.65rem);
    background:
      radial-gradient(circle at 90% 0%, rgba(245,184,0,.18), transparent 42%),
      linear-gradient(145deg, #051F4D 0%, #0B3D91 55%, #1A56B0 100%);
    color: #fff;
  }
  .gl-gc-band__inner {
    display: flex; flex-wrap: wrap; gap: 1rem;
    align-items: center; justify-content: space-between;
  }
  .gl-gc-band h2 {
    margin: 0 0 .3rem;
    font-family: Cairo,Tajawal,sans-serif;
    font-size: clamp(1.05rem, 2.2vw, 1.3rem);
    font-weight: 900;
  }
  .gl-gc-band p {
    margin: 0; font-size: .8rem; line-height: 1.6; font-weight: 600;
    color: rgba(255,255,255,.82); max-width: 34rem;
  }
  .gl-gc-band__actions { display: flex; flex-wrap: wrap; gap: .5rem; }
  .gl-gc-band__actions .sana-btn { padding: .65rem 1rem; font-size: .8rem; }
</style>
