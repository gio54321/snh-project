<?php
require_once __DIR__ . '/utils.php';

if (!isset($_GET['token'])) {
    log_info_unauth("Validation request missing token");

    die('Missing parameters');
}

$token = $_GET['token'];

if (!is_string($token)) {
    log_info_unauth("Validation request invalid token [1]");

    die('Invalid parameters');
}

$hashed_token = hash('sha256', $token);

$user = execute_query('SELECT * FROM users WHERE verification_token = :token', [
    'token' => $hashed_token
])->fetch();

if (!$user) {
    log_info_unauth("Validation request missing token [2]");

    die('Invalid token');
}

execute_query('UPDATE users SET verified = 1, verification_token=NULL WHERE id = :id', [
    'id' => $user['id']
]);

log_info_unauth("Validation request success", [
    "user_id" => $user['id'],
    "username" => $user['username']
]);

require_once __DIR__ . '/html/header.php';
?>

<div class="flex items-center justify-center mt-10">
    <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Account verified!</h5>
        <a href="/login.php" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">
            Login
            <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9" />
            </svg>
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/html/footer.php' ?>