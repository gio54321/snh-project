<?php

require_once __DIR__ . '/utils.php';

//TODO check CSRF token
unset($_SESSION['user_id']);
unset($_SESSION['username']);
unset($_SESSION['csrf_token']);
session_destroy();

header('Location: /');
exit;
