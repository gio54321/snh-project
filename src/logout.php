<?php

require_once __DIR__ . '/utils.php';

unset($_SESSION['user_id']);
unset($_SESSION['username']);
unset($_SESSION['csrf_token']);

header('Location: /');
exit;
