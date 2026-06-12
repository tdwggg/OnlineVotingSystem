<?php
require_once '../helpers/functions.php';

if (!isset($_SESSION)) {
    session_start();
}

$error = '';

if (isset($_SESSION['admin_id'])) {
    header('Location: ../admin/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = '';
    $password = '';

    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
    }

    if (isset($_POST['password'])) {
        $password = $_POST['password'];
    }

    if ($email == '' || $password == '') {
        $error = 'Please enter your email and password.';
    } else {
        $pdo = db();

        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email LIMIT 1");
        $stmt->execute(array(':email' => $email));
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && verify_password_compat($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['admin_name'];
            $_SESSION['admin_email'] = $admin['email'];

            log_admin_action($admin['admin_name'], 'Login');

            header('Location: ../admin/index.php');
            exit();
        } else {
            $error = 'Invalid admin email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>iVotePH Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;800;900&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="ivote-login-page">
    <div class="ivote-login-card">
        <div class="ivote-login-logo">
            <img src="../assets/img/ivoteph-logo.png" alt="iVotePH Logo">
        </div>

        <h1 class="ivote-login-title">Admin Login</h1>
        <p class="ivote-login-subtitle">Secure access to the iVotePH election management system.</p>

        <?php if ($error != '') { ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label fw-bold">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-envelope-fill text-primary"></i>
                    </span>
                    <input type="email" name="email" class="form-control" placeholder="admin@ivoteph.test" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-lock-fill text-primary"></i>
                    </span>
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-ivote w-100">
                <i class="bi bi-shield-lock-fill me-1"></i>
                Login to Dashboard
            </button>
        </form>

        <div class="text-center mt-4">
            <small class="text-muted">
                iVotePH Admin System<br>
                Academic Election Simulation
            </small>
        </div>
    </div>
</div>

</body>
</html>