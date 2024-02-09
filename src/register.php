<?php

require_once __DIR__ . '/utils.php';

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
        $error = "Invalid fields";
        return;
    }

    if ($password !== $confirm_password) {
        $error = "Passwords do not match";
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email";
        return;
    }

    $domain_name = $_ENV['DOMAIN_NAME'];
    $verification_token = bin2hex(random_bytes(32));
    if (!send_mail(
        $email,
        "YASBS - Verify your account",
        "Click <a href=\"http://$domain_name/verify_account.php?token=$verification_token\">here< to verify your account"
    )) {
        // assume that if the email is not sent, the email is not valid
        $error = "Invalid email";
        return;
    }

    // TODO check password strength
    // TODO check if username or email already exists, very important for security!

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $result = execute_query('INSERT INTO users (username, email, password, verification_token) VALUES (:username, :email, :password, :verification_token)', [
        'username' => $username,
        'email' => $email,
        'password' => $hashed_password,
        'verification_token' => hash('sha256', $verification_token)
    ]);

    if (!$result) {
        $error = "Error in the registration";
        return;
    }

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
                <input type="username" id="username" name="username" , class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Mario" required>
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

            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full md:w-auto px-5 py-2.5 text-center">Register</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/html/footer.php' ?>