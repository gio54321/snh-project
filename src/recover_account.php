<?php

require_once __DIR__ . '/utils.php';

$error = "";

function do_recover_account()
{
    global $error;

    if (
        !isset($_POST['email'])
    ) {
        $error = "Missing fields";
        return;
    }

    $email = $_POST['email'];

    if (
        !is_string($email) ||
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        $error = "Invalid fields";
        return;
    }

    $user = execute_query('SELECT * FROM users WHERE email = :email', [
        'email' => $email
    ])->fetch();

    if (!$user) {
        // return silently
        return;
    }

    $token = bin2hex(random_bytes(32));

    $domain_name = $_ENV['DOMAIN_NAME'];
    send_mail(
        $email,
        "YASBS - Recover your account",
        "Click <a href=\"http://$domain_name/reset_password.php?token=$token\">here</a> to recover your account"
    );

    execute_query('UPDATE users SET reset_password_token = :token WHERE id = :id', [
        'token' => hash('sha256', $token),
        'id' => $user['id']
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    do_recover_account();
}

require_once __DIR__ . '/html/header.php';
?>

<div class="h-screen flex items-center justify-center pb-32">

    <div class="p-8 border-2 border-gray-200 border-dashed rounded-lg w-full md:w-2/5 mx-4">
        <h2 class="text-3xl font-extrabold mb-6">Recover account</h2>

        <?php if ($error !== "") { ?>
            <div class="flex items-start mb-6 text-sm font-bold text-red-500">
                <?php echo $error ?>
            </div>
        <?php } ?>

        <form action="/recover_account.php" method="post">
            <div class="mb-6">
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email address</label>
                <input type="email" id="email" name="email" , class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="mario.rossi@example.com" required>
            </div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full md:w-auto px-5 py-2.5 text-center">Recover</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/html/footer.php' ?>