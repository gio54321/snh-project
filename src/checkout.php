<?php
require_once __DIR__ . '/utils.php';

function do_checkout() {
    if (!is_logged_in()) {
        header('Location: /login.php', true, 401);
        exit;
    }

    header('Location: /checkout.php', true, 200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    do_checkout();
}

if (!is_logged_in()) {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/html/header.php';
?>



<?php
require_once __DIR__ . '/html/footer.php';
?>