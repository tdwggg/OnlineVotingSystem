<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="ivote-sidebar" id="ivoteSidebar">
    <div class="ivote-sidebar-header">
        <a href="../admin/index.php" class="ivote-sidebar-logo-wrap">
            <img src="../assets/img/ivoteph-logo.png" alt="iVotePH Logo" class="ivote-sidebar-logo-img">
        </a>

        <button type="button" class="ivote-sidebar-close" onclick="closeSidebar()" aria-label="Close sidebar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <nav class="ivote-sidebar-nav">
        <a href="../admin/index.php" class="ivote-sidebar-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
            <i class="bi bi-house-door"></i>
            <span>Dashboard</span>
        </a>

        <a href="../admin/voters.php" class="ivote-sidebar-link <?php echo ($current_page == 'voters.php') ? 'active' : ''; ?>">
            <i class="bi bi-person-check"></i>
            <span>Voter Management</span>
        </a>

        <a href="../admin/candidates.php" class="ivote-sidebar-link <?php echo ($current_page == 'candidates.php') ? 'active' : ''; ?>">
            <i class="bi bi-person-badge"></i>
            <span>Candidate Management</span>
        </a>

        <a href="../admin/elections.php" class="ivote-sidebar-link <?php echo ($current_page == 'elections.php') ? 'active' : ''; ?>">
            <i class="bi bi-calendar-check"></i>
            <span>Elections</span>
        </a>

        <a href="../admin/results.php" class="ivote-sidebar-link <?php echo ($current_page == 'results.php') ? 'active' : ''; ?>">
            <i class="bi bi-bar-chart"></i>
            <span>Results</span>
        </a>

        <a href="../admin/audit_logs.php" class="ivote-sidebar-link <?php echo ($current_page == 'audit_logs.php') ? 'active' : ''; ?>">
            <i class="bi bi-clock-history"></i>
            <span>Audit Logs</span>
        </a>
    </nav>
</aside>

<div class="ivote-sidebar-overlay" id="ivoteSidebarOverlay" onclick="closeSidebar()"></div>

<main class="ivote-main">
    <div class="ivote-dashboard-top-card">
        <div class="ivote-dashboard-top-left">
            <button type="button" class="ivote-menu-clean-btn" onclick="openSidebar()" aria-label="Open sidebar">
                <i class="bi bi-list"></i>
            </button>

            <a href="../admin/index.php" class="ivote-topbar-logo">
                <img src="../assets/img/ivoteph-logo.png" alt="iVotePH Logo">
            </a>

            <div class="ivote-dashboard-title-block">
                <h1><?php echo e($page_title); ?></h1>
                <p><?php echo e($page_subtitle); ?></p>
            </div>
        </div>

        <div class="ivote-dashboard-top-right">
            <button type="button" class="ivote-logout-btn" data-bs-toggle="modal" data-bs-target="#logoutConfirmModal">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </button>
        </div>
    </div>

    <div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-labelledby="logoutConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ivote-logout-modal">
                <div class="modal-body">
                    <div class="ivote-logout-modal-icon">
                        <i class="bi bi-box-arrow-right"></i>
                    </div>

                    <h5 id="logoutConfirmModalLabel">Confirm Logout</h5>

                    <p>
                        Are you sure you want to logout from the iVotePH admin panel?
                    </p>

                    <div class="ivote-logout-modal-actions">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <a href="../auth/logout.php" class="btn btn-danger">
                            Yes, Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>