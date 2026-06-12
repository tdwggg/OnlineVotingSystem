<?php
require_once dirname(__FILE__) . '/../helpers/functions.php';

if (session_id() == '') {
    session_start();
}

$_SESSION = array();

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

header('Location: ../auth/login.php');
exit;
?>