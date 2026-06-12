<?php
require_once dirname(__FILE__) . '/../helpers/functions.php';

require_admin();

$page_title = 'Candidate Management';
$page_subtitle = 'Manage candidate profiles, political parties, positions, photos, and platforms.';

$pdo = db();
$errors = array();

function candidate_post_value($key)
{
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
}

function candidate_nullable_value($value)
{
    $value = trim((string) $value);

    if ($value === '') {
        return null;
    }

    return $value;
}

function candidate_modal_id($candidate_id, $prefix)
{
    return $prefix . preg_replace('/[^A-Za-z0-9_]/', '_', (string) $candidate_id);
}

function candidate_form_fields($candidate, $positions)
{
    ob_start();
    ?>
    <div class="ivote-form-section">
        <h6>Candidate Information</h6>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Full Name *</label>
                <input type="text" name="full_name" class="form-control" value="<?php echo e(isset($candidate['full_name']) ? $candidate['full_name'] : ''); ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Political Party</label>
                <input type="text" name="political_party" class="form-control" value="<?php echo e(isset($candidate['political_party']) ? $candidate['political_party'] : ''); ?>" placeholder="Independent / Party Name">
            </div>

            <div class="col-md-6">
                <label class="form-label">Position *</label>
                <select name="position_id" class="form-select" required>
                    <option value="">Select Position</option>
                    <?php foreach ($positions as $position) { ?>
                        <option value="<?php echo e($position['position_id']); ?>" <?php echo ((isset($candidate['position_id']) ? $candidate['position_id'] : '') == $position['position_id']) ? 'selected' : ''; ?>>
                            <?php echo e($position['position_name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Candidate Photo</label>
                <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/gif">
                <small class="text-muted">Accepted: JPG, PNG, GIF. Max 2MB.</small>
            </div>

            <div class="col-md-12">
                <label class="form-label">Platform</label>
                <textarea name="platform" class="form-control" rows="5" placeholder="Candidate platform, agenda, or campaign description"><?php echo e(isset($candidate['platform']) ? $candidate['platform'] : ''); ?></textarea>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

try {
    $positions_stmt = $pdo->query("
        SELECT position_id, position_name, max_votes
        FROM positions
        ORDER BY position_id ASC
    ");
    $positions = $positions_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $positions = array();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf();

    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action == 'create') {
        $full_name = candidate_post_value('full_name');
        $political_party = candidate_post_value('political_party');
        $position_id = candidate_post_value('position_id');
        $platform = candidate_post_value('platform');
        $photo = null;

        if ($full_name == '') {
            $errors[] = 'Candidate full name is required.';
        }

        if ($position_id == '') {
            $errors[] = 'Position is required.';
        }

        if (count($errors) == 0) {
            try {
                $check_position = $pdo->prepare("SELECT COUNT(*) FROM positions WHERE position_id = :position_id");
                $check_position->execute(array(':position_id' => $position_id));

                if ((int) $check_position->fetchColumn() == 0) {
                    $errors[] = 'Selected position does not exist.';
                }
            } catch (Exception $e) {
                $errors[] = 'Unable to validate position.';
            }
        }

        if (count($errors) == 0) {
            try {
                if (isset($_FILES['photo'])) {
                    $photo = upload_candidate_photo($_FILES['photo'], null);
                }

                $stmt = $pdo->prepare("
                    INSERT INTO candidates
                    (full_name, political_party, position_id, photo, platform)
                    VALUES
                    (:full_name, :political_party, :position_id, :photo, :platform)
                ");

                $stmt->execute(array(
                    ':full_name' => $full_name,
                    ':political_party' => candidate_nullable_value($political_party),
                    ':position_id' => $position_id,
                    ':photo' => candidate_nullable_value($photo),
                    ':platform' => candidate_nullable_value($platform)
                ));

                audit_log('Added candidate: ' . $full_name);
                flash('success', 'Candidate added successfully.');
                header('Location: candidates.php');
                exit;
            } catch (Exception $e) {
                $errors[] = 'Unable to add candidate.';
            }
        }
    }

    if ($action == 'update') {
        $candidate_id = candidate_post_value('candidate_id');
        $full_name = candidate_post_value('full_name');
        $political_party = candidate_post_value('political_party');
        $position_id = candidate_post_value('position_id');
        $platform = candidate_post_value('platform');
        $old_photo = candidate_post_value('old_photo');

        if ($candidate_id == '') {
            $errors[] = 'Candidate ID is missing.';
        }

        if ($full_name == '') {
            $errors[] = 'Candidate full name is required.';
        }

        if ($position_id == '') {
            $errors[] = 'Position is required.';
        }

        if (count($errors) == 0) {
            try {
                $check_position = $pdo->prepare("SELECT COUNT(*) FROM positions WHERE position_id = :position_id");
                $check_position->execute(array(':position_id' => $position_id));

                if ((int) $check_position->fetchColumn() == 0) {
                    $errors[] = 'Selected position does not exist.';
                }
            } catch (Exception $e) {
                $errors[] = 'Unable to validate position.';
            }
        }

        if (count($errors) == 0) {
            try {
                $photo = $old_photo;

                if (isset($_FILES['photo'])) {
                    $photo = upload_candidate_photo($_FILES['photo'], $old_photo);
                }

                $stmt = $pdo->prepare("
                    UPDATE candidates
                    SET
                        full_name = :full_name,
                        political_party = :political_party,
                        position_id = :position_id,
                        photo = :photo,
                        platform = :platform
                    WHERE candidate_id = :candidate_id
                ");

                $stmt->execute(array(
                    ':full_name' => $full_name,
                    ':political_party' => candidate_nullable_value($political_party),
                    ':position_id' => $position_id,
                    ':photo' => candidate_nullable_value($photo),
                    ':platform' => candidate_nullable_value($platform),
                    ':candidate_id' => $candidate_id
                ));

                audit_log('Updated candidate: ' . $full_name);
                flash('success', 'Candidate updated successfully.');
                header('Location: candidates.php');
                exit;
            } catch (Exception $e) {
                $errors[] = 'Unable to update candidate.';
            }
        }
    }

    if ($action == 'delete') {
        $candidate_id = candidate_post_value('candidate_id');
        $photo = candidate_post_value('photo');

        if ($candidate_id == '') {
            $errors[] = 'Candidate ID is missing.';
        }

        if (count($errors) == 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM candidates WHERE candidate_id = :candidate_id");
                $stmt->execute(array(':candidate_id' => $candidate_id));

                if ($photo != '') {
                    delete_candidate_photo($photo);
                }

                audit_log('Deleted candidate ID: ' . $candidate_id);
                flash('success', 'Candidate deleted successfully.');
                header('Location: candidates.php');
                exit;
            } catch (Exception $e) {
                $errors[] = 'Unable to delete candidate. This candidate may already have vote records.';
            }
        }
    }

    if (count($errors) > 0) {
        foreach ($errors as $error) {
            flash('danger', $error);
        }
    }
}

$search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
$position_filter = isset($_GET['position_id']) ? trim((string) $_GET['position_id']) : '';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page = 10;

$where = array();
$params = array();

if ($search != '') {
    $where[] = "(c.full_name LIKE :search OR c.political_party LIKE :search OR c.platform LIKE :search OR p.position_name LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

if ($position_filter != '') {
    $where[] = "c.position_id = :position_id";
    $params[':position_id'] = $position_filter;
}

$where_sql = '';

if (count($where) > 0) {
    $where_sql = 'WHERE ' . implode(' AND ', $where);
}

$count_sql = "
    SELECT COUNT(*)
    FROM candidates c
    LEFT JOIN positions p ON c.position_id = p.position_id
    $where_sql
";

$count_stmt = $pdo->prepare($count_sql);

foreach ($params as $key => $value) {
    $count_stmt->bindValue($key, $value);
}

$count_stmt->execute();
$total_rows = (int) $count_stmt->fetchColumn();

$pagination = paginate($total_rows, $page, $per_page);
$page = $pagination[0];
$total_pages = $pagination[1];
$offset = $pagination[2];

$sql = "
    SELECT
        c.candidate_id,
        c.full_name,
        c.political_party,
        c.position_id,
        c.photo,
        c.platform,
        p.position_name
    FROM candidates c
    LEFT JOIN positions p ON c.position_id = p.position_id
    $where_sql
    ORDER BY p.position_id ASC, c.candidate_id ASC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->bindValue(':limit', (int) $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
$stmt->execute();

$candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_candidates = 0;
$total_positions = 0;

try {
    $total_candidates = (int) $pdo->query("SELECT COUNT(*) FROM candidates")->fetchColumn();
    $total_positions = (int) $pdo->query("SELECT COUNT(*) FROM positions")->fetchColumn();
} catch (Exception $e) {
}

$flashes = consume_flash();

require_once dirname(__FILE__) . '/../includes/header.php';
require_once dirname(__FILE__) . '/../includes/sidebar.php';
?>

<div class="ivote-management-page">

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

    <div class="ivote-filter-card">
        <form method="GET" action="candidates.php" class="ivote-filter-form">
            <div>
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" value="<?php echo e($search); ?>" placeholder="Search candidate, party, position, or platform">
            </div>

            <div>
                <label class="form-label">Position</label>
                <select name="position_id" class="form-select">
                    <option value="">All positions</option>
                    <?php foreach ($positions as $position) { ?>
                        <option value="<?php echo e($position['position_id']); ?>" <?php echo ($position_filter == $position['position_id']) ? 'selected' : ''; ?>>
                            <?php echo e($position['position_name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit" class="btn btn-ivote-outline">
                <i class="bi bi-funnel me-1"></i>
                Filter
            </button>

            <a href="candidates.php" class="btn btn-light border ivote-reset-btn">
                Reset
            </a>

            <button type="button" class="btn btn-ivote" data-bs-toggle="modal" data-bs-target="#addCandidateModal">
                <i class="bi bi-plus-circle me-1"></i>
                Add Candidate
            </button>
        </form>
    </div>

    <div class="ivote-card ivote-data-card">
        <div class="ivote-card-header">
            <h3 class="ivote-section-title">
                <i class="bi bi-person-badge text-primary me-1"></i>
                Candidate Records
            </h3>

            <span class="ivote-record-count">
                <?php echo number_format($total_rows); ?> record(s)
            </span>
        </div>

        <div class="table-responsive">
            <table class="table ivote-management-table">
                <thead>
                    <tr>
                        <th>Candidate</th>
                        <th>Political Party</th>
                        <th>Position</th>
                        <th>Platform</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($candidates) > 0) { ?>
                        <?php foreach ($candidates as $candidate) { ?>
                            <?php
                                $view_modal = candidate_modal_id($candidate['candidate_id'], 'viewCandidate');
                                $edit_modal = candidate_modal_id($candidate['candidate_id'], 'editCandidate');
                                $delete_modal = candidate_modal_id($candidate['candidate_id'], 'deleteCandidate');

                                $photo_url = candidate_photo_url($candidate['photo']);
                            ?>

                            <tr>
                                <td>
                                    <div class="ivote-candidate-cell">
                                        <img src="<?php echo e($photo_url); ?>" alt="Candidate Photo">
                                        <div>
                                            <div class="fw-bold"><?php echo e($candidate['full_name']); ?></div>
                                            <small class="text-muted">ID: <?php echo e($candidate['candidate_id']); ?></small>
                                        </div>
                                    </div>
                                </td>

                                <td><?php echo e($candidate['political_party'] ? $candidate['political_party'] : 'Independent'); ?></td>

                                <td>
                                    <span class="badge text-bg-secondary">
                                        <?php echo e($candidate['position_name'] ? $candidate['position_name'] : 'No Position'); ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="ivote-platform-preview">
                                        <?php
                                            $platform = trim((string) $candidate['platform']);
                                            if ($platform == '') {
                                                echo 'No platform provided.';
                                            } else {
                                                echo e(strlen($platform) > 80 ? substr($platform, 0, 80) . '...' : $platform);
                                            }
                                        ?>
                                    </span>
                                </td>

                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-ivote-icon" data-bs-toggle="modal" data-bs-target="#<?php echo e($view_modal); ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <button type="button" class="btn btn-sm btn-ivote-icon" data-bs-toggle="modal" data-bs-target="#<?php echo e($edit_modal); ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <button type="button" class="btn btn-sm btn-ivote-icon danger" data-bs-toggle="modal" data-bs-target="#<?php echo e($delete_modal); ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            <div class="modal fade" id="<?php echo e($view_modal); ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content ivote-modal">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Candidate Profile</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="ivote-candidate-profile">
                                                <div class="ivote-candidate-photo-large">
                                                    <img src="<?php echo e($photo_url); ?>" alt="Candidate Photo">
                                                </div>

                                                <div class="ivote-profile-view">
                                                    <div>
                                                        <span>Full Name</span>
                                                        <strong><?php echo e($candidate['full_name']); ?></strong>
                                                    </div>

                                                    <div>
                                                        <span>Political Party</span>
                                                        <strong><?php echo e($candidate['political_party'] ? $candidate['political_party'] : 'Independent'); ?></strong>
                                                    </div>

                                                    <div>
                                                        <span>Position</span>
                                                        <strong><?php echo e($candidate['position_name'] ? $candidate['position_name'] : 'No Position'); ?></strong>
                                                    </div>

                                                    <div class="full">
                                                        <span>Platform</span>
                                                        <strong><?php echo nl2br(e($candidate['platform'] ? $candidate['platform'] : 'No platform provided.')); ?></strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="<?php echo e($edit_modal); ?>" tabindex="-1">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content ivote-modal">
                                        <form method="POST" action="candidates.php" enctype="multipart/form-data">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Candidate</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="candidate_id" value="<?php echo e($candidate['candidate_id']); ?>">
                                                <input type="hidden" name="old_photo" value="<?php echo e($candidate['photo']); ?>">

                                                <?php echo candidate_form_fields($candidate, $positions); ?>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-ivote">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="<?php echo e($delete_modal); ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content ivote-modal">
                                        <form method="POST" action="candidates.php">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete Candidate</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="candidate_id" value="<?php echo e($candidate['candidate_id']); ?>">
                                                <input type="hidden" name="photo" value="<?php echo e($candidate['photo']); ?>">

                                                <p>
                                                    Are you sure you want to delete
                                                    <strong><?php echo e($candidate['full_name']); ?></strong>?
                                                </p>

                                                <small class="text-muted">
                                                    This action cannot be undone. Candidates with vote records may not be deletable.
                                                </small>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Delete Candidate</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                No candidates found.
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
                            <a class="page-link" href="candidates.php?search=<?php echo urlencode($search); ?>&position_id=<?php echo urlencode($position_filter); ?>&page=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal fade" id="addCandidateModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content ivote-modal">
            <form method="POST" action="candidates.php" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Add Candidate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="create">

                    <?php
                        $blank_candidate = array(
                            'full_name' => '',
                            'political_party' => '',
                            'position_id' => '',
                            'photo' => '',
                            'platform' => ''
                        );

                        echo candidate_form_fields($blank_candidate, $positions);
                    ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-ivote">Add Candidate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once dirname(__FILE__) . '/../includes/footer.php';
?>