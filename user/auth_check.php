<?php
if (session_id() == '') {
    session_start();
}

if (!isset($_SESSION['voter_id']) || $_SESSION['voter_id'] == '') {
    header('Location: login.php?error=login_required');
    exit();
}

require_once __DIR__ . '/db_connect.php';

$voter_id = $_SESSION['voter_id'];

$sql = "
    SELECT 
        rv.voter_id,
        rv.first_name,
        rv.last_name,
        rv.birth_date,
        rv.email,
        rv.registration_status,
        a.is_active
    FROM registered_voters rv
    INNER JOIN accounts a ON rv.voter_id = a.voter_id
    WHERE rv.voter_id = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    session_destroy();
    header('Location: login.php?error=server_error');
    exit();
}

mysqli_stmt_bind_param($stmt, 's', $voter_id);
mysqli_stmt_execute($stmt);

mysqli_stmt_bind_result(
    $stmt,
    $auth_voter_id,
    $auth_first_name,
    $auth_last_name,
    $auth_birth_date,
    $auth_email,
    $auth_registration_status,
    $auth_is_active
);

if (!mysqli_stmt_fetch($stmt)) {
    mysqli_stmt_close($stmt);
    session_destroy();
    header('Location: login.php?error=account_not_found');
    exit();
}

mysqli_stmt_close($stmt);

if ($auth_is_active != 1) {
    session_destroy();
    header('Location: login.php?error=inactive');
    exit();
}

if ($auth_registration_status != 'Verified' && $auth_registration_status != 'Registered') {
    session_destroy();
    header('Location: login.php?error=not_registered');
    exit();
}

$auth_full_name = trim($auth_first_name . ' ' . $auth_last_name);

if ($auth_full_name == '') {
    $auth_full_name = $auth_voter_id;
}

$auth_initials = strtoupper(substr($auth_first_name, 0, 1) . substr($auth_last_name, 0, 1));

if ($auth_initials == '') {
    $auth_initials = 'V';
}
?>