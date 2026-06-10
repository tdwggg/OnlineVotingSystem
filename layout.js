(function () {
  function getSidebarElements() {
    const sidebar = document.getElementById("sidebar") || document.getElementById("dashboardSidebar");
    const overlay = document.getElementById("sidebarOverlay") || document.getElementById("dashboardSidebarOverlay");
    const toggleBtn = document.querySelector(".sidebarToggle, .dashboardSidebarToggle");
    return { sidebar, overlay, toggleBtn };
  }

  function setSidebarOpen(open) {
    const { sidebar, overlay, toggleBtn } = getSidebarElements();
    if (!sidebar) return;

    sidebar.classList.toggle("open", open);

    if (overlay) {
      overlay.classList.toggle("show", open);
      overlay.hidden = !open;
      overlay.setAttribute("aria-hidden", open ? "false" : "true");
    }

    document.body.classList.toggle("sidebar-open", open);

    if (toggleBtn) {
      toggleBtn.setAttribute("aria-expanded", open ? "true" : "false");
    }
  }

  window.toggleSidebar = function () {
    const { sidebar } = getSidebarElements();
    if (!sidebar) return;
    setSidebarOpen(!sidebar.classList.contains("open"));
  };

  window.closeSidebar = function () {
    setSidebarOpen(false);
  };

  document.addEventListener("DOMContentLoaded", function () {
    const { sidebar, overlay } = getSidebarElements();

    if (overlay) {
      overlay.addEventListener("click", closeSidebar);
    }

    if (sidebar) {
      sidebar.addEventListener("click", function (event) {
        const menuTarget = event.target.closest("a, button");
        if (menuTarget && window.innerWidth < 992) {
          closeSidebar();
        }
      });
    }

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape") {
        closeSidebar();
      }
    });

    window.addEventListener("resize", function () {
      if (window.innerWidth >= 992 && document.getElementById("dashboardSidebar")) {
        closeSidebar();
      }
    });
  });
})();
