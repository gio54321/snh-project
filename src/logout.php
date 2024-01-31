<?php

require_once __DIR__ . '/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

session_start();

// check that the user is logged in
if (!is_logged_in()) {
    header('Location: /');
    exit;
}

// check that the CSRF token is valid
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: /');
    exit;
}

// clear the session
unset($_SESSION['user_id']);
unset($_SESSION['username']);
unset($_SESSION['csrf_token']);

header('Location: /');
exit;
