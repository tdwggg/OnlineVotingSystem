<?php
require_once dirname(__FILE__) . '/../helpers/functions.php';

require_admin();

date_default_timezone_set('Asia/Manila');

$page_title = 'Election Results';
$page_subtitle = 'View real-time voting results, turnout, candidate rankings, and vote distribution charts.';

$pdo = db();

function results_quote_col($column)
{
    return '`' . str_replace('`', '', $column) . '`';
}

function results_table_columns($pdo, $table)
{
    $columns = array();

    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM " . $table);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $columns[$row['Field']] = $row;
        }
    } catch (Exception $e) {
        $columns = array();
    }

    return $columns;
}

function results_pick_column($columns, $choices)
{
    foreach ($choices as $choice) {
        if (isset($columns[$choice])) {
            return $choice;
        }
    }

    return '';
}

function results_display_datetime($value)
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

function results_percent($part, $whole)
{
    if ((int) $whole <= 0) {
        return 0;
    }

    return round(((int) $part / (int) $whole) * 100, 1);
}

function results_status_clean($status)
{
    $status = strtolower(trim((string) $status));

    if ($status == 'open') {
        return 'Open';
    }

    if ($status == 'closed') {
        return 'Closed';
    }

    return 'Draft';
}

$election_columns = results_table_columns($pdo, 'elections');
$title_col = results_pick_column($election_columns, array('election_name', 'election_title', 'title'));
$start_col = results_pick_column($election_columns, array('start_datetime', 'start_date', 'starts_at'));
$end_col = results_pick_column($election_columns, array('end_datetime', 'end_date', 'ends_at'));
$status_col = results_pick_column($election_columns, array('election_status', 'status'));

$election = false;

try {
    if ($title_col != '') {
        $select_parts = array();
        $select_parts[] = "election_id";
        $select_parts[] = results_quote_col($title_col) . " AS election_title";

        if ($start_col != '') {
            $select_parts[] = results_quote_col($start_col) . " AS start_datetime";
        } else {
            $select_parts[] = "NULL AS start_datetime";
        }

        if ($end_col != '') {
            $select_parts[] = results_quote_col($end_col) . " AS end_datetime";
        } else {
            $select_parts[] = "NULL AS end_datetime";
        }

        if ($status_col != '') {
            $select_parts[] = results_quote_col($status_col) . " AS election_status";
        } else {
            $select_parts[] = "'Draft' AS election_status";
        }

        $stmt = $pdo->query("
            SELECT " . implode(', ', $select_parts) . "
            FROM elections
            ORDER BY election_id ASC
            LIMIT 1
        ");

        $election = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    $election = false;
}

$election_id = 0;

if ($election && isset($election['election_id'])) {
    $election_id = (int) $election['election_id'];
}

$total_voters = 0;
$complete_voters = 0;
$total_ballots = 0;
$total_votes = 0;
$total_candidates = 0;
$total_positions = 0;
$turnout_rate = 0;

try {
    $total_voters = (int) $pdo->query("SELECT COUNT(*) FROM registered_voters")->fetchColumn();
} catch (Exception $e) {
    $total_voters = 0;
}

try {
    $complete_voters = (int) $pdo->query("SELECT COUNT(*) FROM registered_voters WHERE profile_status = 'Complete'")->fetchColumn();
} catch (Exception $e) {
    $complete_voters = 0;
}

try {
    if ($election_id > 0) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM ballots WHERE election_id = :election_id");
        $stmt->execute(array(':election_id' => $election_id));
        $total_ballots = (int) $stmt->fetchColumn();
    } else {
        $total_ballots = (int) $pdo->query("SELECT COUNT(*) FROM ballots")->fetchColumn();
    }
} catch (Exception $e) {
    $total_ballots = 0;
}

try {
    if ($election_id > 0) {
        $stmt = $pdo->prepare("
            SELECT COUNT(v.vote_id)
            FROM votes v
            INNER JOIN ballots b ON v.ballot_id = b.ballot_id
            WHERE b.election_id = :election_id
        ");
        $stmt->execute(array(':election_id' => $election_id));
        $total_votes = (int) $stmt->fetchColumn();
    } else {
        $total_votes = (int) $pdo->query("SELECT COUNT(*) FROM votes")->fetchColumn();
    }
} catch (Exception $e) {
    $total_votes = 0;
}

try {
    $total_candidates = (int) $pdo->query("SELECT COUNT(*) FROM candidates")->fetchColumn();
} catch (Exception $e) {
    $total_candidates = 0;
}

try {
    $total_positions = (int) $pdo->query("SELECT COUNT(*) FROM positions")->fetchColumn();
} catch (Exception $e) {
    $total_positions = 0;
}

if ($complete_voters > 0) {
    $turnout_rate = results_percent($total_ballots, $complete_voters);
}

$position_results = array();
$candidate_rows = array();

try {
    $where_election = '';
    $params = array();

    if ($election_id > 0) {
        $where_election = "WHERE b.election_id = :election_id";
        $params[':election_id'] = $election_id;
    }

    $sql = "
        SELECT
            p.position_id,
            p.position_name,
            p.max_votes,
            c.candidate_id,
            c.full_name,
            c.political_party,
            COUNT(v.vote_id) AS vote_total
        FROM candidates c
        LEFT JOIN positions p ON c.position_id = p.position_id
        LEFT JOIN votes v ON c.candidate_id = v.candidate_id
        LEFT JOIN ballots b ON v.ballot_id = b.ballot_id
        $where_election
        GROUP BY
            p.position_id,
            p.position_name,
            p.max_votes,
            c.candidate_id,
            c.full_name,
            c.political_party
        ORDER BY
            p.position_id ASC,
            vote_total DESC,
            c.full_name ASC
    ";

    $stmt = $pdo->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->execute();
    $candidate_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($candidate_rows as $row) {
        $position_id = isset($row['position_id']) ? (int) $row['position_id'] : 0;
        $position_name = $row['position_name'] ? $row['position_name'] : 'Unassigned Position';

        if (!isset($position_results[$position_id])) {
            $position_results[$position_id] = array(
                'position_id' => $position_id,
                'position_name' => $position_name,
                'max_votes' => isset($row['max_votes']) ? $row['max_votes'] : 1,
                'total_position_votes' => 0,
                'candidates' => array()
            );
        }

        $vote_total = (int) $row['vote_total'];

        $position_results[$position_id]['total_position_votes'] += $vote_total;
        $position_results[$position_id]['candidates'][] = array(
            'candidate_id' => $row['candidate_id'],
            'full_name' => $row['full_name'],
            'political_party' => $row['political_party'] ? $row['political_party'] : 'Independent',
            'vote_total' => $vote_total
        );
    }
} catch (Exception $e) {
    $position_results = array();
    $candidate_rows = array();
}

$latest_ballots = array();

try {
    if ($election_id > 0) {
        $stmt = $pdo->prepare("
            SELECT
                b.ballot_id,
                b.voter_id,
                b.submitted_at,
                rv.first_name,
                rv.last_name
            FROM ballots b
            LEFT JOIN registered_voters rv ON b.voter_id = rv.voter_id
            WHERE b.election_id = :election_id
            ORDER BY b.submitted_at DESC
            LIMIT 8
        ");
        $stmt->execute(array(':election_id' => $election_id));
    } else {
        $stmt = $pdo->query("
            SELECT
                b.ballot_id,
                b.voter_id,
                b.submitted_at,
                rv.first_name,
                rv.last_name
            FROM ballots b
            LEFT JOIN registered_voters rv ON b.voter_id = rv.voter_id
            ORDER BY b.submitted_at DESC
            LIMIT 8
        ");
    }

    $latest_ballots = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $latest_ballots = array();
}

$chart_position_labels = array();
$chart_position_votes = array();

foreach ($position_results as $position) {
    $chart_position_labels[] = $position['position_name'];
    $chart_position_votes[] = (int) $position['total_position_votes'];
}

if (count($chart_position_labels) == 0) {
    $chart_position_labels = array('No votes yet');
    $chart_position_votes = array(0);
}

$chart_turnout_labels = array('Submitted Ballots', 'Remaining Eligible Voters');
$remaining_voters = max(0, $complete_voters - $total_ballots);
$chart_turnout_values = array($total_ballots, $remaining_voters);

$chart_candidate_labels = array();
$chart_candidate_votes = array();

foreach ($candidate_rows as $row) {
    $chart_candidate_labels[] = $row['full_name'];
    $chart_candidate_votes[] = (int) $row['vote_total'];
}

if (count($chart_candidate_labels) == 0) {
    $chart_candidate_labels = array('No candidates');
    $chart_candidate_votes = array(0);
}

$flashes = consume_flash();

require_once dirname(__FILE__) . '/../includes/header.php';
require_once dirname(__FILE__) . '/../includes/sidebar.php';
?>

<div class="ivote-management-page ivote-results-page">

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

    <div class="ivote-results-hero">
        <div>
            <div class="ivote-results-eyebrow">Official Results Dashboard</div>

            <h2>
                <?php
                    if ($election) {
                        echo e($election['election_title']);
                    } else {
                        echo 'Election Results';
                    }
                ?>
            </h2>

            <p>
                Results are computed directly from submitted ballots and recorded vote choices in the database.
            </p>
        </div>

        <div class="ivote-results-hero-meta">
            <?php if ($election) { ?>
                <span class="badge <?php echo badge_class(results_status_clean($election['election_status'])); ?>">
                    <?php echo e(results_status_clean($election['election_status'])); ?>
                </span>

                <small>
                    <?php echo e(results_display_datetime($election['start_datetime'])); ?>
                    -
                    <?php echo e(results_display_datetime($election['end_datetime'])); ?>
                </small>
            <?php } else { ?>
                <span class="badge text-bg-secondary">No election found</span>
            <?php } ?>
        </div>
    </div>

    <div class="ivote-results-stat-grid">
        <div class="ivote-card ivote-result-stat">
            <span>Total Ballots</span>
            <strong><?php echo number_format($total_ballots); ?></strong>
            <small>Submitted voter ballots</small>
        </div>

        <div class="ivote-card ivote-result-stat">
            <span>Total Votes</span>
            <strong><?php echo number_format($total_votes); ?></strong>
            <small>Candidate selections recorded</small>
        </div>

        <div class="ivote-card ivote-result-stat">
            <span>Turnout Rate</span>
            <strong><?php echo e($turnout_rate); ?>%</strong>
            <small><?php echo number_format($complete_voters); ?> eligible complete profiles</small>
        </div>

        <div class="ivote-card ivote-result-stat">
            <span>Candidates</span>
            <strong><?php echo number_format($total_candidates); ?></strong>
            <small><?php echo number_format($total_positions); ?> ballot categories</small>
        </div>
    </div>

    <div class="ivote-results-chart-grid">
        <div class="ivote-card ivote-results-chart-card large">
            <div class="ivote-card-header">
                <h3 class="ivote-section-title">
                    <i class="bi bi-bar-chart-fill text-primary me-1"></i>
                    Votes by Candidate
                </h3>
            </div>

            <div class="ivote-results-chart-box large">
                <canvas id="candidateVotesChart"></canvas>
            </div>
        </div>

        <div class="ivote-card ivote-results-chart-card">
            <div class="ivote-card-header">
                <h3 class="ivote-section-title">
                    <i class="bi bi-pie-chart-fill text-primary me-1"></i>
                    Voter Turnout
                </h3>
            </div>

            <div class="ivote-results-chart-box">
                <canvas id="turnoutChart"></canvas>
            </div>
        </div>
    </div>

    <div class="ivote-card ivote-results-chart-card">
        <div class="ivote-card-header">
            <h3 class="ivote-section-title">
                <i class="bi bi-columns-gap text-primary me-1"></i>
                Votes by Position
            </h3>
        </div>

        <div class="ivote-results-chart-box medium">
            <canvas id="positionVotesChart"></canvas>
        </div>
    </div>

    <div class="ivote-results-position-grid">
        <?php if (count($position_results) > 0) { ?>
            <?php foreach ($position_results as $position) { ?>
                <?php
                    $position_total = (int) $position['total_position_votes'];
                    $winner_votes = 0;

                    if (count($position['candidates']) > 0) {
                        $winner_votes = (int) $position['candidates'][0]['vote_total'];
                    }
                ?>

                <div class="ivote-card ivote-position-result-card">
                    <div class="ivote-position-result-header">
                        <div>
                            <span>Position</span>
                            <h3><?php echo e($position['position_name']); ?></h3>
                        </div>

                        <strong><?php echo number_format($position_total); ?> votes</strong>
                    </div>

                    <div class="ivote-position-candidate-list">
                        <?php foreach ($position['candidates'] as $candidate) { ?>
                            <?php
                                $candidate_votes = (int) $candidate['vote_total'];
                                $candidate_percent = results_percent($candidate_votes, $position_total);
                                $is_leading = ($position_total > 0 && $candidate_votes == $winner_votes);
                            ?>

                            <div class="ivote-position-candidate-row">
                                <div class="ivote-position-candidate-main">
                                    <div>
                                        <strong><?php echo e($candidate['full_name']); ?></strong>
                                        <small><?php echo e($candidate['political_party']); ?></small>
                                    </div>

                                    <div class="ivote-position-candidate-votes">
                                        <?php if ($is_leading) { ?>
                                            <span class="badge text-bg-success">Leading</span>
                                        <?php } ?>

                                        <strong><?php echo number_format($candidate_votes); ?></strong>
                                        <small><?php echo e($candidate_percent); ?>%</small>
                                    </div>
                                </div>

                                <div class="ivote-result-progress">
                                    <div style="width: <?php echo e($candidate_percent); ?>%;"></div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="ivote-card ivote-position-result-card">
                <div class="text-center text-muted py-4">
                    No result data available yet.
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="ivote-results-bottom-grid">
        <div class="ivote-card ivote-data-card">
            <div class="ivote-card-header">
                <h3 class="ivote-section-title">
                    <i class="bi bi-trophy-fill text-primary me-1"></i>
                    Candidate Ranking Table
                </h3>
            </div>

            <div class="table-responsive">
                <table class="table ivote-management-table">
                    <thead>
                        <tr>
                            <th>Position</th>
                            <th>Candidate</th>
                            <th>Political Party</th>
                            <th>Votes</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($candidate_rows) > 0) { ?>
                            <?php foreach ($candidate_rows as $row) { ?>
                                <tr>
                                    <td><?php echo e($row['position_name'] ? $row['position_name'] : 'Unassigned'); ?></td>
                                    <td class="fw-bold"><?php echo e($row['full_name']); ?></td>
                                    <td><?php echo e($row['political_party'] ? $row['political_party'] : 'Independent'); ?></td>
                                    <td>
                                        <span class="badge text-bg-primary">
                                            <?php echo number_format((int) $row['vote_total']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No candidate votes recorded yet.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="ivote-card ivote-data-card">
            <div class="ivote-card-header">
                <h3 class="ivote-section-title">
                    <i class="bi bi-clock-history text-primary me-1"></i>
                    Latest Submitted Ballots
                </h3>
            </div>

            <div class="table-responsive">
                <table class="table ivote-management-table">
                    <thead>
                        <tr>
                            <th>Ballot ID</th>
                            <th>Voter</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($latest_ballots) > 0) { ?>
                            <?php foreach ($latest_ballots as $ballot) { ?>
                                <tr>
                                    <td class="fw-bold text-primary"><?php echo e($ballot['ballot_id']); ?></td>
                                    <td>
                                        <?php
                                            $voter_name = trim($ballot['first_name'] . ' ' . $ballot['last_name']);

                                            if ($voter_name == '') {
                                                $voter_name = $ballot['voter_id'];
                                            }

                                            echo e($voter_name);
                                        ?>
                                    </td>
                                    <td><?php echo e(results_display_datetime($ballot['submitted_at'])); ?></td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    No submitted ballots yet.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
function initResultsCharts() {
    var candidateCanvas = document.getElementById('candidateVotesChart');
    var turnoutCanvas = document.getElementById('turnoutChart');
    var positionCanvas = document.getElementById('positionVotesChart');

    if (candidateCanvas) {
        new Chart(candidateCanvas, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chart_candidate_labels); ?>,
                datasets: [{
                    label: 'Votes',
                    data: <?php echo json_encode($chart_candidate_votes); ?>,
                    backgroundColor: '#0647b8',
                    hoverBackgroundColor: '#033587',
                    borderRadius: 12,
                    maxBarThickness: 44
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1400,
                    easing: 'easeOutQuart',
                    delay: function(context) {
                        if (context.type === 'data' && context.mode === 'default') {
                            return context.dataIndex * 120;
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
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: '#667085'
                        },
                        grid: {
                            color: '#eef2f7'
                        }
                    },
                    y: {
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

    if (turnoutCanvas) {
        new Chart(turnoutCanvas, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($chart_turnout_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($chart_turnout_values); ?>,
                    backgroundColor: ['#0647b8', '#d9e4f5'],
                    hoverBackgroundColor: ['#033587', '#c8d7ee'],
                    borderWidth: 0,
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1500,
                    easing: 'easeOutBounce'
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            color: '#667085',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
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

    if (positionCanvas) {
        new Chart(positionCanvas, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chart_position_labels); ?>,
                datasets: [{
                    label: 'Votes',
                    data: <?php echo json_encode($chart_position_votes); ?>,
                    backgroundColor: '#16a34a',
                    hoverBackgroundColor: '#15803d',
                    borderRadius: 12,
                    maxBarThickness: 52
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1300,
                    easing: 'easeOutQuart'
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
}

document.addEventListener('DOMContentLoaded', function() {
    initResultsCharts();
});
</script>

<?php
require_once dirname(__FILE__) . '/../includes/footer.php';
?>