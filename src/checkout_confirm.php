<?php
$checkout_procedure_page = 0;
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/utils/checkout_utils.php';

$error = "";
$checkout = Checkout::instance();

function checkout_confirmation()
{
    global $error;
    global $checkout;

    if (!is_logged_in()) {
        log_warning_unauth("Checkout confirm post request by unauthenticated user");

        header('Location: /', true, 403);
        exit;
    }

    if (!check_csrf_token()) {
        log_warning_auth("Checkout confirm post request invalid CSRF token");

        header('Location: /', true, 403);
        exit;
    }

    $result = execute_query('INSERT INTO orders (date, user_id, fullname, address, city, zipcode, country, phone_number)
    VALUES (:date, :user_id, :fullname, :address, :city, :zipcode, :country, :phone_number)', [
        'date' => $checkout->date,
        'user_id' => $checkout->user,
        'fullname' => $checkout->shipping->fullname,
        'address' => $checkout->shipping->address,
        'city' => $checkout->shipping->city,
        'zipcode' => $checkout->shipping->zipcode,
        'country' => $checkout->shipping->country,
        'phone_number' => $checkout->shipping->phone_number
    ]);

    if (!$result) {
        log_error_auth("Checkout confirm post request database error [0]");

        http_response_code(500);
        exit;
    }

    $order_id = execute_query('SELECT id from orders WHERE date=:date AND user_id=:user', [
        'date' => $checkout->date,
        'user' => $checkout->user
    ])->fetch();

    if (!$order_id) {
        log_error_auth("Checkout confirm post request database error [1]");

        http_response_code(500);
        exit;
    }

    foreach ($checkout->items as $item) {
        $result = execute_query('INSERT INTO order_details (order_id, book_id, quantity)
        VALUES (:order_id, :book_id, :quantity)', [
            'order_id' => $order_id['id'],
            'book_id' => $item->book_id,
            'quantity' => $item->quantity
        ]);

        if (!$result) {
            log_error_auth("Checkout confirm post request database error [2]");

            http_response_code(500);
            exit;
        }

        //the ON DUPLICATE KEY UPDATE should not insert the row if the pair is already present in the table
        //the assignment is mandatory but the database engine will probably optimize the query and skip the update entirely.
        //thus performing the same action as skipping the insertion if the row is already present
        //without ignoring any other kinds of errors.
        $result = execute_query('INSERT INTO owned_books (user_id, book_id)
        VALUES (:user_id, :book_id)
        ON DUPLICATE KEY UPDATE user_id=:user_id', [
            'user_id' => $checkout->user,
            'book_id' => $item->book_id
        ]);

        if (!$result) {
            log_error_auth("Checkout confirm post request database error [3]");

            http_response_code(500);
            exit;
        }
    }

    log_info_auth("Checkout confirm post request success");
    header('Location: /');
    exit;
}

$books = [];
$total = 0;
function prepare_confirm_page()
{
    global $books;
    global $total;
    global $checkout;

    if (!is_logged_in()) {
        log_warning_auth("Checkout confirm page request by unauthorized user");

        header('Location: /', true, 403);
        exit;
    }

    if (!check_checkout_next_step("checkout_confirm")) {
        log_warning_auth("Checkout confirm page request out of order");

        header('Location: /', true, 403);
        exit;
    }


    $all_books = execute_query('SELECT id, title, price, image FROM books')->fetchAll();
    if (!$all_books) {
        log_error_auth("Database books fetch error");

        http_response_code(500);
        exit;
    }

    foreach ($checkout->items as $item) {
        $key = array_search($item->book_id, array_column($all_books, "id"));
        $book_data = $all_books[$key];

        $book = [];
        $book['title'] = $book_data['title'];
        $book['image'] = $book_data['image'];
        $book['quantity'] = $item->quantity;
        $book['price'] = $book_data['price'];
        array_push($books, $book);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkout_confirmation();
}

prepare_confirm_page();

require_once __DIR__ . '/html/header.php';
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
        <li class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-100 after:border-4 after:inline-block dark:after:border-gray-700">
            <span class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-full lg:h-12 lg:w-12 dark:bg-gray-700 shrink-0">
                <svg class="w-4 h-4 text-gray-500 lg:w-5 lg:h-5 dark:text-gray-100" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                    <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2ZM7 2h4v3H7V2Zm5.7 8.289-3.975 3.857a1 1 0 0 1-1.393 0L5.3 12.182a1.002 1.002 0 1 1 1.4-1.436l1.328 1.289 3.28-3.181a1 1 0 1 1 1.392 1.435Z" />
                </svg>
            </span>
        </li>
        <li class="flex w-full items-center text-blue-600 dark:text-blue-500">
            <span class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full lg:h-12 lg:w-12 dark:bg-blue-800 shrink-0">
                <svg class="w-3.5 h-3.5 text-blue-600 lg:w-4 lg:h-4 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5" />
                </svg>
            </span>
        </li>
    </ol>

    <?php if ($error !== "") { ?>
        <div class="flex items-start mb-6 text-sm font-bold text-red-500">
            <?php echo $error ?>
        </div>
    <?php } ?>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Book
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <span class="sr-only">Book Name</span>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Qty
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Price
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book) { ?>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="p-4">
                            <img src="<?php echo $book['image'] ?>" class="w-16 md:w-24 max-w-full max-h-full rounded-lg" alt="product image">
                        </td>
                        <td class="px-6 py-4 w-80 font-semibold text-gray-900 dark:text-white">
                            <?php echo $book['title'] ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php echo $book['quantity'] ?>
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                            <?php $value = ($book['quantity'] * $book['price'] / 100);
                            $total += $value;
                            echo $value . " €"; ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="p-4"></td>
                    <td class="px-6 py-4 w-80 font-semibold text-gray-900 dark:text-white">
                        Total
                    </td>
                    <td class="px-6 py-4"></td>
                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                        <?php echo $total . " €" ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="p-8 border-2 border-gray-200 rounded-lg w-full md:w-2/5 mx-4 mt-10">
        <h2 class="text-3xl font-extrabold mb-6">Shipping Information</h2>
        <div class="mb-5">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Full Name</label>
            <label class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <?php echo $checkout->shipping->fullname ?>
            </label>
        </div>
        <div class="mb-5">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Shipping Address</label>
            <label class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <?php echo $checkout->shipping->address ?>
            </label>
        </div>
        <div class="mb-5">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">City</label>
            <label class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <?php echo $checkout->shipping->city ?>
            </label>
        </div>
        <div class="mb-5">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Zip Code</label>
            <label class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <?php echo $checkout->shipping->zipcode ?>
            </label>
        </div>
        <div class="mb-5">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Country</label>
            <label class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <?php echo $checkout->shipping->country ?>
            </label>
        </div>
        <div class="mb-5">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Phone Number</label>
            <label class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <?php echo $checkout->shipping->phone_number ?>
            </label>
        </div>
    </div>

    <div class="p-8 border-2 border-gray-200 rounded-lg w-full md:w-2/5 mx-4 mt-10">
        <h2 class="text-3xl font-extrabold mb-6">Payment Information</h2>
        <div class="mb-5">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Full Name</label>
            <label class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <?php echo $checkout->billing->card_owner ?>
            </label>
        </div>
        <div class="mb-5">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Card Number</label>
            <label class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <?php echo $checkout->billing->card_number ?>
            </label>
        </div>
        <div class="mb-5">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Expiry Date</label>
            <label class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <?php echo $checkout->billing->expiry_date ?>
            </label>
        </div>
    </div>

    <form class="max-w-sm mx-auto items-right mt-10" action="/checkout_confirm.php" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?>" />
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            Submit Order
        </button>
    </form>
</div>

<?php
require_once __DIR__ . '/html/footer.php';
?>