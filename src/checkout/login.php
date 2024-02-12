<?php
$checkout_procedure_page = 0;
require_once __DIR__ . '/../utils.php';
require_once __DIR__ . '/../utils/checkout_utils.php';

$error = "";
$username = "";

function checkout_login()
{
    global $error;

    if (!is_logged_in()) {
        log_warning_unauth("Checkout login post request from anonymous user");

        header('Location: /', true, 403);
        exit;
    }

    if (!check_csrf_token()) {
        log_warning_auth("Checkout login post request invalid CSRF token");

        header('Location: /', true, 403);
        exit;
    }

    $userid = $_SESSION['user_id'];

    if (!isset($_POST['password'])) {
        log_warning_auth("Checkout login post request missing password");

        $error = "Missing password";
        http_response_code(400);
        return;
    }

    $password = $_POST['password'];

    if (!is_string($password)) {
        log_info_auth("Checkout login post request invalid password");

        $error = "Invalid password";
        http_response_code(400);
        return;
    }

    $user = execute_query('SELECT * FROM users WHERE id = :userid', [
        'userid' => $userid
    ])->fetch();

    if (!$user) {
        log_warning_auth("Checkout login post request missing user");

        $error = "Invalid credentials";
        http_response_code(400);
        return;
    }

    if (!$user['verified']) {
        log_warning_auth("Checkout login post request accout not verified");

        $error = "Account not verified";
        http_response_code(400);
        return;
    }

    if (!password_verify($password, $user['password'])) {
        // TODO handle password verification error and locking logic
        log_warning_auth("Checkout login post request incorrect password");

        $error = "Invalid credentials";
        http_response_code(400);
        return;
    }

    log_info_auth("Checkout login post request success");

    set_checkout_next_step("checkout_shipping");
    header('Location: /checkout/shipping.php');
    exit;
}

function prepare_login_page()
{
    global $username;

    if (!is_logged_in()) {
        log_warning_unauth("Checkout login page requested from anonymous user");

        header('Location: /', true, 403);
        exit;
    }

    if (!check_checkout_next_step("checkout_login")) {
        log_warning_auth("Checkout login page requested out of order");

        $_SESSION['index_page_error'] = "error!";
        header('Location: /', true, 403);
        exit;
    }

    $username = $_SESSION['username'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkout_login();
}

prepare_login_page();

require_once __DIR__ . '/../html/header.php';
?>

<div class="flex flex-col items-center justify-center mt-10 mb-10">
    <ol class="flex items-center w-96 mb-10">
        <li class="flex w-full items-center text-blue-600 after:content-[''] after:w-full after:h-1 after:border-b after:border-blue-100 after:border-4 after:inline-block">
            <span class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full lg:h-12 lg:w-12 shrink-0">
                <svg class="w-3.5 h-3.5 text-blue-600 lg:w-4 lg:h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5" />
                </svg>
            </span>
        </li>
        <li class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-100 after:border-4 after:inline-block">
            <span class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-full lg:h-12 lg:w-12 shrink-0">
                <svg class="w-4 h-4 text-gray-500 lg:w-5 lg:h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                    <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2ZM7 2h4v3H7V2Zm5.7 8.289-3.975 3.857a1 1 0 0 1-1.393 0L5.3 12.182a1.002 1.002 0 1 1 1.4-1.436l1.328 1.289 3.28-3.181a1 1 0 1 1 1.392 1.435Z" />
                </svg>
            </span>
        </li>
        <li class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-100 after:border-4 after:inline-block">
            <span class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-full lg:h-12 lg:w-12 shrink-0">
                <svg class="w-4 h-4 text-gray-500 lg:w-5 lg:h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                    <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2ZM7 2h4v3H7V2Zm5.7 8.289-3.975 3.857a1 1 0 0 1-1.393 0L5.3 12.182a1.002 1.002 0 1 1 1.4-1.436l1.328 1.289 3.28-3.181a1 1 0 1 1 1.392 1.435Z" />
                </svg>
            </span>
        </li>
        <li class="flex items-center w-full">
            <span class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-full lg:h-12 lg:w-12 shrink-0">
                <svg class="w-4 h-4 text-gray-500 lg:w-5 lg:h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                    <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2ZM7 2h4v3H7V2Zm5.7 8.289-3.975 3.857a1 1 0 0 1-1.393 0L5.3 12.182a1.002 1.002 0 1 1 1.4-1.436l1.328 1.289 3.28-3.181a1 1 0 1 1 1.392 1.435Z" />
                </svg>
            </span>
        </li>
    </ol>
    <div class="p-8 border-2 border-gray-200 border-dashed rounded-lg w-full md:w-2/5 mx-4">
        <h2 class="text-3xl font-extrabold mb-6">Re-authenticate<?php if ($username !== "") {
                                                                    echo ': ' . htmlspecialchars($username);
                                                                } ?></h2>

        <?php if ($error !== "") { ?>
            <div class="flex items-start mb-6 text-sm font-bold text-red-500">
                <?php echo $error ?>
            </div>
        <?php } ?>

        <form action="/checkout/login.php" method="post">
            <div class="mb-6">
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                <input type="password" id="password" name="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="•••••••••" required>
            </div>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?>" />
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full md:w-auto px-5 py-2.5 text-center">Authenticate</button>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../html/footer.php';
?>