<?php
$checkout_procedure_page = 0;
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/utils/checkout_utils.php';

$error = "";

function checkout_shipping()
{
    global $error;

    if (!is_logged_in()) {
        header('Location: /', true, 401);
        exit;
    }
    
    if (!check_checkout_csrf_token()) {
        $_SESSION['index_page_error'] = "error 3!";
        header('Location: /');
        exit;
    }

    if (
        !isset($_POST['fullname']) ||
        !isset($_POST['address']) ||
        !isset($_POST['city']) ||
        !isset($_POST['zipcode']) ||
        !isset($_POST['country']) ||
        !isset($_POST['phone_number'])
    ) {
        $error = "Missing fields";
        return;
    }

    if (
        !is_string($_POST['fullname']) ||
        !is_string($_POST['address']) ||
        !is_string($_POST['city']) ||
        !is_string($_POST['zipcode']) ||
        !is_string($_POST['country']) ||
        !is_string($_POST['phone_number'])
    ) {
        $error = "Invalid fields";
        return;
    }

    $zipcode = intval($_POST['zipcode']);

    if ($zipcode == 0) {
        $error = "Invalid zipcode";
        return;
    }

    $shipping = new CheckoutShipping();
    $shipping->fullname = $_POST['fullname'];
    $shipping->address = $_POST['address'];
    $shipping->city = $_POST['city'];
    $shipping->zipcode = $zipcode;
    $shipping->country = $_POST['country'];
    $shipping->phone_number = $_POST['phone_number'];

    $checkout = Checkout::instance();
    $checkout->shipping = $shipping;

    set_checkout_next_step("checkout_payment");
    header('Location: /checkout_payment.php');
    exit;
}

function prepare_shipping_page() {    
    if (!is_logged_in()) {
        header('Location: /login.php');
        exit;
    }

    if (!check_checkout_next_step("checkout_shipping")) {
        $_SESSION['index_page_error'] = "error 2!";
        header('Location: /');
        exit;
    }

    update_checkout_csrf_token();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkout_shipping();
}

prepare_shipping_page();

require_once __DIR__ . '/html/header.php';
?>

<div class="flex flex-col items-center justify-center mt-10 mb-10">
    <ol class="flex items-center w-96 mb-10">
        <li class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-100 after:border-4 after:inline-block dark:after:border-gray-700">
            <span class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-full lg:h-12 lg:w-12 dark:bg-gray-700 shrink-0">
                <svg class="w-4 h-4 text-gray-500 lg:w-5 lg:h-5 dark:text-gray-100" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                    <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2ZM7 2h4v3H7V2Zm5.7 8.289-3.975 3.857a1 1 0 0 1-1.393 0L5.3 12.182a1.002 1.002 0 1 1 1.4-1.436l1.328 1.289 3.28-3.181a1 1 0 1 1 1.392 1.435Z"/>
                </svg>
            </span>
        </li>
        <li class="flex w-full items-center text-blue-600 dark:text-blue-500 after:content-[''] after:w-full after:h-1 after:border-b after:border-blue-100 after:border-4 after:inline-block dark:after:border-blue-800">
            <span class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full lg:h-12 lg:w-12 dark:bg-blue-800 shrink-0">
                <svg class="w-3.5 h-3.5 text-blue-600 lg:w-4 lg:h-4 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                </svg>
            </span>
        </li>
        <li class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-100 after:border-4 after:inline-block dark:after:border-gray-700">
            <span class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-full lg:h-12 lg:w-12 dark:bg-gray-700 shrink-0">
                <svg class="w-4 h-4 text-gray-500 lg:w-5 lg:h-5 dark:text-gray-100" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                    <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2ZM7 2h4v3H7V2Zm5.7 8.289-3.975 3.857a1 1 0 0 1-1.393 0L5.3 12.182a1.002 1.002 0 1 1 1.4-1.436l1.328 1.289 3.28-3.181a1 1 0 1 1 1.392 1.435Z"/>
                </svg>
            </span>
        </li>
        <li class="flex items-center w-full">
            <span class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-full lg:h-12 lg:w-12 dark:bg-gray-700 shrink-0">
                <svg class="w-4 h-4 text-gray-500 lg:w-5 lg:h-5 dark:text-gray-100" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                    <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2ZM7 2h4v3H7V2Zm5.7 8.289-3.975 3.857a1 1 0 0 1-1.393 0L5.3 12.182a1.002 1.002 0 1 1 1.4-1.436l1.328 1.289 3.28-3.181a1 1 0 1 1 1.392 1.435Z"/>
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
        <form class="max-w-sm mx-auto" action="/checkout_shipping.php" method="post">
            <h2 class="text-3xl font-extrabold mb-6">Shipping Information</h2>
            <div class="mb-5">
                <label for="fullname" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Full Name</label>
                <input type="fullname" id="fullname" name="fullname" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Mario Rossi" required>
            </div>
            <div class="mb-5">
                <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Shipping Address</label>
                <input type="address" id="address" name="address" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Via Garibaldi, 1" required>
            </div>
            <div class="mb-5">
                <label for="city" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">City</label>
                <input type="city" id="city" name="city" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Pisa" required>
            </div>
            <div class="mb-5">
                <label for="zipcode" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Zip Code</label>
                <input type="zipcode" id="zipcode" name="zipcode" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="56127" required>
            </div>
            <div class="mb-5">
                <label for="country" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Country</label>
                <input type="country" id="country" name="country" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Italy" required>
            </div>
            <div class="mb-5">
                <label for="phone_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Phone Number</label>
                <input type="phone_number" id="phone_number" name="phone_number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="+39 333 2415 780" required>
            </div>
            <input type="hidden" name="checkout_csrf_token" value="<?php echo get_checkout_csrf_token(); ?>" />
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Next Step: Payment Info
            </button>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/html/footer.php';
?>