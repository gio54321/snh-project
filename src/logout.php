<?php

require_once __DIR__ . '/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

// check that the user is logged in
if (!is_logged_in()) {
    log_warning_unauth("Unlogged client tried to log out");

    header('Location: /');
    exit;
}

// check that the CSRF token is valid
if (!check_csrf_token()) {
    log_warning_auth("Invalid CSRF token for logout request");

    header('Location: /');
    exit;
}

log_info_auth("User logged out");

// clear the session
unset($_SESSION['user_id']);
unset($_SESSION['username']);
unset($_SESSION['csrf_token']);

header('Location: /');
exit;
