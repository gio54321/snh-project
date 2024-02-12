<?php

require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/utils/validation.php';

$error = "";

function do_reset_password()
{
    global $error;

    if (
        !isset($_POST['password']) ||
        !isset($_POST['confirm_password']) ||
        !isset($_POST['token'])
    ) {
        log_info_unauth("Reset password request missing fields");

        $error = "Missing fields";
        return;
    }

    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $token = $_POST['token'];

    if (
        !is_string($password) ||
        !is_string($confirm_password) ||
        !is_string($token)
    ) {
        log_info_unauth("Reset password request invalid fields");

        $error = "Invalid fields";
        return;
    }

    $user = execute_query('SELECT * FROM users WHERE reset_password_token = :token', [
        'token' => hash('sha256', $token)
    ])->fetch();

    if (!$user) {
        log_info_unauth("Reset password request invalid reset password token");

        $error = "Invalid token";
        return;
    }

    if ($password !== $confirm_password) {
        log_info_unauth("Reset password password and confirm password fields do not match.", [
            "user_id" => $user['id'],
            "username" => $user['username']
        ]);

        $error = "Passwords do not match";
        return;
    }

    if (!validate_password_strength($password)) {
        log_info_unauth("Reset password request new password strength check failed", [
            "user_id" => $user['id'],
            "username" => $user['username']
        ]);

        $error = "Password not strong enough";
        return;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    execute_query('UPDATE users SET reset_password_token=NULL, password=:password WHERE id = :id', [
        'id' => $user['id'],
        'password' => $hashed_password
    ]);

    log_info_unauth("Reset password request successful", [
        "user_id" => $user['id'],
        "username" => $user['username']
    ]);

    header('Location: /login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    do_reset_password();
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];
} elseif (isset($_POST['token'])) {
    $token = $_POST['token'];
} else {
    log_info_unauth("Reset password request missing token");
    header('Location: /');
    exit;
}

require_once __DIR__ . '/html/header.php';
?>

<div class="h-screen flex items-center justify-center pb-32">

    <div class="p-8 border-2 border-gray-200 border-dashed rounded-lg w-full md:w-2/5 mx-4">
        <h2 class="text-3xl font-extrabold mb-6">Reset password</h2>

        <?php if ($error !== "") { ?>
            <div class="flex items-start mb-6 text-sm font-bold text-red-500">
                <?php echo $error ?>
            </div>
        <?php } ?>

        <form action="/reset_password.php" method="post">
            <div class="mb-6">
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                <input type="password" id="password" name="password" , class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="•••••••••" required>
            </div>
            <div class="mb-6">
                <label for="confirm_password" class="block mb-2 text-sm font-medium text-gray-900">Password confirm</label>
                <input type="password" id="confirm_password" name="confirm_password" , class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="•••••••••" required>
            </div>

            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token) ?>">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full md:w-auto px-5 py-2.5 text-center">Reset</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/html/footer.php' ?>