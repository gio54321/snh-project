<?php

require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/utils/validation.php';

$error = "";

function do_register()
{
    global $error;

    if (
        !isset($_POST['username']) ||
        !isset($_POST['email']) ||
        !isset($_POST['password']) ||
        !isset($_POST['confirm_password'])
    ) {
        log_info_unauth("Register request missing fields");
        
        $error = "Missing fields";
        return;
    }

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (
        !is_string($username) ||
        !is_string($email) ||
        !is_string($password) ||
        !is_string($confirm_password)
    ) {
        log_info_unauth("Register request invalid fields");

        $error = "Invalid fields";
        return;
    }

    if ($password !== $confirm_password) {
        log_info_unauth("Register request password and confirm password fields do not match");

        $error = "Passwords do not match";
        return;
    }

    if (!validate_email($email)) {
        log_info_unauth("Register request invalid email.");

        $error = "Invalid email";
        return;
    }

    $username_already_used = execute_query(
        'SELECT id from users where username=:username',
        [
            'username' => $username
        ]
    )->fetchAll();

    if (count($username_already_used) > 0) {
        log_info_unauth("Register request username already used.");

        $error = "Username already used";
        return;
    }

    $email_already_used = execute_query(
        'SELECT id from users where email=:email',
        [
            'email' => $email
        ]
    )->fetchAll();

    if (count($email_already_used) > 0) {
        log_info_unauth("Register request email already used.");

        $error = "Email already used";
        return;
    }

    if (!validate_password_strength($password)) {
        log_info_unauth("Register request password strength check fail");

        $error = "Password not strong enough";
        return;
    }

    $domain_name = $_ENV['DOMAIN_NAME'];
    $verification_token = bin2hex(random_bytes(32));
    if (!send_mail(
        $email,
        "YASBS - Verify your account",
        "Click <a href=\"http://$domain_name/verify_account.php?token=$verification_token\">here</a> to verify your account"
    )) {
        // assume that if the email is not sent, the email is not valid
        log_info_unauth("Register request verify email not sent");
    
        $error = "Invalid email";
        return;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $result = execute_query('INSERT INTO users (username, email, password, verification_token) VALUES (:username, :email, :password, :verification_token)', [
        'username' => $username,
        'email' => $email,
        'password' => $hashed_password,
        'verification_token' => hash('sha256', $verification_token)
    ]);

    if (!$result) {
        log_info_unauth("Register request registration error");

        $error = "Error in the registration";
        return;
    }

    log_info_unauth("Registration successful", [
        "username" => $username
    ]);

    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    do_register();
}

require_once __DIR__ . '/html/header.php';
?>

<div class="h-screen flex items-center justify-center pb-32">

    <div class="p-8 border-2 border-gray-200 border-dashed rounded-lg w-full md:w-2/5 mx-4">
        <h2 class="text-3xl font-extrabold mb-6">Register</h2>

        <?php if ($error !== "") { ?>
            <div class="flex items-start mb-6 text-sm font-bold text-red-500">
                <?php echo $error ?>
            </div>
        <?php } ?>

        <form action="/register.php" method="post">
            <div class="mb-6">
                <label for="username" class="block mb-2 text-sm font-medium text-gray-900">Username</label>
                <input type="username" id="username" name="username" , class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="mario.rossi" required>
            </div>
            <div class="mb-6">
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email address</label>
                <input type="email" id="email" name="email" , class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="mario.rossi@example.com" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                <input type="password" id="password" name="password" , class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="•••••••••" required>
            </div>
            <div class="mb-6">
                <label for="confirm_password" class="block mb-2 text-sm font-medium text-gray-900">Password confirm</label>
                <input type="password" id="confirm_password" name="confirm_password" , class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="•••••••••" required>
            </div>

            <div class="flex items-start mt-6">
                <span class="text-sm text-gray-900">Password must be at least 8 characters, and must contain at least an uppercase letter, a lowercase letter and a number.</span>
            </div>

            <button type="submit" class="mt-6 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full md:w-auto px-5 py-2.5 text-center">Register</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/html/footer.php' ?>