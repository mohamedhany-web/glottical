(function () {

  const CART_KEY = "atheer-cart-v1";



  function readCart() {

    try {

      return JSON.parse(localStorage.getItem(CART_KEY) || "[]");

    } catch {

      return [];

    }

  }



  function writeCart(items) {

    localStorage.setItem(CART_KEY, JSON.stringify(items));

    updateCartBadge();

  }



  function cartCount() {

    return readCart().reduce((sum, item) => sum + (item.quantity || 1), 0);

  }



  function updateCartBadge() {

    const badge = document.querySelector("[data-cart-count]");

    const count = cartCount();

    if (!badge) return;

    if (count > 0) {

      badge.textContent = count > 9 ? "9+" : String(count);

      badge.classList.remove("hidden");

      badge.classList.add("inline-flex");

      badge.setAttribute("aria-label", `${count} منتج في السلة`);

    } else {

      badge.classList.add("hidden");

      badge.classList.remove("inline-flex");

      badge.removeAttribute("aria-label");

    }

  }



  window.AtheerCart = {

    read: readCart,

    write: writeCart,

    count: cartCount,

    add(product) {

      const items = readCart();

      const key = `${product.id}:${product.color || ""}:${product.size || ""}`;

      const existing = items.find(

        (item) => `${item.id}:${item.color || ""}:${item.size || ""}` === key,

      );

      if (existing) existing.quantity += product.quantity || 1;

      else items.push({ ...product, quantity: product.quantity || 1 });

      writeCart(items);

    },

    clear() {

      writeCart([]);

    },

  };



  function headerHTML() {

    return `

<div class="bg-ink text-white">

  <div class="container-wide flex h-10 items-center justify-center gap-3 text-center text-xs sm:text-sm">

    <span class="text-metal">✦</span>

    <p>شحن مجاني للطلبات فوق ٥٠٠ ر.س · إرجاع خلال ١٤ يوماً · دفع آمن</p>

    <a href="offers.html" class="hidden underline underline-offset-4 opacity-90 transition hover:opacity-100 sm:inline">تصفّح العروض</a>

  </div>

</div>

<header class="sticky top-0 z-50 border-b border-line/80 bg-surface/90 backdrop-blur-xl">

  <div class="container-wide flex h-16 items-center gap-3 md:h-20 md:gap-4">

    <button type="button" id="nav-toggle" class="inline-flex size-10 items-center justify-center rounded-xl transition hover:bg-canvas lg:hidden" aria-label="القائمة" aria-expanded="false" aria-controls="mobile-nav">

      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 5h16M4 12h16M4 19h16"/></svg>

    </button>

    <a href="index.html" class="shrink-0 text-2xl font-bold tracking-tight text-ink md:text-3xl">أثير</a>

    <nav class="mr-2 hidden items-center gap-0.5 lg:flex" aria-label="التنقل الرئيسي">

      <a class="nav-link rounded-lg px-3 py-2 text-sm text-ink-soft hover:bg-canvas hover:text-ink" href="categories.html">التصنيفات</a>

      <a class="nav-link rounded-lg px-3 py-2 text-sm text-ink-soft hover:bg-canvas hover:text-ink" href="collections.html">المجموعات</a>

      <a class="nav-link rounded-lg px-3 py-2 text-sm text-ink-soft hover:bg-canvas hover:text-ink" href="brands.html">العلامات</a>

      <a class="nav-link rounded-lg px-3 py-2 text-sm text-ink-soft hover:bg-canvas hover:text-ink" href="offers.html">العروض</a>

      <a class="nav-link rounded-lg px-3 py-2 text-sm text-ink-soft hover:bg-canvas hover:text-ink" href="new-arrivals.html">وصل حديثاً</a>

    </nav>

    <div class="mx-auto hidden min-w-0 max-w-xl flex-1 md:block">

      <div class="relative">

        <svg class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-muted" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>

        <input class="h-12 w-full rounded-xl border border-line bg-surface pr-10 pl-4 text-sm transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20" placeholder="ابحث عن منتج، علامة، أو أسلوب…" aria-label="بحث" />

      </div>

    </div>

    <div class="ms-auto flex items-center gap-0.5 sm:gap-1">

      <a href="assistant.html" class="assistant-pill group relative hidden items-center gap-2 overflow-hidden rounded-xl px-3 py-2 text-sm font-medium sm:inline-flex">

        <span class="pointer-events-none absolute inset-0 bg-gradient-to-l from-accent/10 via-accent-soft to-metal/10 opacity-80 transition group-hover:opacity-100"></span>

        <span class="pointer-events-none absolute inset-0 opacity-0 transition group-hover:opacity-100" style="background:radial-gradient(circle at 30% 50%,rgba(176,141,87,0.25),transparent 55%),radial-gradient(circle at 80% 30%,rgba(15,92,87,0.2),transparent 50%)"></span>

        <svg class="relative shrink-0 text-accent" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/><path d="M5 3v4"/><path d="M19 17v4"/><path d="M3 5h4"/><path d="M17 19h4"/></svg>

        <span class="relative bg-gradient-to-l from-accent to-ink bg-clip-text text-transparent">مساعد أثير</span>

      </a>

      <a href="account.html#wishlist" class="inline-flex size-10 items-center justify-center rounded-xl transition hover:bg-canvas hover:text-accent" aria-label="المفضلة">

        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>

      </a>

      <a href="account.html" class="inline-flex size-10 items-center justify-center rounded-xl transition hover:bg-canvas" aria-label="حسابي">

        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>

      </a>

      <a href="cart.html" class="relative inline-flex size-10 items-center justify-center rounded-xl transition hover:bg-canvas" aria-label="السلة">

        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>

        <span data-cart-count class="absolute top-1 left-1 hidden size-[18px] min-w-[18px] items-center justify-center rounded-full bg-accent px-1 text-[10px] font-bold leading-none text-white shadow-soft"></span>

      </a>

    </div>

  </div>

  <div id="mobile-nav" class="hidden border-t border-line bg-surface px-4 py-4 lg:hidden">

    <div class="mb-4">

      <div class="relative">

        <svg class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-muted" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>

        <input class="h-11 w-full rounded-xl border border-line bg-canvas pr-10 pl-4 text-sm" placeholder="ابحث…" aria-label="بحث" />

      </div>

    </div>

    <div class="flex flex-col gap-0.5 text-sm font-medium">

      <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="categories.html">التصنيفات</a>

      <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="collections.html">المجموعات</a>

      <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="brands.html">العلامات</a>

      <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="offers.html">العروض</a>

      <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="new-arrivals.html">وصل حديثاً</a>

      <a class="assistant-pill relative flex items-center gap-2 overflow-hidden rounded-xl px-3 py-3" href="assistant.html">

        <span class="pointer-events-none absolute inset-0 bg-gradient-to-l from-accent/10 via-accent-soft to-metal/10"></span>

        <svg class="relative shrink-0 text-accent" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>

        <span class="relative font-semibold text-accent">مساعد أثير</span>

      </a>

      <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="account.html#wishlist">المفضلة</a>

      <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="account.html">حسابي</a>

      <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="cart.html">السلة</a>

      <a class="rounded-xl px-3 py-3 text-muted transition hover:bg-canvas hover:text-ink" href="help.html">مركز المساعدة</a>

      <a class="rounded-xl px-3 py-3 text-muted transition hover:bg-canvas hover:text-ink" href="admin/index.html">لوحة الإدارة</a>

    </div>

  </div>

</header>`;

  }



  function footerHTML() {

    return `

<footer class="mt-24 border-t border-line bg-ink text-white">

  <div class="container-wide py-16 md:py-20">

    <div class="grid gap-12 lg:grid-cols-[1.15fr_2fr]">

      <div class="space-y-5">

        <p class="text-3xl font-bold">أثير</p>

        <p class="max-w-sm text-sm leading-8 text-white/70">منصة تسوق عربية فاخرة صُممت لتسهيل الاكتشاف، وتعزيز الثقة، وإتمام الشراء بثقة وسرعة — من البحث إلى بابك.</p>

        <a href="assistant.html" class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/5 px-4 py-2.5 text-sm font-medium transition hover:bg-white/10">

          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-metal" aria-hidden="true"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>

          جرّب مساعد أثير

        </a>

      </div>

      <div class="grid gap-10 sm:grid-cols-3">

        <div>

          <p class="mb-4 text-sm font-semibold">تسوق</p>

          <nav class="space-y-3 text-sm text-white/65" aria-label="روابط التسوق">

            <a class="block transition hover:text-white" href="categories.html">التصنيفات</a>

            <a class="block transition hover:text-white" href="collections.html">المجموعات</a>

            <a class="block transition hover:text-white" href="brands.html">العلامات</a>

            <a class="block transition hover:text-white" href="offers.html">العروض</a>

            <a class="block transition hover:text-white" href="new-arrivals.html">وصل حديثاً</a>

            <a class="block transition hover:text-white" href="compare.html">مقارنة المنتجات</a>

          </nav>

        </div>

        <div>

          <p class="mb-4 text-sm font-semibold">خدمة العملاء</p>

          <nav class="space-y-3 text-sm text-white/65" aria-label="روابط خدمة العملاء">

            <a class="block transition hover:text-white" href="help.html">مركز المساعدة</a>

            <a class="block transition hover:text-white" href="account.html">حسابي</a>

            <a class="block transition hover:text-white" href="account.html#orders">تتبع الطلب</a>

            <a class="block transition hover:text-white" href="account.html#returns">المرتجعات</a>

            <a class="block transition hover:text-white" href="cart.html">السلة</a>

            <a class="block transition hover:text-white" href="checkout.html">إتمام الشراء</a>

          </nav>

        </div>

        <div>

          <p class="mb-4 text-sm font-semibold">أثير</p>

          <nav class="space-y-3 text-sm text-white/65" aria-label="روابط أثير">

            <a class="block transition hover:text-white" href="about.html">من نحن</a>

            <a class="block transition hover:text-white" href="assistant.html">مساعد أثير</a>

            <a class="block transition hover:text-white" href="account.html#rewards">برنامج المكافآت</a>

            <a class="block transition hover:text-white" href="admin/index.html">لوحة الإدارة</a>

          </nav>

        </div>

      </div>

    </div>

  </div>

  <div class="border-t border-white/10">

    <div class="container-wide flex flex-wrap items-center justify-between gap-4 py-6 text-xs text-white/50">

      <p>© 2026 أثير. جميع الحقوق محفوظة.</p>

      <div class="flex flex-wrap gap-4">

        <a href="help.html" class="transition hover:text-white/80">سياسة الخصوصية</a>

        <a href="help.html" class="transition hover:text-white/80">الشروط والأحكام</a>

      </div>

    </div>

  </div>

</footer>`;

  }



  document.addEventListener("DOMContentLoaded", () => {

    const headerMount = document.querySelector("[data-site-header]");

    const footerMount = document.querySelector("[data-site-footer]");

    if (headerMount) headerMount.innerHTML = headerHTML();

    if (footerMount) footerMount.innerHTML = footerHTML();



    const toggle = document.getElementById("nav-toggle");

    const mobile = document.getElementById("mobile-nav");

    if (toggle && mobile) {

      toggle.addEventListener("click", () => {

        const open = mobile.classList.toggle("hidden");

        toggle.setAttribute("aria-expanded", open ? "false" : "true");

      });

    }



    updateCartBadge();



    document.querySelectorAll("[data-add-to-cart]").forEach((btn) => {

      btn.addEventListener("click", () => {

        const product = {

          id: btn.dataset.id,

          name: btn.dataset.name,

          brand: btn.dataset.brand,

          price: Number(btn.dataset.price),

          image: btn.dataset.image,

          color: btn.dataset.color || "",

          size: btn.dataset.size || "",

          quantity: Number(btn.dataset.qty || 1),

        };

        window.AtheerCart.add(product);

        const label = btn.dataset.label || "أضف إلى السلة";

        const original = btn.innerHTML;

        btn.textContent = "تمت الإضافة";

        setTimeout(() => {

          btn.innerHTML = original;

        }, 1600);

      });

    });



    const main = document.querySelector("main");

    if (main) main.classList.add("page-enter");



    document.querySelectorAll("a[href$='.html']").forEach((link) => {

      link.addEventListener("click", (event) => {

        const href = link.getAttribute("href");

        if (!href || href.startsWith("http") || event.metaKey || event.ctrlKey) return;

        if (href.includes("#")) return;

        const page = document.querySelector("main");

        if (!page) return;

        event.preventDefault();

        page.classList.remove("page-enter");

        page.classList.add("page-leave");

        setTimeout(() => {

          location.href = href;

        }, 160);

      });

    });

  });

})();

