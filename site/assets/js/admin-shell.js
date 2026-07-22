(function () {
  const ICONS = {
    dashboard:
      '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>',
    reports:
      '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="8" height="4" x="8" y="2" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/></svg>',
    orders:
      '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>',
    products:
      '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>',
    categories:
      '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z"/><path d="m7 16.5-4.74-2.85"/><path d="m7 16.5 5-3"/><path d="M7 16.5v5.17"/><path d="M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z"/><path d="m17 16.5-5-3"/><path d="m17 16.5 4.74-2.85"/><path d="M17 16.5v5.17"/><path d="M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z"/><path d="M12 8 7.26 5.15"/><path d="m12 8 4.74-2.85"/><path d="M12 13.5V8"/></svg>',
    inventory:
      '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 21V10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v11"/><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 1.132-1.803l7.95-3.974a2 2 0 0 1 1.837 0l7.948 3.974A2 2 0 0 1 22 8z"/><path d="M6 13h12"/><path d="M6 17h12"/></svg>',
    coupons:
      '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r=".5" fill="currentColor"/></svg>',
    customers:
      '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
    shipping:
      '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>',
    support:
      '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
    settings:
      '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.47a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>',
    shield:
      '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/></svg>',
    media:
      '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>',
    logs:
      '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>',
  };

  const groups = [
    {
      title: "الرئيسية",
      items: [
        { href: "index.html", key: "index.html", label: "لوحة التحكم", icon: "dashboard" },
        { href: "reports.html", key: "reports.html", label: "التقارير", icon: "reports" },
      ],
    },
    {
      title: "التجارة",
      items: [
        { href: "orders.html", key: "orders.html", label: "الطلبات", icon: "orders", badge: "18" },
        { href: "products.html", key: "products.html", label: "المنتجات", icon: "products" },
        { href: "categories.html", key: "categories.html", label: "التصنيفات", icon: "categories" },
        { href: "inventory.html", key: "inventory.html", label: "المخزون", icon: "inventory", badge: "8" },
        { href: "coupons.html", key: "coupons.html", label: "القسائم", icon: "coupons" },
      ],
    },
    {
      title: "العملاء والعمليات",
      items: [
        { href: "customers.html", key: "customers.html", label: "العملاء", icon: "customers" },
        { href: "orders.html", key: "shipping", label: "الشحن", icon: "shipping" },
        { href: "reports.html", key: "support", label: "الدعم", icon: "support" },
      ],
    },
    {
      title: "النظام",
      items: [
        { href: "settings.html", key: "settings.html", label: "الإعدادات", icon: "settings" },
        { href: "settings.html", key: "roles", label: "الصلاحيات", icon: "shield" },
        { href: "settings.html", key: "media", label: "الوسائط", icon: "media" },
        { href: "settings.html", key: "logs", label: "السجلات", icon: "logs" },
      ],
    },
  ];

  function currentFile() {
    return (location.pathname.split("/").pop() || "index.html").toLowerCase();
  }

  function isActive(item) {
    const file = currentFile();
    if (item.key === "index.html") return file === "index.html" || file === "";
    if (item.key === file) return true;
    return false;
  }

  function pageTitle() {
    const file = currentFile();
    for (const group of groups) {
      for (const item of group.items) {
        if (item.key === file || (file === "index.html" && item.key === "index.html")) {
          if (item.key === file || item.key === "index.html") return item.label;
        }
      }
    }
    const map = {
      "index.html": "لوحة التحكم",
      "orders.html": "الطلبات",
      "products.html": "المنتجات",
      "categories.html": "التصنيفات",
      "inventory.html": "المخزون",
      "coupons.html": "القسائم",
      "customers.html": "العملاء",
      "reports.html": "التقارير",
      "settings.html": "الإعدادات",
    };
    return map[file] || "لوحة الإدارة";
  }

  function navItems(mobile) {
    return groups
      .map((group) => {
        const links = group.items
          .map((item) => {
            const active = isActive(item);
            return `
            <a href="${item.href}" data-nav class="nav-link mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition ${
              active
                ? "bg-white font-semibold text-ink"
                : "text-white/70 hover:bg-white/10 hover:text-white"
            }">
              <span class="inline-flex size-8 items-center justify-center rounded-lg ${
                active ? "bg-accent text-white" : "bg-white/5"
              }">${ICONS[item.icon]}</span>
              <span class="flex-1">${item.label}</span>
              ${
                item.badge
                  ? `<span class="rounded-md px-1.5 py-0.5 text-[10px] font-semibold ${
                      active ? "bg-accent-soft text-accent" : "bg-white/10 text-white/80"
                    }">${item.badge}</span>`
                  : ""
              }
            </a>`;
          })
          .join("");
        return `
          <div class="mb-5">
            <p class="mb-2 px-3 text-[11px] font-semibold tracking-wide text-white/35">${group.title}</p>
            ${links}
          </div>`;
      })
      .join("");
  }

  function shellHTML() {
    return `
    <div class="flex min-h-screen">
      <aside class="admin-sidebar sticky top-0 hidden h-screen w-[280px] shrink-0 flex-col text-white lg:flex">
        <div class="flex h-[72px] items-center gap-3 border-b border-white/10 px-5">
          <div class="flex size-10 items-center justify-center rounded-xl bg-accent text-lg font-bold">أ</div>
          <div>
            <p class="text-base font-bold tracking-tight">أثير Control</p>
            <p class="text-[11px] text-white/50">مركز تشغيل التجارة</p>
          </div>
        </div>
        <div class="flex-1 overflow-y-auto px-3 py-5 scrollbar-none">${navItems()}</div>
        <div class="border-t border-white/10 p-4">
          <div class="rounded-2xl bg-white/5 p-3">
            <div class="flex items-center gap-3">
              <div class="flex size-10 items-center justify-center rounded-full bg-metal/30 text-sm font-bold text-metal">م.ن</div>
              <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold">مدير النظام</p>
                <p class="truncate text-xs text-white/45">ops@atheer.sa</p>
              </div>
            </div>
            <a href="../index.html" data-nav class="mt-3 flex items-center justify-center gap-2 rounded-xl bg-white/10 px-3 py-2 text-xs font-medium transition hover:bg-white/15">فتح واجهة المتجر</a>
          </div>
        </div>
      </aside>

      <div id="admin-drawer" class="fixed inset-0 z-50 hidden lg:hidden">
        <button type="button" class="absolute inset-0 bg-ink/50" data-close-drawer aria-label="إغلاق"></button>
        <aside class="drawer-panel absolute inset-y-0 right-0 flex w-[min(88vw,300px)] flex-col bg-ink text-white shadow-lift">
          <div class="flex h-16 items-center justify-between border-b border-white/10 px-4">
            <p class="font-bold">أثير Control</p>
            <button type="button" class="btn-press px-2 text-xl" data-close-drawer>×</button>
          </div>
          <div class="flex-1 overflow-y-auto px-3 py-4 scrollbar-none">${navItems(true)}</div>
        </aside>
      </div>

      <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-40 border-b border-line/80 bg-surface/90 backdrop-blur-xl">
          <div class="flex h-[72px] items-center gap-3 px-4 md:px-6">
            <button type="button" id="admin-menu" class="btn-press inline-flex size-10 items-center justify-center rounded-xl lg:hidden" aria-label="فتح القائمة">☰</button>
            <div class="min-w-0">
              <p class="text-[11px] font-medium text-muted">مركز التحكم · أثير</p>
              <h1 class="truncate text-lg font-semibold text-ink md:text-xl" data-page-title>${pageTitle()}</h1>
            </div>
            <form class="mx-auto hidden min-w-0 max-w-md flex-1 md:block" onsubmit="return false">
              <input class="h-11 w-full rounded-xl border border-line bg-[#f7f8fa] px-4 text-sm transition focus:border-accent focus:outline-none focus:ring-4 focus:ring-accent/15" placeholder="بحث سريع: طلب، منتج، عميل…" />
            </form>
            <div class="ms-auto flex items-center gap-1.5 md:gap-2">
              <button type="button" class="btn-press hidden rounded-xl bg-accent-soft px-3 py-2 text-sm text-accent sm:inline-flex">مساعد التشغيل</button>
              <button type="button" class="btn-press relative inline-flex size-10 items-center justify-center rounded-xl hover:bg-canvas" aria-label="الإشعارات">🔔<span class="absolute top-2 left-2 size-2 rounded-full bg-danger"></span></button>
              <a href="../index.html" data-nav class="btn-press hidden items-center rounded-xl border border-line px-3 py-2 text-sm xl:inline-flex">المتجر</a>
              <div class="ms-1 flex items-center gap-2 rounded-xl bg-canvas px-2 py-1.5 md:px-3">
                <div class="flex size-8 items-center justify-center rounded-full bg-ink text-xs font-bold text-white">م</div>
                <div class="hidden leading-tight md:block">
                  <p class="text-xs font-semibold text-ink">مدير النظام</p>
                  <p class="text-[10px] text-muted">صلاحيات كاملة</p>
                </div>
              </div>
            </div>
          </div>
        </header>
        <div id="admin-content-slot" class="flex-1"></div>
      </div>
    </div>`;
  }

  function bindTransitions(root) {
    root.querySelectorAll("a[data-nav], a[href$='.html']").forEach((link) => {
      link.addEventListener("click", (event) => {
        const href = link.getAttribute("href");
        if (!href || href.startsWith("#") || href.startsWith("http") || event.metaKey || event.ctrlKey) return;
        const main = document.querySelector("[data-admin-page]");
        if (!main) return;
        event.preventDefault();
        main.classList.remove("page-enter");
        main.classList.add("page-leave");
        setTimeout(() => {
          location.href = href;
        }, 160);
      });
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
    const mount = document.querySelector("[data-admin-shell]");
    const page = document.querySelector("[data-admin-page]");
    if (!mount || !page) return;

    mount.innerHTML = shellHTML();
    const slot = document.getElementById("admin-content-slot");
    slot.appendChild(page);
    page.classList.add("page-enter");

    const drawer = document.getElementById("admin-drawer");
    document.getElementById("admin-menu")?.addEventListener("click", () => {
      drawer.classList.remove("hidden");
    });
    drawer?.querySelectorAll("[data-close-drawer]").forEach((el) => {
      el.addEventListener("click", () => drawer.classList.add("hidden"));
    });

    bindTransitions(document);
  });
})();
