</main>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function openSidebar() {
    var sidebar = document.getElementById('ivoteSidebar');
    var overlay = document.getElementById('ivoteSidebarOverlay');

    if (sidebar) {
        sidebar.className = sidebar.className.replace(' show', '');
        sidebar.className += ' show';
    }

    if (overlay) {
        overlay.className = overlay.className.replace(' show', '');
        overlay.className += ' show';
    }
}

function closeSidebar() {
    var sidebar = document.getElementById('ivoteSidebar');
    var overlay = document.getElementById('ivoteSidebarOverlay');

    if (sidebar) {
        sidebar.className = sidebar.className.replace(' show', '');
    }

    if (overlay) {
        overlay.className = overlay.className.replace(' show', '');
    }
}

function toggleSidebar() {
    var sidebar = document.getElementById('ivoteSidebar');

    if (sidebar && sidebar.className.indexOf('show') !== -1) {
        closeSidebar();
    } else {
        openSidebar();
    }
}

document.onkeydown = function(event) {
    event = event || window.event;

    if (event.keyCode == 27) {
        closeSidebar();
    }
};

if (typeof initDashboardCharts === 'function') {
    initDashboardCharts();
}
</script>

</body>
</html>