/* ==========================================================================
   NEXORA ADMIN — Core script
   ========================================================================== */

(function () {
  "use strict";

  const STORAGE_KEY = "nexora-theme";

  /* ---------------------------------------------------------------------
     Theme (dark / light)
     ------------------------------------------------------------------- */
  function initTheme() {
    const saved = localStorage.getItem(STORAGE_KEY) || "dark";
    document.documentElement.setAttribute("data-theme", saved);
    syncThemeToggleIcon(saved);
  }

  function toggleTheme() {
    const current = document.documentElement.getAttribute("data-theme") || "dark";
    const next = current === "dark" ? "light" : "dark";
    document.documentElement.setAttribute("data-theme", next);
    localStorage.setItem(STORAGE_KEY, next);
    syncThemeToggleIcon(next);
  }

  function syncThemeToggleIcon(theme) {
    const btn = document.getElementById("themeToggleBtn");
    if (!btn) return;
    const icon = btn.querySelector("i");
    if (!icon) return;
    icon.className = theme === "dark" ? "bi bi-sun" : "bi bi-moon-stars";
  }

  /* ---------------------------------------------------------------------
     Sidebar collapse (desktop) + mobile drawer
     BUG FIX: Close submenu when sidebar collapses
     ------------------------------------------------------------------- */
  function initSidebar() {
    const sidebar = document.getElementById("appSidebar");
    const collapseBtn = document.getElementById("sidebarCollapseBtn");
    const mobileToggle = document.getElementById("sidebarMobileToggle");
    const backdrop = document.getElementById("sidebarBackdrop");
    if (!sidebar) return;

    const savedCollapsed = localStorage.getItem("nexora-sidebar-collapsed") === "1";
    if (savedCollapsed) sidebar.classList.add("is-collapsed");

    if (collapseBtn) {
      collapseBtn.addEventListener("click", function () {
        sidebar.classList.toggle("is-collapsed");
        localStorage.setItem(
          "nexora-sidebar-collapsed",
          sidebar.classList.contains("is-collapsed") ? "1" : "0"
        );
        
        // Close all open submenus when collapsing
        if (sidebar.classList.contains("is-collapsed")) {
          document.querySelectorAll(".nav-submenu").forEach(submenu => {
            submenu.style.display = "none";
          });
          document.querySelectorAll(".submenu-caret").forEach(caret => {
            caret.style.transform = "rotate(0deg)";
          });
        }
      });
    }

    if (mobileToggle) {
      mobileToggle.addEventListener("click", function () {
        const isOpen = sidebar.classList.contains("is-mobile-open");
        sidebar.classList.toggle("is-mobile-open");
        if (backdrop) backdrop.classList.toggle("show");
        // Prevent body scroll when sidebar open on mobile
        document.body.style.overflow = isOpen ? "" : "hidden";
      });
    }

    if (backdrop) {
      backdrop.addEventListener("click", function () {
        sidebar.classList.remove("is-mobile-open");
        backdrop.classList.remove("show");
        document.body.style.overflow = "";
      });
    }

    // Mobile: when clicking a nav-link that leads somewhere, close sidebar and navigate
    sidebar.querySelectorAll(".nav-link[href]").forEach(function (link) {
      link.addEventListener("click", function (e) {
        if (window.innerWidth > 991) return;
        const href = link.getAttribute("href");
        if (!href || href === "#") return;
        // Close sidebar immediately so user sees the target page
        sidebar.classList.remove("is-mobile-open");
        if (backdrop) backdrop.classList.remove("show");
        document.body.style.overflow = "";
        // Browser navigates naturally via href
      });
    });
  }

  /* ---------------------------------------------------------------------
     Submenu accordion in sidebar
     ------------------------------------------------------------------- */
  function initSubmenus() {
    document.querySelectorAll("[data-submenu-toggle]").forEach(function (toggle) {
      toggle.addEventListener("click", function (e) {
        e.preventDefault();
        const sidebar = document.getElementById('appSidebar');
        // If sidebar is collapsed, do not open accordion here — let flyout handle navigation
        if (sidebar && sidebar.classList.contains('is-collapsed')) return;
        const parent = toggle.closest(".nav-item");
        const submenu = parent.querySelector(".nav-submenu");
        if (!submenu) return;
        const isOpen = submenu.style.display === "block";
        submenu.style.display = isOpen ? "none" : "block";
        const caret = toggle.querySelector(".submenu-caret");
        if (caret) caret.style.transform = isOpen ? "rotate(0deg)" : "rotate(90deg)";
      });
    });
  }

  /* ---------------------------------------------------------------------
     Tabs (data-tabs / data-tab-target) with effects
     ------------------------------------------------------------------- */
  function initTabs() {
    document.querySelectorAll(".tabs-modern").forEach(function (tabGroup) {
      const links = tabGroup.querySelectorAll(".tab-link");
      links.forEach(function (link) {
        link.addEventListener("click", function () {
          const targetSel = link.getAttribute("data-tab-target");
          links.forEach((l) => l.classList.remove("active"));
          link.classList.add("active");
          if (!targetSel) return;
          
          // Toggle panels with animation
          const allPanels = tabGroup.parentElement.querySelectorAll("[data-tab-panel]");
          allPanels.forEach((p) => (p.style.display = "none"));
          const target = document.querySelector(targetSel);
          if (target) {
            target.style.display = "block";
            // Trigger animation
            target.offsetHeight; // Force reflow
            target.style.animation = "none";
            setTimeout(() => {
              target.style.animation = "panelFadeIn 0.25s ease";
            }, 10);
          }
        });
      });
    });
  }

  /* ---------------------------------------------------------------------
     Toast notifications
     ------------------------------------------------------------------- */
  function ensureToastStack() {
    let stack = document.querySelector(".toast-stack");
    if (!stack) {
      stack = document.createElement("div");
      stack.className = "toast-stack";
      document.body.appendChild(stack);
    }
    return stack;
  }

  function showToast(message, type) {
    type = type || "default";
    const stack = ensureToastStack();
    const toast = document.createElement("div");
    toast.className = "toast-modern " + type;
    const iconMap = {
      success: "bi-check-circle-fill",
      danger: "bi-exclamation-circle-fill",
      default: "bi-info-circle-fill",
    };
    const titleMap = {
      success: "Berhasil",
      danger: "Error",
      default: "Informasi",
    };
    toast.innerHTML =
      '<i class="bi ' +
      (iconMap[type] || iconMap.default) +
      '"></i><div class="toast-content"><strong>' +
      (titleMap[type] || titleMap.default) +
      '</strong>' + message +
      "</div>";
    stack.appendChild(toast);
    setTimeout(function () {
      toast.style.animation = "toast-out 0.25s ease forwards";
      setTimeout(function () {
        toast.remove();
      }, 250);
    }, 3500);
  }
  window.NexoraToast = showToast;

  /* ---------------------------------------------------------------------
     Active nav-link from current page (for multi-page templates)
     ------------------------------------------------------------------- */
  function markActiveNav() {
    const path = window.location.pathname.split("/").pop() || "index.html";
    document.querySelectorAll(".sidebar-nav .nav-link[href]").forEach(function (link) {
      const href = link.getAttribute("href");
      if (href === path) {
        link.closest(".nav-item").classList.add("active");
      }
    });
  }

  /* ---------------------------------------------------------------------
     Settings page — Live change indicators
     ------------------------------------------------------------------- */
  function initSettingsEffects() {
    document.querySelectorAll(".setting-item [data-setting]").forEach(function (el) {
      el.addEventListener("change", function () {
        const item = el.closest(".setting-item");
        if (!item) return;
        
        // Remove existing indicator
        const existing = item.querySelector(".change-indicator");
        if (existing) existing.remove();
        
        // Add change indicator with animation
        const indicator = document.createElement("div");
        indicator.className = "change-indicator";
        indicator.innerHTML = '<i class="bi bi-check-circle-fill"></i> Tersimpan';
        
        const control = item.querySelector(".setting-control");
        if (control) control.appendChild(indicator);
        
        // Remove after animation
        setTimeout(() => {
          indicator.style.opacity = "0";
          indicator.style.transition = "opacity 0.3s ease";
          setTimeout(() => indicator.remove(), 300);
        }, 2500);
      });
    });
  }

  /* ---------------------------------------------------------------------
     Settings panels — switch visible panel when left menu clicked
     ------------------------------------------------------------------- */
  function initSettingsPanels() {
    const menu = document.querySelectorAll('.settings-menu .nav-link[data-settings-target]');
    if (!menu || menu.length === 0) return;
    menu.forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        // clear active
        document.querySelectorAll('.settings-menu .nav-item').forEach(i => i.classList.remove('active'));
        link.closest('.nav-item').classList.add('active');

        // show target
        const target = document.querySelector(link.getAttribute('data-settings-target'));
        if (!target) return;
        document.querySelectorAll('.settings-panel').forEach(p => (p.style.display = 'none'));
        target.style.display = 'block';
      });
    });

    // sidebar compact switch (in settings panel)
    const compactSwitch = document.getElementById('sidebarCompactSwitch');
    const sidebar = document.getElementById('appSidebar');
    if (compactSwitch && sidebar) {
      compactSwitch.addEventListener('change', function () {
        if (compactSwitch.checked) {
          sidebar.classList.add('is-collapsed');
          localStorage.setItem('nexora-sidebar-collapsed', '1');
        } else {
          sidebar.classList.remove('is-collapsed');
          localStorage.setItem('nexora-sidebar-collapsed', '0');
        }
      });
    }

    // dark mode switch in settings
    const darkSwitch = document.getElementById('darkModeSwitch');
    if (darkSwitch) {
      darkSwitch.addEventListener('change', function () {
        const theme = darkSwitch.checked ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem(STORAGE_KEY, theme);
        syncThemeToggleIcon(theme);
      });
    }
  }

  /* ---------------------------------------------------------------------
     Sidebar hover tooltips / flyouts when collapsed
     ------------------------------------------------------------------- */
  function initSidebarFlyouts() {
    const sidebar = document.getElementById('appSidebar');
    if (!sidebar) return;

    // create a single flyout element
    const flyout = document.createElement('div');
    flyout.className = 'sidebar-flyout';
    document.body.appendChild(flyout);
    let hideTimer = null;

    function cancelHide() {
      if (hideTimer) {
        clearTimeout(hideTimer);
        hideTimer = null;
      }
    }

    function scheduleHide() {
      cancelHide();
      hideTimer = setTimeout(function () {
        flyout.style.display = 'none';
        flyout.innerHTML = '';
      }, 140);
    }

    function showFlyout(link) {
      if (!sidebar.classList.contains('is-collapsed')) return;
      cancelHide();
      const rect = link.getBoundingClientRect();
      const label = link.querySelector('.nav-label-text')?.innerText || link.innerText;
      flyout.innerHTML = '';

      const main = document.createElement('div');
      main.className = 'flyout-main';
      const href = link.getAttribute('href') || '#';
      main.innerHTML = '<a class="flyout-main-link" href="' + href + '"><i class="bi ' + (link.querySelector('i')?.className || '') + ' me-2"></i>' + label + '</a>';
      flyout.appendChild(main);

      const submenu = link.closest('.nav-item')?.querySelector('.nav-submenu');
      if (submenu) {
        const clone = document.createElement('ul');
        clone.className = 'flyout-submenu';
        Array.from(submenu.querySelectorAll('.nav-item')).forEach(function (ni) {
          const a = ni.querySelector('.nav-link');
          if (!a) return;
          const li = document.createElement('li');
          li.className = 'flyout-subitem';
          const ahref = a.getAttribute('href') || '#';
          const text = a.querySelector('.nav-label-text')?.innerText || a.innerText;
          li.innerHTML = '<a href="' + ahref + '">' + (a.querySelector('i') ? ('<i class="bi ' + a.querySelector('i').className + ' me-2"></i>') : '') + '<span>' + text + '</span></a>';
          clone.appendChild(li);
        });
        flyout.appendChild(clone);
      }

      flyout.style.display = 'block';
      flyout.style.top = Math.max(10, rect.top - 4) + 'px';
      const flyoutWidth = flyout.offsetWidth || 260;
      flyout.style.left = Math.min(window.innerWidth - flyoutWidth - 12, rect.right + 2) + 'px';
    }

    function hideFlyout() { scheduleHide(); }

    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
      link.addEventListener('mouseenter', () => showFlyout(link));
    });

    flyout.addEventListener('mouseenter', cancelHide);
    flyout.addEventListener('mouseleave', hideFlyout);
    sidebar.addEventListener('mouseleave', hideFlyout);
    sidebar.addEventListener('mouseenter', cancelHide);
  }

  /* ---------------------------------------------------------------------
     Notification flyout (click to toggle)
     ------------------------------------------------------------------- */
  function initNotificationFlyout() {
    const btn = document.querySelector('.icon-btn[aria-label="Notifikasi"]');
    if (!btn) return;
    const fly = document.createElement('div');
    fly.className = 'notification-flyout';
    fly.innerHTML = '<div class="nf-header">Notifikasi</div><ul class="nf-list">'
      + '<li><a href="#"><i class="bi bi-bell-fill"></i><div class="nf-message"><span><strong>3 tugas baru</strong></span><span class="nf-time">Baru saja</span></div></a></li>'
      + '<li><a href="#"><i class="bi bi-cloud-check-fill"></i><div class="nf-message"><span><strong>Backup selesai</strong></span><span class="nf-time">10 menit lalu</span></div></a></li>'
      + '<li><a href="#"><i class="bi bi-credit-card-fill"></i><div class="nf-message"><span><strong>Pembayaran berhasil</strong></span><span class="nf-time">1 jam lalu</span></div></a></li>'
      + '</ul><div class="nf-footer"><a href="#">Lihat semua notifikasi</a></div>';
    document.body.appendChild(fly);

    function position() {
      const r = btn.getBoundingClientRect();
      fly.style.top = (r.bottom + 10) + 'px';
      fly.style.left = Math.max(14, Math.min(window.innerWidth - fly.offsetWidth - 14, r.left - 20)) + 'px';
    }

    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      if (fly.classList.contains('open')) {
        fly.classList.remove('open');
      } else {
        position();
        fly.classList.add('open');
      }
    });

    window.addEventListener('resize', position);
    document.addEventListener('click', function () { fly.classList.remove('open'); });
    fly.addEventListener('click', function (e) { e.stopPropagation(); });
  }

  /* ---------------------------------------------------------------------
     Tooltip functionality
     ------------------------------------------------------------------- */
  function initTooltips() {
    document.querySelectorAll(".tooltip-wrapper").forEach(function (wrapper) {
      // Tooltips are handled by CSS :hover
    });
  }

  /* ---------------------------------------------------------------------
     Custom select replacement for enhanced option styling
     Targets <select data-custom-select>
     ------------------------------------------------------------------- */
  function initCustomSelects() {
    document.querySelectorAll('select.form-select-modern[data-custom-select]').forEach(function (sel) {
      if (sel.__customized) return;
      sel.__customized = true;
      const wrapper = document.createElement('div');
      wrapper.className = 'custom-select-wrapper';
      sel.style.display = 'none';
      sel.parentNode.insertBefore(wrapper, sel);
      wrapper.appendChild(sel);

      const display = document.createElement('button');
      display.type = 'button';
      display.className = 'custom-select-display';
      display.textContent = sel.options[sel.selectedIndex]?.text || '';
      wrapper.appendChild(display);

      const list = document.createElement('ul');
      list.className = 'custom-select-list';
      Array.from(sel.options).forEach(function (opt) {
        const li = document.createElement('li');
        li.className = 'custom-select-option';
        li.textContent = opt.text;
        li.dataset.value = opt.value;
        if (opt.disabled) li.classList.add('disabled');
        if (opt.selected) li.classList.add('selected');
        li.addEventListener('click', function () {
          sel.value = li.dataset.value;
          sel.dispatchEvent(new Event('change'));
          display.textContent = li.textContent;
          list.classList.remove('open');
        });
        list.appendChild(li);
      });
      wrapper.appendChild(list);

      display.addEventListener('click', function (e) {
        e.stopPropagation();
        list.classList.toggle('open');
      });

      document.addEventListener('click', function () { list.classList.remove('open'); });
    });
  }

  /* ---------------------------------------------------------------------
     Chart.js global defaults + sample chart factories
     Only runs if Chart.js is present on the page
     ------------------------------------------------------------------- */
  function initCharts() {
    if (typeof Chart === "undefined") return;

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = "#8B92B8";
    Chart.defaults.borderColor = "rgba(255,255,255,0.06)";

    const lineEl = document.getElementById("chartRevenue");
    if (lineEl) {
      const ctx = lineEl.getContext("2d");
      const gradient = ctx.createLinearGradient(0, 0, 0, 260);
      gradient.addColorStop(0, "rgba(99,102,241,0.35)");
      gradient.addColorStop(1, "rgba(99,102,241,0)");

      new Chart(ctx, {
        type: "line",
        data: {
          labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [
            {
              label: "Revenue",
              data: [42, 55, 49, 60, 58, 72, 68, 80, 77, 90, 86, 98],
              borderColor: "#6366F1",
              backgroundColor: gradient,
              fill: true,
              tension: 0.4,
              pointRadius: 0,
              pointHoverRadius: 5,
              pointBackgroundColor: "#6366F1",
              borderWidth: 2.5,
            },
            {
              label: "Forecast",
              data: [38, 44, 47, 50, 53, 60, 64, 66, 70, 74, 79, 84],
              borderColor: "#22D3EE",
              backgroundColor: "transparent",
              borderDash: [5, 5],
              tension: 0.4,
              pointRadius: 0,
              borderWidth: 2,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: { mode: "index", intersect: false },
          plugins: { legend: { display: false } },
          scales: {
            x: { grid: { display: false } },
            y: { grid: { color: "rgba(255,255,255,0.05)" }, ticks: { callback: (v) => "$" + v + "k" } },
          },
        },
      });
    }

    const doughnutEl = document.getElementById("chartUsage");
    if (doughnutEl) {
      new Chart(doughnutEl.getContext("2d"), {
        type: "doughnut",
        data: {
          labels: ["GPT Tasks", "Image Gen", "Embeddings", "Other"],
          datasets: [
            {
              data: [42, 26, 18, 14],
              backgroundColor: ["#6366F1", "#8B5CF6", "#22D3EE", "#353C66"],
              borderWidth: 0,
              hoverOffset: 6,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: "72%",
          plugins: { legend: { position: "bottom", labels: { boxWidth: 10, padding: 16 } } },
        },
      });
    }

    const barEl = document.getElementById("chartBar");
    if (barEl) {
      new Chart(barEl.getContext("2d"), {
        type: "bar",
        data: {
          labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
          datasets: [
            {
              label: "Requests",
              data: [120, 190, 150, 220, 260, 140, 95],
              backgroundColor: "#6366F1",
              borderRadius: 6,
              maxBarThickness: 28,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: { grid: { display: false } },
            y: { grid: { color: "rgba(255,255,255,0.05)" } },
          },
        },
      });
    }
  }

  /* ---------------------------------------------------------------------
     Animated counters for stat cards (data-counter="1234")
     ------------------------------------------------------------------- */
  function initCounters() {
    document.querySelectorAll("[data-counter]").forEach(function (el) {
      const target = parseFloat(el.getAttribute("data-counter"));
      const decimals = (el.getAttribute("data-counter").split(".")[1] || "").length;
      const duration = 900;
      const start = performance.now();

      function tick(now) {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        const value = target * eased;
        el.textContent = value.toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d)\.)|\B(?=(\d{3})+(?!\d)$)/g, ",");
        if (progress < 1) requestAnimationFrame(tick);
      }
      requestAnimationFrame(tick);
    });
  }

  /* ---------------------------------------------------------------------
     Accordion toggle
     ------------------------------------------------------------------- */
  function initAccordion() {
    document.querySelectorAll("[data-accordion-toggle]").forEach(function (header) {
      header.addEventListener("click", function () {
        const body = header.nextElementSibling;
        if (!body) return;
        const isOpen = body.classList.contains("open");
        // Tutup semua item dalam accordion yang sama (accordion behavior)
        const accordion = header.closest(".accordion-modern");
        if (accordion) {
          accordion.querySelectorAll(".accordion-body").forEach(function (b) {
            b.classList.remove("open");
          });
          accordion.querySelectorAll(".accordion-header").forEach(function (h) {
            h.classList.remove("open");
          });
        }
        if (!isOpen) {
          body.classList.add("open");
          header.classList.add("open");
        }
      });
    });
  }

  /* ---------------------------------------------------------------------
     Alert dismiss
     ------------------------------------------------------------------- */
  function initAlertDismiss() {
    document.querySelectorAll(".alert .alert-close").forEach(function (btn) {
      btn.addEventListener("click", function () {
        const alert = btn.closest(".alert");
        if (alert) {
          alert.style.transition = "opacity 0.2s ease, transform 0.2s ease";
          alert.style.opacity = "0";
          alert.style.transform = "translateX(10px)";
          setTimeout(function () { alert.remove(); }, 200);
        }
      });
    });
  }

  /* ---------------------------------------------------------------------
     Tag remove (click X to remove tag)
     ------------------------------------------------------------------- */
  function initTagRemove() {
    document.querySelectorAll(".tag i.bi-x").forEach(function (x) {
      x.addEventListener("click", function (e) {
        e.stopPropagation();
        const tag = x.closest(".tag");
        if (tag) {
          tag.style.transition = "opacity 0.15s ease";
          tag.style.opacity = "0";
          setTimeout(function () { tag.remove(); }, 150);
        }
      });
    });
  }

  /* ---------------------------------------------------------------------
     Dropzone click simulation (alert on click)
     ------------------------------------------------------------------- */
  function initDropzone() {
    document.querySelectorAll(".dropzone").forEach(function (dz) {
      dz.addEventListener("click", function () {
        NexoraToast("Fitur unggah file akan dibuka di sini.", "default");
      });
      // Drag over visual
      dz.addEventListener("dragover", function (e) {
        e.preventDefault();
        dz.classList.add("dragover");
      });
      dz.addEventListener("dragleave", function () {
        dz.classList.remove("dragover");
      });
      dz.addEventListener("drop", function (e) {
        e.preventDefault();
        dz.classList.remove("dragover");
        NexoraToast("File diterima! (simulasi)", "success");
      });
    });
  }

  /* ---------------------------------------------------------------------
     Dropzone file remove (X button on preview files)
     ------------------------------------------------------------------- */
  function initDropzoneFileRemove() {
    document.querySelectorAll(".dropzone-file .bi-x").forEach(function (x) {
      x.addEventListener("click", function () {
        const fileEl = x.closest(".dropzone-file");
        if (fileEl) {
          fileEl.style.transition = "opacity 0.15s ease";
          fileEl.style.opacity = "0";
          setTimeout(function () { fileEl.remove(); }, 150);
        }
      });
    });
  }

  /* ---------------------------------------------------------------------
     Rating: ensure only one can be selected at a time
     (handled by HTML radio name, but re-check on init)
     ------------------------------------------------------------------- */
  function initRatings() {
    document.querySelectorAll(".rating input[type=radio]").forEach(function (radio) {
      radio.addEventListener("change", function () {
        // Radio group handles this automatically via name attribute
      });
    });
  }

  /* ---------------------------------------------------------------------
     Copy-to-clipboard for documentation code snippets
     ------------------------------------------------------------------- */
  function initCopyButtons() {
    document.querySelectorAll("[data-copy-target]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        const target = document.querySelector(btn.getAttribute("data-copy-target"));
        if (!target) return;
        const text = target.innerText;
        navigator.clipboard.writeText(text).then(function () {
          const original = btn.innerHTML;
          btn.innerHTML = '<i class="bi bi-check2"></i> Copied';
          setTimeout(function () {
            btn.innerHTML = original;
          }, 1600);
        });
      });
    });
  }

  /* ---------------------------------------------------------------------
     Drawer (slide-in panel) toggle
     ------------------------------------------------------------------- */
  function initDrawers() {
    document.querySelectorAll("[data-drawer-toggle]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        const target = document.querySelector(btn.getAttribute("data-drawer-toggle"));
        if (!target) return;
        target.classList.toggle("open");
        const backdrop = target.parentElement.querySelector(".drawer-backdrop");
        if (backdrop) backdrop.classList.toggle("open", target.classList.contains("open"));
      });
    });
    document.querySelectorAll(".drawer-close, .drawer-backdrop").forEach(function (el) {
      el.addEventListener("click", function () {
        const drawer = el.closest(".drawer, .drawer-backdrop")?.parentElement?.querySelector(".drawer");
        if (drawer) {
          drawer.classList.remove("open");
          const backdrop = drawer.parentElement.querySelector(".drawer-backdrop");
          if (backdrop) backdrop.classList.remove("open");
        }
      });
    });
  }

  /* ---------------------------------------------------------------------
     Command Palette (Cmd+K)
     ------------------------------------------------------------------- */
  function initCommandPalette() {
    const backdrop = document.getElementById("cmdPalette");
    const input = backdrop?.querySelector(".cmd-search-wrap input");
    if (!backdrop) return;

    function open() {
      backdrop.classList.add("open");
      if (input) setTimeout(function () { input.focus(); }, 100);
    }
    function close() { backdrop.classList.remove("open"); }

    document.addEventListener("keydown", function (e) {
      if ((e.metaKey || e.ctrlKey) && e.key === "k") {
        e.preventDefault();
        backdrop.classList.contains("open") ? close() : open();
      }
    });
    backdrop.addEventListener("click", function (e) {
      if (e.target === backdrop) close();
    });
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && backdrop.classList.contains("open")) close();
    });

    // Filter items
    if (input) {
      input.addEventListener("input", function () {
        const q = input.value.toLowerCase();
        document.querySelectorAll(".cmd-item").forEach(function (item) {
          const text = item.textContent.toLowerCase();
          item.style.display = text.includes(q) ? "flex" : "none";
        });
      });
    }
  }

  /* ---------------------------------------------------------------------
     Back to Top
     ------------------------------------------------------------------- */
  function initBackToTop() {
    const btn = document.getElementById("backToTop");
    if (!btn) return;
    window.addEventListener("scroll", function () {
      btn.classList.toggle("visible", window.scrollY > 400);
    });
    btn.addEventListener("click", function () {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  }

  /* ---------------------------------------------------------------------
     Fullscreen Toggle
     ------------------------------------------------------------------- */
  function initFullscreenToggle() {
    document.querySelectorAll("[data-fullscreen-toggle]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        if (!document.fullscreenElement) {
          document.documentElement.requestFullscreen();
        } else {
          document.exitFullscreen();
        }
      });
    });
  }

  /* ---------------------------------------------------------------------
     Lightbox
     ------------------------------------------------------------------- */
  function initLightbox() {
    const backdrop = document.getElementById("lightboxBackdrop");
    if (!backdrop) return;
    const img = backdrop.querySelector("img");
    const caption = backdrop.querySelector(".lightbox-caption");
    const close = backdrop.querySelector(".lightbox-close");

    document.querySelectorAll("[data-lightbox]").forEach(function (el) {
      el.addEventListener("click", function () {
        const src = el.getAttribute("data-lightbox") || el.src || el.href;
        if (img) img.src = src;
        if (caption) {
          const txt = el.getAttribute("data-lightbox-caption") || el.getAttribute("alt") || "";
          caption.textContent = txt;
          caption.style.display = txt ? "block" : "none";
        }
        backdrop.classList.add("open");
      });
    });

    function closeLightbox() { backdrop.classList.remove("open"); }
    if (close) close.addEventListener("click", closeLightbox);
    backdrop.addEventListener("click", function (e) { if (e.target === backdrop) closeLightbox(); });
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && backdrop.classList.contains("open")) closeLightbox();
    });
  }

  /* ---------------------------------------------------------------------
     Progress Ring animation
     ------------------------------------------------------------------- */
  function initProgressRings() {
    document.querySelectorAll(".progress-ring").forEach(function (ring) {
      const fg = ring.querySelector(".ring-fg");
      if (!fg) return;
      const pct = parseFloat(ring.getAttribute("data-pct") || "0");
      const radius = parseFloat(fg.getAttribute("r") || "30");
      const circ = 2 * Math.PI * radius;
      fg.style.strokeDasharray = circ;
      // Animate
      ring.offsetHeight; // force reflow
      const targetDash = circ - (circ * pct) / 100;
      fg.style.strokeDashoffset = targetDash;
    });
  }

  /* ---------------------------------------------------------------------
     Countdown timer
     ------------------------------------------------------------------- */
  function initCountdowns() {
    document.querySelectorAll(".countdown[data-target]").forEach(function (el) {
      const target = new Date(el.getAttribute("data-target")).getTime();
      if (isNaN(target)) return;

      function tick() {
        const now = Date.now();
        const diff = Math.max(0, target - now);
        const days = Math.floor(diff / 86400000);
        const hours = Math.floor((diff % 86400000) / 3600000);
        const mins = Math.floor((diff % 3600000) / 60000);
        const secs = Math.floor((diff % 60000) / 1000);

        var dEl = el.querySelector("[data-cd='days']");
        var hEl = el.querySelector("[data-cd='hours']");
        var mEl = el.querySelector("[data-cd='mins']");
        var sEl = el.querySelector("[data-cd='secs']");
        if (dEl) dEl.textContent = String(days).padStart(2, "0");
        if (hEl) hEl.textContent = String(hours).padStart(2, "0");
        if (mEl) mEl.textContent = String(mins).padStart(2, "0");
        if (sEl) sEl.textContent = String(secs).padStart(2, "0");
      }
      tick();
      setInterval(tick, 1000);
    });
  }

  /* ---------------------------------------------------------------------
     Carousel auto-slide
     ------------------------------------------------------------------- */
  function initCarousels() {
    document.querySelectorAll(".carousel-modern").forEach(function (carousel) {
      const track = carousel.querySelector(".carousel-track");
      const slides = carousel.querySelectorAll(".carousel-slide");
      const prev = carousel.querySelector(".carousel-prev");
      const next = carousel.querySelector(".carousel-next");
      const dots = carousel.querySelectorAll(".carousel-dots span");
      if (!track || !slides.length) return;
      let idx = 0;

      function goTo(n) {
        idx = (n + slides.length) % slides.length;
        track.style.transform = "translateX(-" + (idx * 100) + "%)";
        dots.forEach(function (d, i) { d.classList.toggle("active", i === idx); });
      }

      if (prev) prev.addEventListener("click", function () { goTo(idx - 1); });
      if (next) next.addEventListener("click", function () { goTo(idx + 1); });
      dots.forEach(function (d, i) { d.addEventListener("click", function () { goTo(i); }); });

      // auto-play
      var delay = parseInt(carousel.getAttribute("data-autoplay") || "0");
      if (delay > 0) {
        setInterval(function () { goTo(idx + 1); }, delay);
      }

      goTo(0);
    });
  }

  /* ---------------------------------------------------------------------
     Price toggle (monthly/yearly)
     ------------------------------------------------------------------- */
  function initPriceToggles() {
    document.querySelectorAll(".price-toggle .pt-opt").forEach(function (opt) {
      opt.addEventListener("click", function () {
        var parent = opt.closest(".price-toggle");
        parent.querySelectorAll(".pt-opt").forEach(function (o) { o.classList.remove("active"); });
        opt.classList.add("active");
      });
    });
  }

  /* ---------------------------------------------------------------------
     Context Menu (right-click)
     ------------------------------------------------------------------- */
  function initContextMenus() {
    document.querySelectorAll("[data-context-menu]").forEach(function (trigger) {
      trigger.addEventListener("contextmenu", function (e) {
        e.preventDefault();
        var menuId = trigger.getAttribute("data-context-menu");
        var menu = document.querySelector(menuId);
        if (!menu) return;

        // Close other open menus
        document.querySelectorAll(".context-menu.open").forEach(function (m) {
          if (m !== menu) m.classList.remove("open");
        });

        menu.style.left = Math.min(e.clientX, window.innerWidth - menu.offsetWidth - 10) + "px";
        menu.style.top = Math.min(e.clientY, window.innerHeight - menu.offsetHeight - 10) + "px";
        menu.style.position = "fixed";
        menu.classList.add("open");
      });
    });
    document.addEventListener("click", function () {
      document.querySelectorAll(".context-menu.open").forEach(function (m) { m.classList.remove("open"); });
    });
  }

  /* ---------------------------------------------------------------------

  /* ---------------------------------------------------------------------
     Button loading — tambah class is-loading saat klik, disabled otomatis
     ------------------------------------------------------------------- */
  function initBtnLoading() {
    document.addEventListener("click", function (e) {
      const btn = e.target.closest(".btn-loading");
      if (!btn || btn.classList.contains("is-loading")) return;
      btn.classList.add("is-loading");
      if (btn.type === "submit" && btn.form) {
        btn.form.addEventListener("submit", function onFormSubmit() {
          btn.disabled = true;
          btn.form.removeEventListener("submit", onFormSubmit);
        });
      } else {
        btn.disabled = true;
      }
    });
  }

  /* ---------------------------------------------------------------------
     Input skeleton — otomatis is-loading saat form submit
     ------------------------------------------------------------------- */
  function initInputSkeleton() {
    document.addEventListener("submit", function (e) {
      var form = e.target;
      form.querySelectorAll(".input-skeleton").forEach(function (el) {
        el.classList.add("is-loading");
      });
    });
  }

  /* ---------------------------------------------------------------------
     Init on DOM ready
     ------------------------------------------------------------------- */
  document.addEventListener("DOMContentLoaded", function () {
    initTheme();
    initSidebar();
    initSubmenus();
    initTabs();
    markActiveNav();
    initCharts();
    initCounters();
    initCopyButtons();
    initTooltips();
    initSettingsEffects();
    initSettingsPanels();
    initSidebarFlyouts();
    initCustomSelects();
    initNotificationFlyout();
    initAccordion();
    initAlertDismiss();
    initTagRemove();
    initDropzone();
    initDropzoneFileRemove();
    initRatings();
    initDrawers();
    initCommandPalette();
    initBackToTop();
    initFullscreenToggle();
    initLightbox();
    initProgressRings();
    initCountdowns();
    initCarousels();
    initPriceToggles();
    initContextMenus();
    initBtnLoading();
    initInputSkeleton();

    const themeBtn = document.getElementById("themeToggleBtn");
    if (themeBtn) themeBtn.addEventListener("click", toggleTheme);
  });
})();
