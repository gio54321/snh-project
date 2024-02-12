<?php

require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/utils/validation.php';


if (!is_logged_in()) {
    log_warning_unauth("Change password page get request from anonymous user");

    header('Location: /', true, 200);
    exit;
}

$error = "";

function do_change_password()
{
    global $error;

    if (!check_csrf_token()) {
        log_warning_auth("Change password post request invalid CSRF token");

        header('Location: /', true, 403);
        exit;
    }

    if (
        !isset($_POST['old_password']) ||
        !isset($_POST['password']) ||
        !isset($_POST['confirm_password'])
    ) {
        log_info_auth("Change password request missing fields");

        $error = "Missing fields";
        return;
    }

    $old_password = $_POST['old_password'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (
        !is_string($old_password) ||
        !is_string($password) ||
        !is_string($confirm_password)
    ) {
        log_info_auth("Change password request invalid fields");

        $error = "Invalid fields";
        return;
    }

    $user = execute_query('SELECT * FROM users WHERE id = :id', [
        'id' => $_SESSION['user_id']
    ])->fetch();

    if (!password_verify($old_password, $user['password'])) {
        log_warning_auth("Change password post request incorrect password");

        $error = "Invalid password";
        http_response_code(400);
        return;
    }

    if ($password !== $confirm_password) {
        log_info_auth("Change password password and confirm password fields do not match.");

        $error = "Passwords do not match";
        return;
    }

    if (!validate_password_strength($password)) {
        log_info_unauth("Change password request new password strength check failed");

        $error = "New password not strong enough";
        return;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    execute_query('UPDATE users SET password = :password WHERE id = :id', [
        'id' => $user['id'],
        'password' => $hashed_password
    ]);

    send_mail(
        $user['email'],
        "YASBS - Password changed",
        "Your password has been successfully changed"
    );

    log_info_auth("Change password request successful");
    header('Location: /profile.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    do_change_password();
}

require_once __DIR__ . '/html/header.php';
?>

<div class="h-screen flex items-center justify-center pb-32">

    <div class="p-8 border-2 border-gray-200 border-dashed rounded-lg w-full md:w-2/5 mx-4">
        <h2 class="text-3xl font-extrabold mb-6">Change password</h2>

        <?php if ($error !== "") { ?>
            <div class="flex items-start mb-6 text-sm font-bold text-red-500">
                <?php echo $error ?>
            </div>
        <?php } ?>

        <form action="/change_password.php" method="post">
            <div class="mb-6">
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Old password</label>
                <input type="password" id="old_password" name="old_password" , class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="•••••••••" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900">New password</label>
                <input type="password" id="password" name="password" , class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="•••••••••" required>
            </div>
            <div class="mb-6">
                <label for="confirm_password" class="block mb-2 text-sm font-medium text-gray-900">Password confirm</label>
                <input type="password" id="confirm_password" name="confirm_password" , class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="•••••••••" required>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?>" />
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full md:w-auto px-5 py-2.5 text-center">Change</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/html/footer.php' ?>