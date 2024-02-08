<?php

require_once __DIR__ . '/utils.php';

if (!is_logged_in()) {
    log_warning_unauth("Unauthenticated download request");

    http_response_code(403);
    exit;
}

if (!isset($_GET["id"])) {
    log_warning_auth("No book id provided for download request");

    http_response_code(400);
    exit;
}

$result = execute_query('SELECT * FROM owned_books WHERE user_id=:user_id AND book_id=:book_id', [
    "user_id" => $_SESSION['user_id'],
    "book_id" => $_GET["id"]
])->fetchAll();

if (count($result) == 0) {
    log_warning_auth("Unauthorized book download request", [
        "book_id" => $_GET["id"]
    ]);

    http_response_code(403);
    exit;
}

$result = execute_query('SELECT file FROM books WHERE id=:book_id', [
    'book_id' => $_GET["id"]
])->fetch();

if (!isset($result['file'])) {
    log_error_auth("Book file not found", [
        'book_id' => $_GET["id"]
    ]);

    http_response_code(500);
    exit;
}

$file = $result['file'];

log_info_auth("Book downloaded", [
    "book_id" => $_GET["id"],
    "file" => $file
]);

header('Content-Type: application/pdf');
header('X-Accel-Redirect: ' . $file);
