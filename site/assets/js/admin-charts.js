(function () {
  const sales = [
    { label: "١ يوليو", current: 28400, previous: 25100 },
    { label: "٢ يوليو", current: 31200, previous: 26800 },
    { label: "٣ يوليو", current: 29850, previous: 30100 },
    { label: "٤ يوليو", current: 35600, previous: 28900 },
    { label: "٥ يوليو", current: 27100, previous: 31400 },
    { label: "٦ يوليو", current: 40200, previous: 33600 },
    { label: "٧ يوليو", current: 38840, previous: 35200 },
    { label: "٨ يوليو", current: 42150, previous: 36700 },
    { label: "٩ يوليو", current: 36500, previous: 34100 },
    { label: "١٠ يوليو", current: 44800, previous: 39200 },
    { label: "١١ يوليو", current: 39200, previous: 37800 },
    { label: "١٢ يوليو", current: 47600, previous: 40100 },
    { label: "١٣ يوليو", current: 51200, previous: 42900 },
    { label: "١٤ يوليو", current: 48200, previous: 45500 },
  ];

  const hourly = [4, 2, 1, 6, 18, 27, 34, 31, 29, 41, 38, 22];

  function money(n) {
    return new Intl.NumberFormat("ar-SA", {
      style: "currency",
      currency: "SAR",
      maximumFractionDigits: 0,
    }).format(n);
  }

  function renderSalesChart() {
    const svg = document.getElementById("sales-chart");
    const tip = document.getElementById("chart-tip");
    if (!svg) return;

    const width = 640;
    const height = 220;
    const padX = 12;
    const padY = 16;
    const maxY =
      Math.max(...sales.flatMap((d) => [d.current, d.previous])) * 1.08;

    const toX = (i) =>
      padX + (i / Math.max(sales.length - 1, 1)) * (width - padX * 2);
    const toY = (v) => height - padY - (v / maxY) * (height - padY * 2);

    const current = sales.map((d, i) => ({ x: toX(i), y: toY(d.current), ...d }));
    const previous = sales.map((d, i) => ({ x: toX(i), y: toY(d.previous) }));

    const line = (pts) =>
      pts.map((p, i) => `${i ? "L" : "M"} ${p.x} ${p.y}`).join(" ");
    const area =
      line(current) +
      ` L ${current[current.length - 1].x} ${height - padY} L ${current[0].x} ${height - padY} Z`;

    let html = "";
    [0.25, 0.5, 0.75, 1].forEach((step) => {
      const y = height - padY - step * (height - padY * 2);
      html += `<line x1="12" x2="628" y1="${y}" y2="${y}" stroke="#e5e9ef" stroke-width="1" />`;
    });

    html += `
      <defs>
        <linearGradient id="salesGrad" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#0f5c57" stop-opacity="0.22" />
          <stop offset="100%" stop-color="#0f5c57" stop-opacity="0.02" />
        </linearGradient>
      </defs>
      <path d="${area}" fill="url(#salesGrad)" />
      <path d="${line(previous)}" fill="none" stroke="#9aa3b2" stroke-width="1.5" stroke-dasharray="4 4" />
      <path d="${line(current)}" fill="none" stroke="#0f5c57" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" />
    `;

    current.forEach((p, i) => {
      html += `<rect data-i="${i}" x="${p.x - 18}" y="0" width="36" height="220" fill="transparent" style="cursor:pointer" />`;
    });

    svg.innerHTML = html;

    const total = sales.reduce((s, d) => s + d.current, 0);
    const prev = sales.reduce((s, d) => s + d.previous, 0);
    const delta = ((total - prev) / prev) * 100;
    const totalEl = document.getElementById("sales-total");
    const deltaEl = document.getElementById("sales-delta");
    if (totalEl) totalEl.textContent = money(total);
    if (deltaEl) deltaEl.textContent = `+${delta.toFixed(1)}% مقابل الفترة السابقة`;

    let activeMark = null;
    svg.querySelectorAll("rect[data-i]").forEach((rect) => {
      rect.addEventListener("mouseenter", () => {
        const i = Number(rect.dataset.i);
        const p = current[i];
        if (activeMark) activeMark.remove();
        activeMark = document.createElementNS("http://www.w3.org/2000/svg", "g");
        activeMark.innerHTML = `
          <line x1="${p.x}" x2="${p.x}" y1="12" y2="204" stroke="#0b1220" stroke-opacity="0.12" />
          <circle cx="${p.x}" cy="${p.y}" r="4.5" fill="#fff" stroke="#0f5c57" stroke-width="2" />
        `;
        svg.appendChild(activeMark);
        const dlt = ((p.current - p.previous) / p.previous) * 100;
        tip.classList.remove("hidden");
        tip.innerHTML = `
          <p class="text-[11px] text-muted">${p.label}</p>
          <p class="mt-1 text-sm font-semibold tabular-nums">${money(p.current)}</p>
          <p class="mt-0.5 text-[11px] text-muted">السابق: ${money(p.previous)}</p>
          <p class="mt-1 text-[11px] font-semibold ${dlt >= 0 ? "text-success" : "text-danger"}">${dlt >= 0 ? "+" : ""}${dlt.toFixed(1)}%</p>
        `;
      });
    });

    svg.addEventListener("mouseleave", () => {
      tip.classList.add("hidden");
      if (activeMark) activeMark.remove();
      activeMark = null;
    });
  }

  function renderHourly() {
    const root = document.getElementById("hourly-bars");
    if (!root) return;
    const max = Math.max(...hourly);
    root.innerHTML = hourly
      .map((value, index) => {
        const h = Math.max(8, (value / max) * 100);
        return `<button type="button" class="group relative flex flex-1 flex-col items-center justify-end" aria-label="${value} طلب">
          <span class="pointer-events-none absolute -top-7 hidden rounded-md bg-ink px-1.5 py-0.5 text-[10px] font-semibold text-white group-hover:block">${value}</span>
          <span class="w-full rounded-t-md bg-[#c5d4d3] transition group-hover:bg-accent" style="height:${h}%"></span>
        </button>`;
      })
      .join("");
  }

  document.addEventListener("DOMContentLoaded", () => {
    renderSalesChart();
    renderHourly();
  });
})();
