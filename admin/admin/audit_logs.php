<?php
require_once dirname(__FILE__) . '/../helpers/functions.php';

require_admin();

date_default_timezone_set('Asia/Manila');

$page_title = 'Audit Logs';
$page_subtitle = 'Track admin activities, security actions, and important system changes.';

$pdo = db();

function audit_post_value($key)
{
    return isset($_GET[$key]) ? trim((string) $_GET[$key]) : '';
}

function audit_display_datetime($value)
{
    if ($value == '' || $value == null || $value == '0000-00-00 00:00:00') {
        return '-';
    }

    $time = strtotime($value);

    if ($time === false) {
        return '-';
    }

    return date('M d, Y h:i A', $time);
}

function audit_action_badge($action)
{
    $action = strtolower((string) $action);

    if (strpos($action, 'login') !== false || strpos($action, 'logged') !== false) {
        return 'text-bg-primary';
    }

    if (strpos($action, 'add') !== false || strpos($action, 'create') !== false || strpos($action, 'open') !== false) {
        return 'text-bg-success';
    }

    if (strpos($action, 'update') !== false || strpos($action, 'edit') !== false || strpos($action, 'change') !== false || strpos($action, 'schedule') !== false) {
        return 'text-bg-warning';
    }

    if (strpos($action, 'delete') !== false || strpos($action, 'close') !== false || strpos($action, 'logout') !== false) {
        return 'text-bg-danger';
    }

    return 'text-bg-secondary';
}

function audit_action_icon($action)
{
    $action = strtolower((string) $action);

    if (strpos($action, 'login') !== false || strpos($action, 'logged') !== false) {
        return 'bi-shield-check';
    }

    if (strpos($action, 'voter') !== false) {
        return 'bi-person-check';
    }

    if (strpos($action, 'candidate') !== false) {
        return 'bi-person-badge';
    }

    if (strpos($action, 'election') !== false || strpos($action, 'schedule') !== false || strpos($action, 'voting') !== false) {
        return 'bi-calendar-check';
    }

    if (strpos($action, 'delete') !== false) {
        return 'bi-trash';
    }

    if (strpos($action, 'close') !== false || strpos($action, 'logout') !== false) {
        return 'bi-lock';
    }

    return 'bi-clock-history';
}

$search = audit_post_value('search');
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page = 12;

$where = array();
$params = array();

if ($search != '') {
    $where[] = "(admin_name LIKE :search OR action LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

$where_sql = '';

if (count($where) > 0) {
    $where_sql = 'WHERE ' . implode(' AND ', $where);
}

$total_rows = 0;
$total_today = 0;
$total_logins = 0;
$total_system_changes = 0;
$audit_logs = array();

try {
    $count_sql = "
        SELECT COUNT(*)
        FROM audit_logs
        $where_sql
    ";

    $count_stmt = $pdo->prepare($count_sql);

    foreach ($params as $key => $value) {
        $count_stmt->bindValue($key, $value);
    }

    $count_stmt->execute();
    $total_rows = (int) $count_stmt->fetchColumn();
} catch (Exception $e) {
    $total_rows = 0;
}

try {
    $total_today = (int) $pdo->query("
        SELECT COUNT(*)
        FROM audit_logs
        WHERE DATE(`timestamp`) = CURDATE()
    ")->fetchColumn();
} catch (Exception $e) {
    $total_today = 0;
}

try {
    $total_logins = (int) $pdo->query("
        SELECT COUNT(*)
        FROM audit_logs
        WHERE action LIKE '%login%' OR action LIKE '%logged%'
    ")->fetchColumn();
} catch (Exception $e) {
    $total_logins = 0;
}

try {
    $total_system_changes = (int) $pdo->query("
        SELECT COUNT(*)
        FROM audit_logs
        WHERE
            action LIKE '%add%'
            OR action LIKE '%create%'
            OR action LIKE '%update%'
            OR action LIKE '%edit%'
            OR action LIKE '%delete%'
            OR action LIKE '%open%'
            OR action LIKE '%close%'
            OR action LIKE '%schedule%'
    ")->fetchColumn();
} catch (Exception $e) {
    $total_system_changes = 0;
}

$pagination = paginate($total_rows, $page, $per_page);
$page = $pagination[0];
$total_pages = $pagination[1];
$offset = $pagination[2];

try {
    $sql = "
        SELECT
            log_id,
            admin_name,
            action,
            `timestamp` AS log_timestamp
        FROM audit_logs
        $where_sql
        ORDER BY `timestamp` DESC, log_id DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->bindValue(':limit', (int) $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
    $stmt->execute();

    $audit_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $audit_logs = array();
}

$flashes = consume_flash();

require_once dirname(__FILE__) . '/../includes/header.php';
require_once dirname(__FILE__) . '/../includes/sidebar.php';
?>

<div class="ivote-management-page ivote-audit-page">

    <?php if (count($flashes) > 0) { ?>
        <div class="ivote-flash-wrap">
            <?php foreach ($flashes as $message) { ?>
                <div class="alert alert-<?php echo e($message['type']); ?> alert-dismissible fade show" role="alert">
                    <?php echo e($message['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

    <div class="ivote-results-stat-grid">
        <div class="ivote-card ivote-result-stat">
            <span>Total Logs</span>
            <strong><?php echo number_format($total_rows); ?></strong>
            <small>Recorded admin activities</small>
        </div>

        <div class="ivote-card ivote-result-stat">
            <span>Today</span>
            <strong><?php echo number_format($total_today); ?></strong>
            <small>Logs recorded today</small>
        </div>

        <div class="ivote-card ivote-result-stat">
            <span>Login Records</span>
            <strong><?php echo number_format($total_logins); ?></strong>
            <small>Admin login/logout activities</small>
        </div>

        <div class="ivote-card ivote-result-stat">
            <span>System Changes</span>
            <strong><?php echo number_format($total_system_changes); ?></strong>
            <small>Voter, candidate, and election actions</small>
        </div>
    </div>

    <div class="ivote-filter-card">
        <form method="GET" action="audit_logs.php" class="ivote-audit-filter-form">
            <div>
                <label class="form-label">Search Logs</label>
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    value="<?php echo e($search); ?>"
                    placeholder="Search admin name or action"
                >
            </div>

            <button type="submit" class="btn btn-ivote-outline">
                <i class="bi bi-search me-1"></i>
                Search
            </button>

            <a href="audit_logs.php" class="btn btn-light border">
                Reset
            </a>
        </form>
    </div>

    <div class="ivote-card ivote-data-card">
        <div class="ivote-card-header">
            <h3 class="ivote-section-title">
                <i class="bi bi-clock-history text-primary me-1"></i>
                Admin Activity History
            </h3>

            <span class="ivote-record-count">
                <?php echo number_format($total_rows); ?> record(s)
            </span>
        </div>

        <div class="table-responsive">
            <table class="table ivote-management-table">
                <thead>
                    <tr>
                        <th>Log ID</th>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Type</th>
                        <th>Date and Time</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($audit_logs) > 0) { ?>
                        <?php foreach ($audit_logs as $log) { ?>
                            <tr>
                                <td>
                                    <strong class="text-primary">
                                        #<?php echo e($log['log_id']); ?>
                                    </strong>
                                </td>

                                <td>
                                    <div class="fw-bold">
                                        <?php echo e($log['admin_name']); ?>
                                    </div>
                                    <small class="text-muted">System administrator</small>
                                </td>

                                <td>
                                    <div class="ivote-audit-action">
                                        <i class="bi <?php echo e(audit_action_icon($log['action'])); ?>"></i>
                                        <span><?php echo e($log['action']); ?></span>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge <?php echo audit_action_badge($log['action']); ?>">
                                        Activity
                                    </span>
                                </td>

                                <td>
                                    <?php echo e(audit_display_datetime($log['log_timestamp'])); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                No audit logs found.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="ivote-pagination-wrap">
            <div class="text-muted small">
                Page <?php echo number_format($page); ?> of <?php echo number_format($total_pages); ?>
            </div>

            <nav>
                <ul class="pagination mb-0">
                    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="audit_logs.php?search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </div>

</div>

<?php
require_once dirname(__FILE__) . '/../includes/footer.php';
?>