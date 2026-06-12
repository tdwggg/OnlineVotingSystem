<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($page_title)) {
    $page_title = 'Dashboard';
}

if (!isset($page_subtitle)) {
    $page_subtitle = 'Welcome to iVotePH Admin System.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>iVotePH Admin - <?php echo e($page_title); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- iVotePH Custom CSS -->
    <link href="../assets/css/style.css?v=ivote-dashboard-2026" rel="stylesheet">
</head>
<body>

<div class="ivote-admin-shell">