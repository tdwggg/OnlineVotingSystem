<?php
if (session_id() == '') {
    session_start();
}

require_once __DIR__ . '/db_connect.php';

function redirect_register($error) {
    header('Location: register.php?error=' . urlencode($error));
    exit();
}

function make_password_hash_compat($password) {
    if (function_exists('password_hash')) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    $salt = '$2a$10$' . substr(str_replace('+', '.', base64_encode(md5(mt_rand(), true))), 0, 22);
    return crypt($password, $salt);
}

$voter_id = isset($_POST['voter_id']) ? trim($_POST['voter_id']) : '';
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$middle_name = isset($_POST['middle_name']) ? trim($_POST['middle_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$birth_date = isset($_POST['birth_date']) ? trim($_POST['birth_date']) : '';
$sex = isset($_POST['sex']) ? trim($_POST['sex']) : '';
$mobile_number = isset($_POST['mobile_number']) ? trim($_POST['mobile_number']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

$region = isset($_POST['region']) ? trim($_POST['region']) : '';
$province = isset($_POST['province']) ? trim($_POST['province']) : '';
$city_municipality = isset($_POST['city_municipality']) ? trim($_POST['city_municipality']) : '';
$barangay = isset($_POST['barangay']) ? trim($_POST['barangay']) : '';
$specific_address = isset($_POST['specific_address']) ? trim($_POST['specific_address']) : '';

$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

if (
    $voter_id == '' ||
    $first_name == '' ||
    $last_name == '' ||
    $birth_date == '' ||
    $sex == '' ||
    $mobile_number == '' ||
    $email == '' ||
    $region == '' ||
    $province == '' ||
    $city_municipality == '' ||
    $barangay == '' ||
    $specific_address == '' ||
    $password == '' ||
    $confirm_password == ''
) {
    redirect_register('empty');
}

if ($password != $confirm_password) {
    redirect_register('password_mismatch');
}

if (strlen($password) < 8) {
    redirect_register('weak_password');
}

$mobile_digits = preg_replace('/[^0-9]/', '', $mobile_number);

if (strlen($mobile_digits) == 10 && substr($mobile_digits, 0, 1) == '9') {
    $mobile_number = '0' . $mobile_digits;
} elseif (strlen($mobile_digits) == 11 && substr($mobile_digits, 0, 2) == '09') {
    $mobile_number = $mobile_digits;
} else {
    $mobile_number = $mobile_digits;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirect_register('empty');
}

/* Check if Voter ID exists in official registered_voters list */
$sql = "
    SELECT voter_id
    FROM registered_voters
    WHERE voter_id = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    redirect_register('server_error');
}

mysqli_stmt_bind_param($stmt, 's', $voter_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) < 1) {
    mysqli_stmt_close($stmt);
    redirect_register('invalid_voter');
}

mysqli_stmt_close($stmt);

/* Check if this Voter ID already has an account */
$sql = "
    SELECT account_id
    FROM accounts
    WHERE voter_id = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    redirect_register('server_error');
}

mysqli_stmt_bind_param($stmt, 's', $voter_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    redirect_register('already_registered');
}

mysqli_stmt_close($stmt);

/* Check if email is already used by another voter */
$sql = "
    SELECT voter_id
    FROM registered_voters
    WHERE email = ?
      AND voter_id <> ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    redirect_register('server_error');
}

mysqli_stmt_bind_param($stmt, 'ss', $email, $voter_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    redirect_register('email_exists');
}

mysqli_stmt_close($stmt);

$password_hash = make_password_hash_compat($password);

mysqli_query($conn, 'START TRANSACTION');

/* Update voter profile from Incomplete to Complete */
$sql = "
    UPDATE registered_voters
    SET
        first_name = ?,
        middle_name = ?,
        last_name = ?,
        birth_date = ?,
        sex = ?,
        mobile_number = ?,
        email = ?,
        profile_status = 'Complete',
        registration_status = 'Registered'
    WHERE voter_id = ?
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    mysqli_query($conn, 'ROLLBACK');
    redirect_register('server_error');
}

mysqli_stmt_bind_param(
    $stmt,
    'ssssssss',
    $first_name,
    $middle_name,
    $last_name,
    $birth_date,
    $sex,
    $mobile_number,
    $email,
    $voter_id
);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_query($conn, 'ROLLBACK');
    redirect_register('server_error');
}

mysqli_stmt_close($stmt);

/* Save address */
$sql = "
    INSERT INTO voter_addresses
    (
        voter_id,
        region,
        province,
        city_municipality,
        barangay,
        specific_address,
        country,
        created_at,
        updated_at
    )
    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        'Philippines',
        NOW(),
        NOW()
    )
    ON DUPLICATE KEY UPDATE
        region = VALUES(region),
        province = VALUES(province),
        city_municipality = VALUES(city_municipality),
        barangay = VALUES(barangay),
        specific_address = VALUES(specific_address),
        country = 'Philippines',
        updated_at = NOW()
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    mysqli_query($conn, 'ROLLBACK');
    redirect_register('server_error');
}

mysqli_stmt_bind_param(
    $stmt,
    'ssssss',
    $voter_id,
    $region,
    $province,
    $city_municipality,
    $barangay,
    $specific_address
);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_query($conn, 'ROLLBACK');
    redirect_register('server_error');
}

mysqli_stmt_close($stmt);

/* Create login account */
$sql = "
    INSERT INTO accounts
    (
        voter_id,
        username,
        password_hash,
        account_status,
        created_at,
        is_active
    )
    VALUES
    (
        ?,
        ?,
        ?,
        'Active',
        NOW(),
        1
    )
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    mysqli_query($conn, 'ROLLBACK');
    redirect_register('server_error');
}

mysqli_stmt_bind_param($stmt, 'sss', $voter_id, $voter_id, $password_hash);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_query($conn, 'ROLLBACK');
    redirect_register('server_error');
}

mysqli_stmt_close($stmt);

mysqli_query($conn, 'COMMIT');

header('Location: login.php?success=registered');
exit();
?>