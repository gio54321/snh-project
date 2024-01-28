<?php

require_once __DIR__ . '/utils.php';

$error = "";

function do_login()
{
    global $error;

    if (
        !isset($_POST['username']) ||
        !isset($_POST['password'])
    ) {
        $error = "Missing fields";
        return;
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    if (
        !is_string($username) ||
        !is_string($password)
    ) {
        $error = "Invalid fields";
        return;
    }
    // TODO check password strength

    $user = execute_query('SELECT * FROM users WHERE username = :username', [
        'username' => $username
    ])->fetch();

    if (!$user) {
        $error = "Invalid credentials";
        return;
    }

    if (!password_verify($password, $user['password'])) {
        // TODO handle password verification error and locking logic
        $error = "Invalid credentials";
        return;
    }

    // create a new session
    session_start();

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    header('Location: /');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    do_login();
}

require_once __DIR__ . '/html/header.php';
?>

<div class="h-screen flex items-center justify-center pb-32">

    <div class="p-8 border-2 border-gray-200 border-dashed rounded-lg w-full md:w-2/5 mx-4">
        <h2 class="text-3xl font-extrabold mb-6">Login</h2>

        <?php if ($error !== "") { ?>
            <div class="flex items-start mb-6 text-sm font-bold text-red-500">
                <?php echo $error ?>
            </div>
        <?php } ?>

        <form action="/login.php" method="post">
            <div class="mb-6">
                <label for="username" class="block mb-2 text-sm font-medium text-gray-900">Username</label>
                <input type="username" id="username" name="username" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="mario.rossi@example.com" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                <input type="password" id="password" name="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="•••••••••" required>
            </div>

            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full md:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Login</button>

            <div class="flex items-start mt-6">
                <span class="text-sm font-semibold text-gray-900"> <a href="/password-reset" class="text-blue-600 hover:underline">I forgot the password</a></label>
            </div>

            <div class="flex items-start mt-6">
                <span class="text-sm font-semibold text-gray-900">Do not have an account? <a href="/register.php" class="text-blue-600 hover:underline">Register</a></label>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/html/footer.php' ?>