<?php
require_once dirname(__FILE__) . '/helpers/functions.php';

if (is_logged_in()) {
    redirect('admin/index.php');
}

redirect('auth/login.php');
