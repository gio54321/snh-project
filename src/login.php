<?php

require_once __DIR__ . '/utils.php';

$error = "";

// lock the account after 3 unsuccessful login attempts
function handle_unsuccessful_login($user_id)
{
    global $error;

    $user = execute_query('SELECT * FROM users WHERE id=:user_id', [
        'user_id' => $user_id
    ])->fetch();

    $attempts = $user['unsuccessful_login_attempts'];
    $email = $user['email'];

    log_info_unauth("User attempted to log in " . strval($attempts) . " times.", [
        "user_id" => $user_id,
        "username" => $user['username']
    ]);

    if ($attempts === 2) {
        execute_query('UPDATE users SET unsuccessful_login_attempts = 0 WHERE id=:user_id', [
            'user_id' => $user_id
        ]);

        $unlock_token = bin2hex(random_bytes(32));
        execute_query('UPDATE users SET unlock_token = :unlock_token, locked = 1 WHERE id=:user_id', [
            'user_id' => $user_id,
            'unlock_token' => hash('sha256', $unlock_token)
        ]);

        $domain_name = $_ENV['DOMAIN_NAME'];
        send_mail(
            $email,
            "YASBS - Unlock your account",
            "Click <a href='http://$domain_name/unlock_account.php?token=$unlock_token'>here</a> to unlock your account"
        );

        log_warning_unauth("User locked for too many unsuccessful login attempts", [
            "user_id" => $user_id,
            "username" => $user['username']
        ]);

        $error = "Too many unsuccessful login attempts, check your email to unlock the account";
        return false;
    } else {
        execute_query('UPDATE users SET unsuccessful_login_attempts = unsuccessful_login_attempts + 1 WHERE id=:user_id', [
            'user_id' => $user_id
        ]);

        $error = "Invalid credentials";
        return true;
    }

    return true;
}

function do_login()
{
    global $error;

    if (
        !isset($_POST['username']) ||
        !isset($_POST['password'])
    ) {
        log_info_unauth("Missing fields in login request");
        $error = "Missing fields";
        return;
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    if (
        !is_string($username) ||
        !is_string($password)
    ) {
        log_info_unauth("Invalid fields in login request");
        $error = "Invalid fields";
        return;
    }

    $user = execute_query('SELECT * FROM users WHERE username = :username', [
        'username' => $username
    ])->fetch();

    if (!$user) {
        log_info_unauth("Invalid credentials in login request, user not found");

        $error = "Invalid credentials";
        return;
    }

    if (!$user['verified']) {
        log_info_unauth("Login requested from unverified account", [
            "user_id" => $user_id,
            "username" => $user['username']
        ]);

        $error = "Account not verified, please check your email";
        return;
    }

    if ($user['locked']) {
        log_warning_unauth("Login requested from locked account", [
            "user_id" => $user_id,
            "username" => $user['username']
        ]);

        $error = "Account locked, check your email to unlock it";
        return;
    }

    if (!password_verify($password, $user['password'])) {
        if (!handle_unsuccessful_login($user['id'])) {
            return;
        }
        
        log_info_unauth("Invalid credentials in login request, password doesn't match");

        $error = "Invalid credentials";
        return;
    }

    // create a new session
    session_reset();

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    log_info_auth("User logged in successfully.");

    header('Location: /');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    do_login();
}

if (is_logged_in()) {
    header('Location: /');
    exit;
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
                <label for="username" class="block mb-2 text-sm font-medium text-gray-900">email address</label>
                <input type="username" id="username" name="username" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="mario.rossi" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                <input type="password" id="password" name="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="•••••••••" required>
            </div>

            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full md:w-auto px-5 py-2.5 text-center">Login</button>

            <div class="flex items-start mt-6">
                <span class="text-sm font-semibold text-gray-900"> <a href="/recover_account.php" class="text-blue-600 hover:underline">I forgot the password</a></label>
            </div>

            <div class="flex items-start mt-6">
                <span class="text-sm font-semibold text-gray-900">Do not have an account? <a href="/register.php" class="text-blue-600 hover:underline">Register</a></label>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/html/footer.php' ?>