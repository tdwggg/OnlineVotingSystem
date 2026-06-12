<?php
if (session_id() == '') {
    session_start();
}

require_once __DIR__ . '/db_connect.php';

function verify_password_compat($password, $hash) {
    if (function_exists('password_verify')) {
        return password_verify($password, $hash);
    }

    return crypt($password, $hash) === $hash;
}

$voter_id = '';
$password = '';

if (isset($_POST['voter_id'])) {
    $voter_id = trim($_POST['voter_id']);
}

if (isset($_POST['password'])) {
    $password = trim($_POST['password']);
}

if ($voter_id == '' || $password == '') {
    header('Location: login.php?error=empty');
    exit();
}

$sql = "
    SELECT 
        a.voter_id,
        a.password_hash,
        a.account_status,
        a.is_active,
        rv.first_name,
        rv.last_name,
        rv.profile_status,
        rv.registration_status
    FROM accounts a
    INNER JOIN registered_voters rv ON a.voter_id = rv.voter_id
    WHERE TRIM(a.voter_id) = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    header('Location: login.php?error=server_error');
    exit();
}

mysqli_stmt_bind_param($stmt, 's', $voter_id);
mysqli_stmt_execute($stmt);

mysqli_stmt_bind_result(
    $stmt,
    $db_voter_id,
    $db_password_hash,
    $db_account_status,
    $db_is_active,
    $db_first_name,
    $db_last_name,
    $db_profile_status,
    $db_registration_status
);

if (!mysqli_stmt_fetch($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: login.php?error=invalid');
    exit();
}

mysqli_stmt_close($stmt);

if (!verify_password_compat($password, $db_password_hash)) {
    header('Location: login.php?error=invalid');
    exit();
}

if ($db_is_active != 1 || $db_account_status != 'Active') {
    header('Location: login.php?error=inactive');
    exit();
}

if ($db_profile_status != 'Complete' || $db_registration_status != 'Registered') {
    header('Location: login.php?error=not_registered');
    exit();
}

session_regenerate_id(true);

$_SESSION['voter_id'] = $db_voter_id;
$_SESSION['voter_name'] = trim($db_first_name . ' ' . $db_last_name);

header('Location: index.php');
exit();
?>