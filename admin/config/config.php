<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'ivoteph');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', '/');

define('APP_NAME', 'iVotePH Admin');
define('APP_VERSION', '1.0.0');
define('UPLOAD_DIR', dirname(__FILE__) . '/../assets/uploads/candidates/');
define('UPLOAD_URL', '/assets/uploads/candidates/');

function start_secure_session()
{
    if (session_id() !== '') {
        return;
    }

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

    session_name('IVOTEPH_ADMIN_SESSION');

    // PHP 7.3+ supports SameSite through an options array. Older WAMP/PHP needs the old signature.
    if (defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 70300) {
        session_set_cookie_params(array(
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax'
        ));
    } else {
        session_set_cookie_params(0, '/', '', $isHttps, true);
    }

    session_start();

    if (empty($_SESSION['initiated'])) {
        session_regenerate_id(true);
        $_SESSION['initiated'] = true;
    }
}

function db()
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ));

    // Extra safety for older MySQL/PHP builds that ignore charset inside the DSN.
    $pdo->exec('SET NAMES utf8mb4');

    return $pdo;
}

start_secure_session();
