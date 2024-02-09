<?php
$checkout_procedure_page = 0;
require_once __DIR__ . '/../utils.php';
require_once __DIR__ . '/../utils/checkout_utils.php';
require_once __DIR__ . '/../utils/validation.php';

$error = "";

function checkout_payment()
{
    global $error;

    if (!is_logged_in()) {
        log_warning_unauth("Checkout payment post request from anonymous user");

        header('Location: /', true, 403);
        exit;
    }

    if (!check_csrf_token()) {
        log_warning_auth("Checkout payment post request invalid CSRF token");

        header('Location: /', true, 403);
        exit;
    }

    if (
        !isset($_POST['fullname']) ||
        !isset($_POST['card_number']) ||
        !isset($_POST['date']) ||
        !isset($_POST['secret_code'])
    ) {
        log_info_auth("Checkout payment post request missing fields");

        $error = "Missing fields";
        http_response_code(400);
        return;
    }

    if (
        !is_string($_POST['fullname']) ||
        !is_string($_POST['card_number']) ||
        !is_string($_POST['date']) ||
        !is_string($_POST['secret_code'])
    ) {
        log_info_auth("Checkout payment post request invalid fields");

        $error = "Invalid fields";
        http_response_code(400);
        return;
    }

    if (!validate_card_number($_POST['card_number'])) {
        log_info_auth("Checkout payment post request card number");

        $error = "Invalid card number";
        http_response_code(400);
        return;
    }

    if (!validate_card_expiration($_POST['date'])) {
        log_info_auth("Checkout payment post request invalid expiration date");

        $error = "Invalid expiration date";
        http_response_code(400);
        return;
    }

    if (!validate_card_cvv($_POST['secret_code'])) {
        log_info_auth("Checkout payment post request invalid secret code");

        $error = "Invalid secret code";
        http_response_code(400);
        return;
    }

    $billing = new CheckoutBilling();
    $billing->card_owner = $_POST['fullname'];
    $billing->card_number = $_POST['card_number'];
    $billing->expiry_date = $_POST['date'];
    $billing->secret_code = $_POST['secret_code'];

    $checkout = Checkout::instance();
    $checkout->billing = $billing;

    log_info_auth("Checkout payment post request success");

    set_checkout_next_step("checkout_confirm");
    header('Location: /checkout/confirm.php');
    exit;
}

function prepare_payment_page()
{
    if (!is_logged_in()) {
        log_warning_auth("Checkout payment page request by unauthenticated user");

        header('Location: /', true, 403);
        exit;
    }

    if (!check_checkout_next_step("checkout_payment")) {
        log_warning_auth("Checkout payment page request out of order");

        header('Location: /', true, 403);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkout_payment();
}

prepare_payment_page();

require_once __DIR__ . '/../html/header.php';
?>

<div class="flex flex-col items-center justify-center mt-10 mb-10">
    <ol class="flex items-center w-96 mb-10">
        <li class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-100 after:border-4 after:inline-block dark:after:border-gray-700">
            <span class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-full lg:h-12 lg:w-12 dark:bg-gray-700 shrink-0">
                <svg class="w-4 h-4 text-gray-500 lg:w-5 lg:h-5 dark:text-gray-100" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                    <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2ZM7 2h4v3H7V2Zm5.7 8.289-3.975 3.857a1 1 0 0 1-1.393 0L5.3 12.182a1.002 1.002 0 1 1 1.4-1.436l1.328 1.289 3.28-3.181a1 1 0 1 1 1.392 1.435Z" />
                </svg>
            </span>
        </li>
        <li class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-100 after:border-4 after:inline-block dark:after:border-gray-700">
            <span class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-full lg:h-12 lg:w-12 dark:bg-gray-700 shrink-0">
                <svg class="w-4 h-4 text-gray-500 lg:w-5 lg:h-5 dark:text-gray-100" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                    <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2ZM7 2h4v3H7V2Zm5.7 8.289-3.975 3.857a1 1 0 0 1-1.393 0L5.3 12.182a1.002 1.002 0 1 1 1.4-1.436l1.328 1.289 3.28-3.181a1 1 0 1 1 1.392 1.435Z" />
                </svg>
            </span>
        </li>
        <li class="flex w-full items-center text-blue-600 dark:text-blue-500 after:content-[''] after:w-full after:h-1 after:border-b after:border-blue-100 after:border-4 after:inline-block dark:after:border-blue-800">
            <span class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full lg:h-12 lg:w-12 dark:bg-blue-800 shrink-0">
                <svg class="w-3.5 h-3.5 text-blue-600 lg:w-4 lg:h-4 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5" />
                </svg>
            </span>
        </li>
        <li class="flex items-center w-full">
            <span class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-full lg:h-12 lg:w-12 dark:bg-gray-700 shrink-0">
                <svg class="w-4 h-4 text-gray-500 lg:w-5 lg:h-5 dark:text-gray-100" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                    <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2ZM7 2h4v3H7V2Zm5.7 8.289-3.975 3.857a1 1 0 0 1-1.393 0L5.3 12.182a1.002 1.002 0 1 1 1.4-1.436l1.328 1.289 3.28-3.181a1 1 0 1 1 1.392 1.435Z" />
                </svg>
            </span>
        </li>
    </ol>

    <?php if ($error !== "") { ?>
        <div class="flex items-start mb-6 text-sm font-bold text-red-500">
            <?php echo $error ?>
        </div>
    <?php } ?>

    <div class="p-8 border-2 border-gray-200 border-dashed rounded-lg w-full md:w-2/5 mx-4">
        <form class="max-w-sm mx-auto" action="/checkout/payment.php" method="post">
            <h2 class="text-3xl font-extrabold mb-6">Payment Information</h2>
            <div class="mb-5">
                <label for="fullname" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Full Name</label>
                <input type="fullname" id="fullname" name="fullname" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Mario Rossi" required>
            </div>
            <div class="mb-5">
                <label for="card_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Card Number</label>
                <input type="card_number" id="card_number" name="card_number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="9000 6000 0000 0000" required>
            </div>
            <div class="mb-5">
                <label for="date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Expiration Date</label>
                <input type="expiry_date" id="date" name="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="03/28" required>
            </div>
            <div class="mb-5">
                <label for="secret_code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Secret Code</label>
                <input type="secret_code" id="secret_code" name="secret_code" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="•••" required>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Next Step: Confirmation
            </button>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../html/footer.php';
?>