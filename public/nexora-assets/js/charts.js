/* ==========================================================================
   NEXORA ADMIN — Charts Module
   Chart.js configurations for all chart types
   ========================================================================== */

(function () {
  "use strict";

  if (typeof Chart === "undefined") return;

  Chart.defaults.font.family = "'Inter', sans-serif";
  Chart.defaults.color = "#94A3B8";
  Chart.defaults.borderColor = "rgba(255,255,255,0.06)";

  /* ------------------------------------------------------------------
     Color palette (matches design tokens)
     ------------------------------------------------------------------ */
  const colors = {
    blue:   "#2563EB",
    cyan:   "#22D3EE",
    purple: "#6366F1",
    pink:   "#EC4899",
    green:  "#34D399",
    red:    "#F87171",
    yellow: "#FBBF24",
    indigo: "#818CF8",
    orange: "#FB923C",
    teal:   "#2DD4BF",
    muted:  "#475569",
  };

  const palette = [
    colors.blue, colors.cyan, colors.purple, colors.green,
    colors.red, colors.yellow, colors.indigo, colors.pink,
    colors.orange, colors.teal,
  ];

  function hexToRgb(hex, alpha) {
    const r = parseInt(hex.slice(1,3), 16);
    const g = parseInt(hex.slice(3,5), 16);
    const b = parseInt(hex.slice(5,7), 16);
    return `rgba(${r},${g},${b},${alpha})`;
  }

  /* ------------------------------------------------------------------
     1. Line Chart — Tren Pendapatan (replaces #chartRevenue)
     ------------------------------------------------------------------ */
  const lineEl = document.getElementById("chartRevenueLine");
  if (lineEl) {
    const ctx = lineEl.getContext("2d");
    const gradient = ctx.createLinearGradient(0, 0, 0, 240);
    gradient.addColorStop(0, hexToRgb(colors.blue, 0.35));
    gradient.addColorStop(1, hexToRgb(colors.blue, 0));

    new Chart(ctx, {
      type: "line",
      data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [
          {
            label: "Revenue",
            data: [42, 55, 49, 60, 58, 72, 68, 80, 77, 90, 86, 98],
            borderColor: colors.blue,
            backgroundColor: gradient,
            fill: true,
            tension: 0.4,
            pointRadius: 3,
            pointHoverRadius: 6,
            pointBackgroundColor: colors.blue,
            pointBorderColor: "#fff",
            pointBorderWidth: 2,
            borderWidth: 2.5,
          },
          {
            label: "Proyeksi",
            data: [38, 44, 47, 50, 53, 60, 64, 66, 70, 74, 79, 84],
            borderColor: colors.cyan,
            backgroundColor: "transparent",
            borderDash: [5, 5],
            tension: 0.4,
            pointRadius: 2,
            pointHoverRadius: 5,
            borderWidth: 2,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: "index", intersect: false },
        plugins: {
          legend: {
            position: "top",
            labels: { boxWidth: 12, padding: 16, font: { size: 11 } },
          },
          tooltip: {
            backgroundColor: "#132033",
            titleFont: { size: 12 },
            bodyFont: { size: 11 },
            borderColor: "#24354F",
            borderWidth: 1,
            padding: 10,
            cornerRadius: 8,
            callbacks: {
              label: function (ctx) { return ctx.dataset.label + ": $" + ctx.parsed.y + "k"; },
            },
          },
        },
        scales: {
          x: { grid: { display: false }, ticks: { font: { size: 10 } } },
          y: {
            grid: { color: "rgba(255,255,255,0.05)" },
            ticks: { callback: (v) => "$" + v + "k", font: { size: 10 } },
          },
        },
      },
    });
  }

  /* ------------------------------------------------------------------
     2. Doughnut Chart — Penggunaan Model AI (replaces #chartUsage)
     ------------------------------------------------------------------ */
  const doughnutEl = document.getElementById("chartAIModels");
  if (doughnutEl) {
    new Chart(doughnutEl.getContext("2d"), {
      type: "doughnut",
      data: {
        labels: ["GPT-4o", "Claude 4", "Image Gen", "Embeddings", "Fine-tune"],
        datasets: [{
          data: [35, 28, 18, 12, 7],
          backgroundColor: [colors.blue, colors.purple, colors.cyan, colors.indigo, colors.muted],
          borderWidth: 0,
          hoverOffset: 8,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: "70%",
        plugins: {
          legend: {
            position: "bottom",
            labels: { boxWidth: 10, padding: 14, font: { size: 11 } },
          },
          tooltip: {
            backgroundColor: "#132033",
            borderColor: "#24354F",
            borderWidth: 1,
            padding: 10,
            cornerRadius: 8,
            callbacks: {
              label: function (ctx) { return ctx.label + ": " + ctx.parsed + "%"; },
            },
          },
        },
      },
    });
  }

  /* ------------------------------------------------------------------
     3. Vertical Bar Chart — Request per Hari
     ------------------------------------------------------------------ */
  const barEl = document.getElementById("chartDailyRequests");
  if (barEl) {
    new Chart(barEl.getContext("2d"), {
      type: "bar",
      data: {
        labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
        datasets: [{
          label: "Requests",
          data: [120, 190, 150, 220, 260, 140, 95],
          backgroundColor: [
            colors.blue, colors.purple, colors.cyan,
            colors.indigo, colors.blue, colors.purple, colors.cyan,
          ],
          borderRadius: 6,
          maxBarThickness: 32,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: "#132033",
            borderColor: "#24354F",
            borderWidth: 1,
            padding: 10,
            cornerRadius: 8,
            callbacks: {
              label: function (ctx) { return ctx.parsed.y.toLocaleString() + " requests"; },
            },
          },
        },
        scales: {
          x: { grid: { display: false }, ticks: { font: { size: 10 } } },
          y: {
            grid: { color: "rgba(255,255,255,0.05)" },
            ticks: { font: { size: 10 } },
          },
        },
      },
    });
  }

  /* ------------------------------------------------------------------
     4. Horizontal Bar Chart — Performa Tim
     ------------------------------------------------------------------ */
  const hBarEl = document.getElementById("chartTeamPerformance");
  if (hBarEl) {
    new Chart(hBarEl.getContext("2d"), {
      type: "bar",
      data: {
        labels: ["Aditya", "Melati", "Randy", "Dewi", "Fitri", "Gilang"],
        datasets: [{
          label: "Task Selesai",
          data: [34, 28, 42, 19, 31, 25],
          backgroundColor: [
            colors.green, colors.cyan, colors.blue,
            colors.purple, colors.teal, colors.indigo,
          ],
          borderRadius: 6,
          maxBarThickness: 24,
        }],
      },
      options: {
        indexAxis: "y",
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: "#132033",
            borderColor: "#24354F",
            borderWidth: 1,
            padding: 10,
            cornerRadius: 8,
          },
        },
        scales: {
          x: {
            grid: { color: "rgba(255,255,255,0.05)" },
            ticks: { font: { size: 10 } },
          },
          y: {
            grid: { display: false },
            ticks: { font: { size: 10 } },
          },
        },
      },
    });
  }

  /* ------------------------------------------------------------------
     5. Stacked Bar Chart — Breakdown Biaya per Kuartal
     ------------------------------------------------------------------ */
  const stackedEl = document.getElementById("chartStackedCosts");
  if (stackedEl) {
    new Chart(stackedEl.getContext("2d"), {
      type: "bar",
      data: {
        labels: ["Q1", "Q2", "Q3", "Q4"],
        datasets: [
          { label: "Infrastructure", data: [45, 52, 38, 60], backgroundColor: colors.blue, borderRadius: 4 },
          { label: "Engineering",   data: [80, 72, 88, 95], backgroundColor: colors.cyan, borderRadius: 4 },
          { label: "Marketing",     data: [30, 45, 55, 40], backgroundColor: colors.purple, borderRadius: 4 },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "top",
            labels: { boxWidth: 12, padding: 14, font: { size: 11 } },
          },
          tooltip: {
            backgroundColor: "#132033",
            borderColor: "#24354F",
            borderWidth: 1,
            padding: 10,
            cornerRadius: 8,
            callbacks: {
              label: function (ctx) { return ctx.dataset.label + ": $" + ctx.parsed.y + "k"; },
            },
          },
        },
        scales: {
          x: {
            stacked: true,
            grid: { display: false },
            ticks: { font: { size: 10 } },
          },
          y: {
            stacked: true,
            grid: { color: "rgba(255,255,255,0.05)" },
            ticks: { callback: (v) => "$" + v + "k", font: { size: 10 } },
          },
        },
      },
    });
  }

  /* ------------------------------------------------------------------
     6. Polar Area Chart — Sumber Trafik
     ------------------------------------------------------------------ */
  const polarEl = document.getElementById("chartTrafficSources");
  if (polarEl) {
    new Chart(polarEl.getContext("2d"), {
      type: "polarArea",
      data: {
        labels: ["Organic Search", "Social Media", "Direct", "Referral", "Email", "Paid Ads"],
        datasets: [{
          data: [35, 22, 18, 12, 8, 5],
          backgroundColor: [
            hexToRgb(colors.blue, 0.7),
            hexToRgb(colors.cyan, 0.7),
            hexToRgb(colors.purple, 0.7),
            hexToRgb(colors.green, 0.7),
            hexToRgb(colors.yellow, 0.7),
            hexToRgb(colors.red, 0.7),
          ],
          borderColor: [colors.blue, colors.cyan, colors.purple, colors.green, colors.yellow, colors.red],
          borderWidth: 1.5,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "right",
            labels: { boxWidth: 10, padding: 12, font: { size: 10 } },
          },
          tooltip: {
            backgroundColor: "#132033",
            borderColor: "#24354F",
            borderWidth: 1,
            padding: 10,
            cornerRadius: 8,
            callbacks: {
              label: function (ctx) { return ctx.label + ": " + ctx.parsed + "%"; },
            },
          },
        },
        scales: {
          r: {
            grid: { color: "rgba(255,255,255,0.06)" },
            ticks: { display: false },
          },
        },
      },
    });
  }

  /* ------------------------------------------------------------------
     7. Radar Chart — Skor Produk
     ------------------------------------------------------------------ */
  const radarEl = document.getElementById("chartProductRadar");
  if (radarEl) {
    new Chart(radarEl.getContext("2d"), {
      type: "radar",
      data: {
        labels: ["UX", "Performance", "Security", "Features", "Support", "Price"],
        datasets: [
          {
            label: "Produk A",
            data: [85, 70, 90, 75, 65, 80],
            backgroundColor: hexToRgb(colors.blue, 0.15),
            borderColor: colors.blue,
            borderWidth: 2,
            pointBackgroundColor: colors.blue,
            pointBorderColor: "#fff",
            pointBorderWidth: 1.5,
            pointRadius: 4,
          },
          {
            label: "Produk B",
            data: [70, 85, 75, 90, 80, 60],
            backgroundColor: hexToRgb(colors.cyan, 0.15),
            borderColor: colors.cyan,
            borderWidth: 2,
            pointBackgroundColor: colors.cyan,
            pointBorderColor: "#fff",
            pointBorderWidth: 1.5,
            pointRadius: 4,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "top",
            labels: { boxWidth: 12, padding: 14, font: { size: 11 } },
          },
          tooltip: {
            backgroundColor: "#132033",
            borderColor: "#24354F",
            borderWidth: 1,
            padding: 10,
            cornerRadius: 8,
            callbacks: {
              label: function (ctx) { return ctx.dataset.label + ": " + ctx.parsed + "/100"; },
            },
          },
        },
        scales: {
          r: {
            angleLines: { color: "rgba(255,255,255,0.06)" },
            grid: { color: "rgba(255,255,255,0.06)" },
            pointLabels: { font: { size: 11 }, color: "#CBD5E1" },
            suggestedMin: 30,
            suggestedMax: 100,
            ticks: { stepSize: 20, backdropColor: "transparent", font: { size: 9 } },
          },
        },
      },
    });
  }

  /* ------------------------------------------------------------------
     8. Mixed Chart — Revenue (bar) + Orders (line)
     ------------------------------------------------------------------ */
  const mixedEl = document.getElementById("chartMixedRevenue");
  if (mixedEl) {
    new Chart(mixedEl.getContext("2d"), {
      type: "bar",
      data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug"],
        datasets: [
          {
            label: "Orders",
            data: [180, 220, 190, 240, 260, 210, 290, 310],
            backgroundColor: hexToRgb(colors.purple, 0.7),
            borderRadius: 6,
            maxBarThickness: 28,
            order: 2,
          },
          {
            label: "Revenue",
            type: "line",
            data: [12, 18, 14, 20, 22, 17, 25, 28],
            borderColor: colors.green,
            backgroundColor: colors.green,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: colors.green,
            pointBorderColor: "#fff",
            pointBorderWidth: 2,
            borderWidth: 3,
            fill: false,
            order: 1,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: "index", intersect: false },
        plugins: {
          legend: {
            position: "top",
            labels: { boxWidth: 12, padding: 14, font: { size: 11 } },
          },
          tooltip: {
            backgroundColor: "#132033",
            borderColor: "#24354F",
            borderWidth: 1,
            padding: 10,
            cornerRadius: 8,
          },
        },
        scales: {
          y: {
            grid: { color: "rgba(255,255,255,0.05)" },
            ticks: { font: { size: 10 } },
          },
          x: {
            grid: { display: false },
            ticks: { font: { size: 10 } },
          },
        },
      },
    });
  }

  /* ------------------------------------------------------------------
     9. Bubble Chart — Analisis Portofolio
     ------------------------------------------------------------------ */
  const bubbleEl = document.getElementById("chartPortfolio");
  if (bubbleEl) {
    new Chart(bubbleEl.getContext("2d"), {
      type: "bubble",
      data: {
        datasets: [
          {
            label: "Saham",
            data: [
              { x: 20, y: 30, r: 18 },
              { x: 40, y: 50, r: 12 },
              { x: 30, y: 25, r: 22 },
            ],
            backgroundColor: hexToRgb(colors.blue, 0.6),
            borderColor: colors.blue,
            borderWidth: 1.5,
          },
          {
            label: "Obligasi",
            data: [
              { x: 10, y: 15, r: 14 },
              { x: 25, y: 20, r: 10 },
            ],
            backgroundColor: hexToRgb(colors.green, 0.6),
            borderColor: colors.green,
            borderWidth: 1.5,
          },
          {
            label: "Crypto",
            data: [
              { x: 70, y: 80, r: 8 },
              { x: 55, y: 65, r: 6 },
            ],
            backgroundColor: hexToRgb(colors.red, 0.6),
            borderColor: colors.red,
            borderWidth: 1.5,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "top",
            labels: { boxWidth: 12, padding: 14, font: { size: 11 } },
          },
          tooltip: {
            backgroundColor: "#132033",
            borderColor: "#24354F",
            borderWidth: 1,
            padding: 10,
            cornerRadius: 8,
            callbacks: {
              label: function (ctx) {
                const d = ctx.raw;
                return ctx.dataset.label + " — Risk:" + d.x + "% Return:" + d.y + "%";
              },
            },
          },
        },
        scales: {
          x: {
            grid: { color: "rgba(255,255,255,0.05)" },
            title: { display: true, text: "Risk (%)", color: "#94A3B8", font: { size: 11 } },
            min: 0, max: 100,
            ticks: { font: { size: 10 } },
          },
          y: {
            grid: { color: "rgba(255,255,255,0.05)" },
            title: { display: true, text: "Return (%)", color: "#94A3B8", font: { size: 11 } },
            min: 0, max: 100,
            ticks: { font: { size: 10 } },
          },
        },
      },
    });
  }

  /* ------------------------------------------------------------------
     10. Scatter Chart — Korelasi Data
     ------------------------------------------------------------------ */
  const scatterEl = document.getElementById("chartScatter");
  if (scatterEl) {
    new Chart(scatterEl.getContext("2d"), {
      type: "scatter",
      data: {
        datasets: [
          {
            label: "Enterprise",
            data: [
              { x: 1200, y: 68 }, { x: 2400, y: 72 }, { x: 800, y: 55 },
              { x: 3100, y: 85 }, { x: 1800, y: 62 }, { x: 4500, y: 91 },
              { x: 900, y: 58 }, { x: 5000, y: 94 },
            ],
            backgroundColor: hexToRgb(colors.blue, 0.7),
            borderColor: colors.blue,
            borderWidth: 1.5,
            pointRadius: 6,
            pointHoverRadius: 8,
          },
          {
            label: "SMB",
            data: [
              { x: 200, y: 45 }, { x: 350, y: 52 }, { x: 150, y: 38 },
              { x: 500, y: 60 }, { x: 280, y: 48 }, { x: 420, y: 55 },
            ],
            backgroundColor: hexToRgb(colors.cyan, 0.7),
            borderColor: colors.cyan,
            borderWidth: 1.5,
            pointRadius: 6,
            pointHoverRadius: 8,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "top",
            labels: { boxWidth: 12, padding: 14, font: { size: 11 } },
          },
          tooltip: {
            backgroundColor: "#132033",
            borderColor: "#24354F",
            borderWidth: 1,
            padding: 10,
            cornerRadius: 8,
            callbacks: {
              label: function (ctx) {
                const d = ctx.raw;
                return ctx.dataset.label + " — Users: " + d.x.toLocaleString() + ", Engagement: " + d.y + "%";
              },
            },
          },
        },
        scales: {
          x: {
            grid: { color: "rgba(255,255,255,0.05)" },
            title: { display: true, text: "Active Users", color: "#94A3B8", font: { size: 11 } },
            ticks: { callback: (v) => v.toLocaleString(), font: { size: 10 } },
          },
          y: {
            grid: { color: "rgba(255,255,255,0.05)" },
            title: { display: true, text: "Engagement Rate (%)", color: "#94A3B8", font: { size: 11 } },
            min: 0, max: 100,
            ticks: { font: { size: 10 } },
          },
        },
      },
    });
  }

})();
