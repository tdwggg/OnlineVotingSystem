(function () {
  const activeClass = 'active';
  const menu = document.querySelector('.sidebar ul');
  if (!menu) return;

  const sectionEls = document.querySelectorAll('[data-section-panel]');
  const sectionByName = new Map(
    Array.from(sectionEls).map((el) => [el.getAttribute('data-section-panel'), el])
  );

  function setActive(name) {
    document.querySelectorAll('.sidebar li').forEach((li) => li.classList.remove(activeClass));
    const li = document.querySelector(`.sidebar li a[data-section="${name}"]`)?.closest('li');
    if (li) li.classList.add(activeClass);

    sectionByName.forEach((el, key) => {
      el.style.display = key === name ? 'block' : 'none';
    });
  }

  // Default
  if (!location.hash) {
    setActive('home');
  }

  menu.addEventListener('click', (e) => {
    const a = e.target.closest('a[data-section]');
    if (!a) return;

    e.preventDefault();
    const name = a.getAttribute('data-section');
    location.hash = name;
    setActive(name);
  });

  window.addEventListener('hashchange', () => {
    const name = location.hash.replace('#', '') || 'home';
    setActive(name);
  });
})();

