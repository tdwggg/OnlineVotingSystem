// Section Navigation Handler
document.addEventListener('DOMContentLoaded', function() {
  const sidebarLinks = document.querySelectorAll('.sidebar a[data-section]');
  const topbarButtons = document.querySelectorAll('.topbar-icon-btn[data-section]');
  const allSectionPanels = document.querySelectorAll('[data-section-panel]');
  const sidebarItems = document.querySelectorAll('.sidebar ul li');

  // Handle sidebar navigation
  sidebarLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const section = this.getAttribute('data-section');
      showSection(section);
      updateActiveState(this);
      if (window.innerWidth < 992 && typeof window.closeSidebar === 'function') {
        window.closeSidebar();
      }
    });
  });

  // Handle topbar icon button navigation
  topbarButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const section = this.getAttribute('data-section');
      showSection(section);
      
      // Update active state in sidebar
      sidebarLinks.forEach(link => {
        if (link.getAttribute('data-section') === section) {
          updateActiveState(link);
        }
      });
    });
  });

  // Function to show a specific section
  function showSection(sectionName) {
    // Hide all sections
    allSectionPanels.forEach(panel => {
      panel.style.display = 'none';
    });

    // Show the selected section
    const selectedPanel = document.querySelector(`[data-section-panel="${sectionName}"]`);
    if (selectedPanel) {
      selectedPanel.style.display = 'block';
    }
  }

  // Function to update active state
  function updateActiveState(clickedLink) {
    // Remove active class from all sidebar items
    sidebarItems.forEach(item => {
      item.classList.remove('active');
    });

    // Add active class to the parent of clicked link
    clickedLink.closest('li').classList.add('active');
  }

  // Initialize: show home section by default
  showSection('home');
});

// Redirect to login if not logged in (check session storage)
window.addEventListener('load', function() {
  const isLoggedIn = sessionStorage.getItem('isLoggedIn');
  if (!isLoggedIn) {
    window.location.href = 'login.html';
  }
});
