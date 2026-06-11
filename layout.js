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

  function logNavDebug(source) {
    const toggleBtn = document.querySelector(".sidebarToggle, .dashboardSidebarToggle");
    const positionNavBar = document.querySelector(".positionNavBar");
    const style = toggleBtn ? window.getComputedStyle(toggleBtn) : null;
    // #region agent log
    fetch('http://127.0.0.1:7395/ingest/ed9497cf-da7d-4467-b353-51612ec7ddba',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'d12c0f'},body:JSON.stringify({sessionId:'d12c0f',location:'layout.js:logNavDebug',message:'nav visibility snapshot',data:{source,innerWidth:window.innerWidth,hasToggle:!!toggleBtn,toggleDisplay:style?style.display:null,toggleVisibility:style?style.visibility:null,toggleOpacity:style?style.opacity:null,hasD_lg_none:toggleBtn?toggleBtn.classList.contains('d-lg-none'):null,hasPositionNavBar:!!positionNavBar,page:location.pathname.split('/').pop()||'index.html'},timestamp:Date.now(),hypothesisId:'A-B-C-E'})}).catch(()=>{});
    // #endregion
  }

  document.addEventListener("DOMContentLoaded", function () {
    logNavDebug('DOMContentLoaded');
    const { sidebar, overlay } = getSidebarElements();

    if (overlay) {
      overlay.addEventListener("click", closeSidebar);
    }

    if (sidebar) {
      sidebar.addEventListener("click", function (event) {
        const menuTarget = event.target.closest("a, button");
        if (menuTarget) {
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
      logNavDebug('resize');
    });
  });
})();
