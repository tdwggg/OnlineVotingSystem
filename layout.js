(function () {
  function getSidebarElements() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var toggleBtn = document.querySelector('.menuButton, .sidebarToggle');
    return { sidebar: sidebar, overlay: overlay, toggleBtn: toggleBtn };
  }

  function setSidebarOpen(open) {
    var elements = getSidebarElements();
    if (!elements.sidebar) return;
    if (open) {
      elements.sidebar.classList.add('open');
      document.body.classList.add('sidebar-open');
    } else {
      elements.sidebar.classList.remove('open');
      document.body.classList.remove('sidebar-open');
    }
    if (elements.overlay) {
      elements.overlay.classList.toggle('show', open);
      elements.overlay.hidden = !open;
      elements.overlay.setAttribute('aria-hidden', open ? 'false' : 'true');
    }
    if (elements.toggleBtn) {
      elements.toggleBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
  }

  window.toggleSidebar = function () {
    var elements = getSidebarElements();
    if (!elements.sidebar) return;
    setSidebarOpen(!elements.sidebar.classList.contains('open'));
  };

  window.closeSidebar = function () {
    setSidebarOpen(false);
  };

  window.toggleFaq = function (element) {
    var answer = element.nextElementSibling;
    var isActive = element.classList.contains('active');
    var questions = document.querySelectorAll('.faqQuestion');
    for (var i = 0; i < questions.length; i++) {
      questions[i].classList.remove('active');
      if (questions[i].nextElementSibling) {
        questions[i].nextElementSibling.classList.remove('active');
      }
    }
    if (!isActive) {
      element.classList.add('active');
      if (answer) answer.classList.add('active');
    }
  };

  document.addEventListener('DOMContentLoaded', function () {
    var elements = getSidebarElements();
    if (elements.overlay) {
      elements.overlay.addEventListener('click', function () {
        setSidebarOpen(false);
      });
    }
    if (elements.sidebar) {
      elements.sidebar.addEventListener('click', function (event) {
        var target = event.target.closest('a, button');
        if (target && window.innerWidth < 992) setSidebarOpen(false);
      });
    }
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') setSidebarOpen(false);
    });
  });
})();

(function () {
  function addMaterialRipple(event) {
    var target = event.target.closest('.btn, button, .userNavList a, .sidebarMenuNav a, .quickAction, .positionCard, .candidateCard, .userChip, .menuButton, .sidebarClose');
    if (!target) return;
    var rect = target.getBoundingClientRect();
    var size = Math.max(rect.width, rect.height);
    var ripple = document.createElement('span');
    ripple.className = 'materialRipple';
    ripple.style.width = size + 'px';
    ripple.style.height = size + 'px';
    ripple.style.left = (event.clientX - rect.left - size / 2) + 'px';
    ripple.style.top = (event.clientY - rect.top - size / 2) + 'px';
    target.appendChild(ripple);
    window.setTimeout(function () {
      if (ripple && ripple.parentNode) ripple.parentNode.removeChild(ripple);
    }, 650);
  }

  function initMaterialReveal() {
    var targets = document.querySelectorAll('.userCard, .statCard, .candidateCard, .positionCard, .quickAction, .helpCard, .overviewCard, .featureCard, .securityCard, .faqCard, .termsContent, .policyContent');
    for (var i = 0; i < targets.length; i++) {
      targets[i].classList.add('materialReveal');
    }

    if (!('IntersectionObserver' in window)) {
      for (var j = 0; j < targets.length; j++) targets[j].classList.add('isVisible');
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      for (var k = 0; k < entries.length; k++) {
        if (entries[k].isIntersecting) {
          entries[k].target.classList.add('isVisible');
          observer.unobserve(entries[k].target);
        }
      }
    }, { threshold: 0.12 });

    for (var m = 0; m < targets.length; m++) observer.observe(targets[m]);
  }

  document.addEventListener('pointerdown', addMaterialRipple);
  document.addEventListener('DOMContentLoaded', initMaterialReveal);
})();
