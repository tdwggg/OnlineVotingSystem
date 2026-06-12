document.addEventListener('DOMContentLoaded', function () {
  var sectionLinks = document.querySelectorAll('[data-section]');
  var panels = document.querySelectorAll('[data-section-panel]');

  function showSection(sectionName) {
    for (var i = 0; i < panels.length; i++) {
      panels[i].style.display = panels[i].getAttribute('data-section-panel') === sectionName ? 'block' : 'none';
    }
    for (var j = 0; j < sectionLinks.length; j++) {
      var isActive = sectionLinks[j].getAttribute('data-section') === sectionName;
      sectionLinks[j].classList.toggle('active', isActive);
      var parent = sectionLinks[j].closest('li');
      if (parent) parent.classList.toggle('active', isActive);
    }
  }

  for (var i = 0; i < sectionLinks.length; i++) {
    sectionLinks[i].addEventListener('click', function (event) {
      event.preventDefault();
      showSection(this.getAttribute('data-section'));
      if (typeof window.closeSidebar === 'function') window.closeSidebar();
    });
  }

  if (panels.length > 0) showSection('home');
});
