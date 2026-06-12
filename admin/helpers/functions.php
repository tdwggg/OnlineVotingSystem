<?php
require_once dirname(__FILE__) . '/../config/config.php';

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url($path = '')
{
    $base = rtrim(BASE_URL, '/');
    $path = ltrim($path, '/');

    if ($base == '') {
        return '/' . ltrim($path, '/');
    }

    return $base . ($path ? '/' . $path : '');
}

function redirect($path)
{
    header('Location: ' . url($path));
    exit;
}

function is_logged_in()
{
    return !empty($_SESSION['admin_id']);
}

function require_admin()
{
    if (!is_logged_in()) {
        redirect('auth/login.php');
    }
}

function admin_id()
{
    return isset($_SESSION['admin_id']) ? (int) $_SESSION['admin_id'] : 0;
}

function admin_name()
{
    return isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Administrator';
}

function ivote_random_bytes($length)
{
    if (function_exists('random_bytes')) {
        return random_bytes($length);
    }

    if (function_exists('openssl_random_pseudo_bytes')) {
        $strong = false;
        $bytes = openssl_random_pseudo_bytes($length, $strong);

        if ($bytes !== false) {
            return $bytes;
        }
    }

    $bytes = '';

    for ($i = 0; $i < $length; $i++) {
        $bytes .= chr(mt_rand(0, 255));
    }

    return $bytes;
}

function ivote_hash_equals($known, $user)
{
    if (function_exists('hash_equals')) {
        return hash_equals($known, $user);
    }

    $known = (string) $known;
    $user = (string) $user;

    if (strlen($known) !== strlen($user)) {
        return false;
    }

    $result = 0;

    for ($i = 0; $i < strlen($known); $i++) {
        $result |= ord($known[$i]) ^ ord($user[$i]);
    }

    return $result === 0;
}

function ivote_password_verify($password, $hash)
{
    return verify_password_compat($password, $hash);
}

function verify_password_compat($password, $stored_hash)
{
    if (function_exists('password_verify')) {
        if (password_verify($password, $stored_hash)) {
            return true;
        }
    }

    if (!$stored_hash) {
        return false;
    }

    if (function_exists('crypt')) {
        if (strlen($stored_hash) > 20 && substr($stored_hash, 0, 4) == '$2y$') {
            $test_hash = crypt($password, $stored_hash);

            if ($test_hash == $stored_hash) {
                return true;
            }
        }

        if (strlen($stored_hash) > 20 && substr($stored_hash, 0, 4) == '$2a$') {
            $test_hash = crypt($password, $stored_hash);

            if ($test_hash == $stored_hash) {
                return true;
            }
        }
    }

    if (hash('sha256', $password) == $stored_hash) {
        return true;
    }

    return false;
}

function hash_password_compat($password)
{
    if (function_exists('password_hash')) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    return hash('sha256', $password);
}

function ivote_sum_votes($rows)
{
    $total = 0;

    foreach ($rows as $row) {
        $total += isset($row['vote_total']) ? (int) $row['vote_total'] : 0;
    }

    return $total;
}

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(ivote_random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf()
{
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    if (!$token || empty($_SESSION['csrf_token']) || !ivote_hash_equals($_SESSION['csrf_token'], $token)) {
        flash('danger', 'Security token mismatch. Please try again.');

        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin/index.php';

        header('Location: ' . $referer);
        exit;
    }
}

function flash($type, $message)
{
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = array();
    }

    $_SESSION['flash'][] = array(
        'type' => $type,
        'message' => $message
    );
}

function consume_flash()
{
    $messages = isset($_SESSION['flash']) ? $_SESSION['flash'] : array();

    unset($_SESSION['flash']);

    return $messages;
}

function audit_log($action)
{
    try {
        $stmt = db()->prepare("
            INSERT INTO audit_logs (admin_name, action, timestamp)
            VALUES (:admin_name, :action, NOW())
        ");

        $stmt->execute(array(
            ':admin_name' => admin_name(),
            ':action' => $action
        ));
    } catch (Exception $th) {
        return false;
    }

    return true;
}

function log_admin_action($admin_name, $action)
{
    try {
        $pdo = db();

        $stmt = $pdo->prepare("
            INSERT INTO audit_logs (admin_name, action, timestamp)
            VALUES (:admin_name, :action, NOW())
        ");

        $stmt->execute(array(
            ':admin_name' => $admin_name,
            ':action' => $action
        ));
    } catch (Exception $e) {
        return false;
    }

    return true;
}

function count_rows($table)
{
    $allowed = array(
        'admins',
        'registered_voters',
        'accounts',
        'candidates',
        'positions',
        'votes',
        'elections',
        'audit_logs'
    );

    if (!in_array($table, $allowed, true)) {
        return 0;
    }

    return (int) db()->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
}

function get_post_string($key, $maxLength = 255)
{
    $value = trim((string) (isset($_POST[$key]) ? $_POST[$key] : ''));

    if (function_exists('mb_substr')) {
        return mb_substr($value, 0, $maxLength);
    }

    return substr($value, 0, $maxLength);
}

function require_fields($fields)
{
    $errors = array();

    foreach ($fields as $field => $label) {
        if (trim((string) (isset($_POST[$field]) ? $_POST[$field] : '')) === '') {
            $errors[] = $label . ' is required.';
        }
    }

    return $errors;
}

function valid_date($date)
{
    if ($date === '' || !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $date)) {
        return false;
    }

    $parts = explode('-', $date);

    return checkdate((int) $parts[1], (int) $parts[2], (int) $parts[0]);
}

function upload_candidate_photo($file, $oldPhoto = null)
{
    $fileError = isset($file['error']) ? $file['error'] : UPLOAD_ERR_NO_FILE;

    if (!$file || empty($file['name']) || $fileError === UPLOAD_ERR_NO_FILE) {
        return $oldPhoto;
    }

    if ($fileError !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Photo upload failed.');
    }

    $fileSize = isset($file['size']) ? $file['size'] : 0;

    if ($fileSize > 2 * 1024 * 1024) {
        throw new RuntimeException('Photo must not exceed 2 MB.');
    }

    $allowed = array(
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif'
    );

    $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));

    if (!array_key_exists($extension, $allowed)) {
        throw new RuntimeException('Photo must be JPG, PNG, or GIF.');
    }

    if (class_exists('finfo')) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file((string) $file['tmp_name']);

        if ($mime !== $allowed[$extension]) {
            throw new RuntimeException('Invalid photo file type.');
        }
    }

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    $filename = bin2hex(ivote_random_bytes(16)) . '.' . $extension;
    $destination = UPLOAD_DIR . $filename;

    if (!move_uploaded_file((string) $file['tmp_name'], $destination)) {
        throw new RuntimeException('Unable to save uploaded photo.');
    }

    if ($oldPhoto) {
        delete_candidate_photo($oldPhoto);
    }

    return $filename;
}

function candidate_photo_url($filename)
{
    if (!$filename) {
        return 'https://ui-avatars.com/api/?background=0d47a1&color=fff&name=Candidate';
    }

    return url('assets/uploads/candidates/' . rawurlencode($filename));
}

function delete_candidate_photo($filename)
{
    if (!$filename) {
        return;
    }

    $path = UPLOAD_DIR . basename($filename);

    if (is_file($path)) {
        unlink($path);
    }
}

function badge_class($status)
{
    switch (strtolower($status)) {
        case 'verified':
        case 'registered':
        case 'open':
        case 'active':
        case 'complete':
            return 'text-bg-success';

        case 'pending':
        case 'unregistered':
        case 'draft':
        case 'incomplete':
            return 'text-bg-warning';

        case 'rejected':
        case 'blocked':
        case 'closed':
        case 'inactive':
            return 'text-bg-danger';

        default:
            return 'text-bg-secondary';
    }
}

function paginate($total, $page, $perPage)
{
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = max(1, min($page, $totalPages));
    $offset = ($page - 1) * $perPage;

    return array($page, $totalPages, $offset);
}
?>