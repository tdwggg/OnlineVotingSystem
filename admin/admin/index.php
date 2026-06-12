<?php
require_once dirname(__FILE__) . '/../helpers/functions.php';

require_admin();

$page_title = 'Admin Dashboard';
$page_subtitle = 'Monitor voters, ballots, election schedule, and results.';

$pdo = db();

$total_voters = 0;
$total_verified_voters = 0;
$total_accounts = 0;
$total_candidates = 0;
$total_votes = 0;
$total_ballots = 0;
$total_elections = 0;
$total_open_elections = 0;
$turnout_rate = 0;

try {
    $total_voters = (int) $pdo->query("SELECT COUNT(*) FROM registered_voters")->fetchColumn();
} catch (Exception $e) {
    $total_voters = 0;
}

try {
    $total_verified_voters = (int) $pdo->query("SELECT COUNT(*) FROM registered_voters WHERE profile_status = 'Complete'")->fetchColumn();
} catch (Exception $e) {
    $total_verified_voters = 0;
}

try {
    $total_accounts = (int) $pdo->query("SELECT COUNT(*) FROM accounts")->fetchColumn();
} catch (Exception $e) {
    $total_accounts = 0;
}

try {
    $total_candidates = (int) $pdo->query("SELECT COUNT(*) FROM candidates")->fetchColumn();
} catch (Exception $e) {
    $total_candidates = 0;
}

try {
    $total_votes = (int) $pdo->query("SELECT COUNT(*) FROM votes")->fetchColumn();
} catch (Exception $e) {
    $total_votes = 0;
}

try {
    $total_ballots = (int) $pdo->query("SELECT COUNT(*) FROM ballots")->fetchColumn();
} catch (Exception $e) {
    $total_ballots = 0;
}

try {
    $total_elections = (int) $pdo->query("SELECT COUNT(*) FROM elections")->fetchColumn();
} catch (Exception $e) {
    $total_elections = 0;
}

try {
    $total_open_elections = (int) $pdo->query("SELECT COUNT(*) FROM elections WHERE election_status = 'Open'")->fetchColumn();
} catch (Exception $e) {
    $total_open_elections = 0;
}

if ($total_verified_voters > 0) {
    $turnout_rate = round(($total_ballots / $total_verified_voters) * 100, 1);
}

$verified_percent = 0;

if ($total_voters > 0) {
    $verified_percent = round(($total_verified_voters / $total_voters) * 100, 1);
}

$pending_voters = array();

try {
    $stmt = $pdo->query("
        SELECT voter_id, first_name, last_name, email, profile_status, registration_status, created_at
        FROM registered_voters
        WHERE registration_status = 'Unregistered' OR profile_status = 'Incomplete'
        ORDER BY created_at DESC
        LIMIT 6
    ");

    $pending_voters = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $pending_voters = array();
}

$elections = array();

try {
    $stmt = $pdo->query("
        SELECT election_name, start_datetime, end_datetime, election_status
        FROM elections
        ORDER BY start_datetime DESC
        LIMIT 4
    ");

    $elections = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $elections = array();
}

$chart_position_labels = array();
$chart_position_votes = array();

try {
    $stmt = $pdo->query("
        SELECT p.position_name, COUNT(v.vote_id) AS vote_total
        FROM positions p
        LEFT JOIN votes v ON p.position_id = v.position_id
        GROUP BY p.position_id, p.position_name
        ORDER BY p.position_id ASC
    ");

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $chart_position_labels[] = $row['position_name'];
        $chart_position_votes[] = (int) $row['vote_total'];
    }
} catch (Exception $e) {
    $chart_position_labels = array();
    $chart_position_votes = array();
}

if (count($chart_position_labels) == 0) {
    $chart_position_labels = array('President', 'Vice President', 'Senator', 'Governor', 'Mayor');
    $chart_position_votes = array(0, 0, 0, 0, 0);
}

$chart_status_labels = array('Complete Profiles', 'Incomplete Profiles', 'Accounts');
$chart_status_values = array(
    $total_verified_voters,
    max(0, $total_voters - $total_verified_voters),
    $total_accounts
);

require_once dirname(__FILE__) . '/../includes/header.php';
require_once dirname(__FILE__) . '/../includes/sidebar.php';
?>

<div class="ivote-dashboard-wrapper">

    <div class="ivote-stats-grid ivote-stats-grid-dashboard-four">
        <div class="ivote-card ivote-stat-card">
            <div class="ivote-stat-icon">
                <i class="bi bi-people"></i>
            </div>

            <p class="ivote-stat-title">Total Voter IDs</p>
            <h2 class="ivote-stat-value"><?php echo number_format($total_voters); ?></h2>
            <p class="ivote-stat-caption">COMELEC-style master list</p>
        </div>

        <div class="ivote-card ivote-stat-card">
            <div class="ivote-stat-icon green">
                <i class="bi bi-shield-check"></i>
            </div>

            <p class="ivote-stat-title">Complete Profiles</p>
            <h2 class="ivote-stat-value"><?php echo number_format($total_verified_voters); ?></h2>
            <p class="ivote-stat-caption"><?php echo e($verified_percent); ?>% of voter IDs</p>
        </div>

        <div class="ivote-card ivote-stat-card">
            <div class="ivote-stat-icon">
                <i class="bi bi-person-check"></i>
            </div>

            <p class="ivote-stat-title">Accounts</p>
            <h2 class="ivote-stat-value"><?php echo number_format($total_accounts); ?></h2>
            <p class="ivote-stat-caption">Created voter accounts</p>
        </div>

        <div class="ivote-card ivote-stat-card">
            <div class="ivote-stat-icon yellow">
                <i class="bi bi-check2-square"></i>
            </div>

            <p class="ivote-stat-title">Votes Cast</p>
            <h2 class="ivote-stat-value"><?php echo number_format($total_votes); ?></h2>
            <p class="ivote-stat-caption"><?php echo e($turnout_rate); ?>% turnout</p>
        </div>
    </div>

    <div class="ivote-dashboard-grid-main">
        <div class="ivote-card ivote-dashboard-panel">
            <div class="ivote-card-header">
                <h3 class="ivote-section-title">
                    <i class="bi bi-person-lines-fill text-primary me-1"></i>
                    Pending Verifications
                    <span class="badge text-bg-secondary"><?php echo count($pending_voters); ?></span>
                </h3>

                <a href="voters.php" class="btn btn-sm btn-ivote-outline">
                    View Voters
                </a>
            </div>

            <div class="table-responsive">
                <table class="table ivote-clean-table">
                    <thead>
                        <tr>
                            <th>Voter ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($pending_voters) > 0) { ?>
                            <?php foreach ($pending_voters as $voter) { ?>
                                <tr>
                                    <td class="fw-bold"><?php echo e($voter['voter_id']); ?></td>

                                    <td>
                                        <?php
                                            $name = trim($voter['first_name'] . ' ' . $voter['last_name']);

                                            if ($name == '') {
                                                $name = 'Incomplete profile';
                                            }

                                            echo e($name);
                                        ?>
                                    </td>

                                    <td><?php echo e($voter['email']); ?></td>

                                    <td>
                                        <span class="badge <?php echo badge_class($voter['registration_status']); ?>">
                                            <?php echo e($voter['registration_status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    No pending voters.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="ivote-card ivote-dashboard-panel">
            <div class="ivote-card-header">
                <h3 class="ivote-section-title">
                    <i class="bi bi-lightning-charge text-primary me-1"></i>
                    Quick Actions
                </h3>
            </div>

            <div class="ivote-quick-grid">
                <a href="candidates.php" class="ivote-quick-action">
                    <i class="bi bi-person-plus"></i>
                    Add Candidate
                </a>

                <a href="voters.php" class="ivote-quick-action">
                    <i class="bi bi-people-fill"></i>
                    Manage Voters
                </a>

                <a href="elections.php" class="ivote-quick-action">
                    <i class="bi bi-calendar-plus"></i>
                    Manage Election Schedule
                </a>

                <a href="results.php" class="ivote-quick-action">
                    <i class="bi bi-bar-chart-fill"></i>
                    View Results
                </a>
            </div>

            <a href="elections.php" class="btn btn-ivote w-100 mt-3">
                <i class="bi bi-clock-history me-1"></i>
                Control Voting Schedule
            </a>
        </div>
    </div>

    <div class="ivote-dashboard-grid-bottom">
        <div class="ivote-card ivote-dashboard-panel">
            <div class="ivote-card-header">
                <h3 class="ivote-section-title">
                    <i class="bi bi-calendar-event text-primary me-1"></i>
                    Election Overview
                </h3>

                <a href="elections.php" class="btn btn-sm btn-ivote-outline">
                    View Schedule
                </a>
            </div>

            <div class="ivote-election-chart-layout">
                <div class="ivote-election-list">
                    <?php if (count($elections) > 0) { ?>
                        <?php foreach ($elections as $election) { ?>
                            <div class="ivote-election-item">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="ivote-election-name">
                                            <?php echo e($election['election_name']); ?>
                                        </div>

                                        <div class="ivote-election-date">
                                            <?php echo e(date('M d, Y h:i A', strtotime($election['start_datetime']))); ?>
                                            -
                                            <?php echo e(date('M d, Y h:i A', strtotime($election['end_datetime']))); ?>
                                        </div>
                                    </div>

                                    <span class="badge <?php echo badge_class($election['election_status']); ?>">
                                        <?php echo e($election['election_status']); ?>
                                    </span>
                                </div>

                                <div class="ivote-progress">
                                    <div class="ivote-progress-bar" style="width: <?php echo ($election['election_status'] == 'Open') ? '65' : (($election['election_status'] == 'Closed') ? '100' : '20'); ?>%;"></div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="ivote-election-item">
                            <div class="ivote-election-name">No election schedule yet</div>
                            <div class="ivote-election-date">Create or update the official voting schedule.</div>
                        </div>
                    <?php } ?>
                </div>

                <div class="ivote-chart-box">
                    <canvas id="votesByPositionChart"></canvas>
                </div>
            </div>
        </div>

        <div class="ivote-card ivote-dashboard-panel">
            <div class="ivote-card-header">
                <h3 class="ivote-section-title">
                    <i class="bi bi-pie-chart text-primary me-1"></i>
                    Voter Status
                </h3>
            </div>

            <div class="ivote-mini-chart-box">
                <canvas id="voterStatusChart"></canvas>
            </div>

            <div class="ivote-status-summary">
                <div>
                    <span class="ivote-status-dot green"></span>
                    Complete
                </div>

                <div>
                    <span class="ivote-status-dot yellow"></span>
                    Incomplete
                </div>

                <div>
                    <span class="ivote-status-dot blue"></span>
                    Accounts
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function initDashboardCharts() {
    var votesCanvas = document.getElementById('votesByPositionChart');
    var statusCanvas = document.getElementById('voterStatusChart');

    if (votesCanvas) {
        new Chart(votesCanvas, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chart_position_labels); ?>,
                datasets: [{
                    label: 'Votes',
                    data: <?php echo json_encode($chart_position_votes); ?>,
                    backgroundColor: '#0647b8',
                    hoverBackgroundColor: '#033587',
                    borderRadius: 12,
                    maxBarThickness: 48
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1400,
                    easing: 'easeOutQuart',
                    delay: function(context) {
                        if (context.type === 'data' && context.mode === 'default') {
                            return context.dataIndex * 180;
                        }

                        return 0;
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#101828',
                        padding: 12,
                        cornerRadius: 10
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: '#667085'
                        },
                        grid: {
                            color: '#eef2f7'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#667085'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    if (statusCanvas) {
        new Chart(statusCanvas, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($chart_status_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($chart_status_values); ?>,
                    backgroundColor: ['#16a34a', '#facc15', '#0647b8'],
                    hoverBackgroundColor: ['#15803d', '#eab308', '#033587'],
                    borderWidth: 0,
                    hoverOffset: 14
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                rotation: -90,
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1600,
                    easing: 'easeOutBounce'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#101828',
                        padding: 12,
                        cornerRadius: 10
                    }
                }
            }
        });
    }
}
</script>

<?php
require_once dirname(__FILE__) . '/../includes/footer.php';
?>