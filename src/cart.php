<?php
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/utils/checkout_utils.php';

function do_checkout()
{
    if (!is_logged_in()) {
        log_info_unauth("Checkout from anonymous user, redirect to login.");

        header('Location: /login.php', true, 200);
        exit;
    }

    if (!check_csrf_token()) {
        log_warning_auth("Checkout post request invalid CSRF token");

        header('Location: /', true, 200);
        exit;
    }

    $data = json_decode($_POST['items']);
    if ($data === null || !is_array($data)) {
        log_warning_auth("Checkout data decode error [1]", [
            "data" => $data
        ]);

        http_response_code(400);
        exit;
    }

    foreach ($data as $pair) {
        if (
            !isset($pair[0]) || !isset($pair[1]) ||
            !is_numeric($pair[0]) || !is_numeric($pair[0])
        ) {
            log_warning_auth("Checkout data decode error [2]", [
                "data" => $data
            ]);

            http_response_code(400);
            exit;
        }
    }

    $checkout = Checkout::reset();
    foreach ($data as $pair) {
        $book_id = $pair[0];
        $quantity = $pair[1];
        $item = new CheckoutItem($book_id, $quantity);
        array_push($checkout->items, $item);
    }

    log_info_auth("Checkout request success");

    set_checkout_next_step("checkout_login");
    header('Location: /checkout/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    do_checkout();
}

require_once __DIR__ . '/html/header.php';
?>

<script src="/static/cart_page.js"></script>
<div class="flex flex-col items-center justify-center mt-10 mb-10">
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
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
                    <th scope="col" class="px-6 py-3">
                        <span class="sr-only">Action</span>
                    </th>
                </tr>
            </thead>
            <tbody id="cart_table_body">
                <tr id="cart_table_example" class="bg-white border-b" id="book_<?php echo htmlspecialchars($book["id"]) ?>">
                    <td class="p-4">
                        <img id="image_example" src="" class="w-16 md:w-24 max-w-full max-h-full rounded-lg" alt="product image">
                    </td>
                    <td id="title_example" class="px-6 py-4 w-80 font-semibold text-gray-900">product title</td>
                    <td class="px-6 py-4">
                        <input type="number" id="input_example" class="bg-gray-50 w-14 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block px-2.5 py-1" value="" required min=0 max=10 oninput="updateItem(<?php echo htmlspecialchars($book['id']) ?>, <?php echo htmlspecialchars($book['price'] / 100) ?>);">
                    </td>
                    <td class="px-6 py-4 font-semibold text-gray-900" id="price_example">10</td>
                    <td class="px-6 py-4">
                        <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 focus:outline-none" id="remove_example">Remove</button>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="bg-white border-b">
                    <td class="p-4"></td>
                    <td class="px-6 py-4 w-80 font-semibold text-gray-900">
                        Total
                    </td>
                    <td class="px-6 py-4"></td>
                    <td class="px-6 py-4 font-semibold text-gray-900" id="total_price"></td>
                    <td class="px-6 py-4">
                        <?php if (is_logged_in()) { ?>
                            <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
                        <?php } ?>
                        <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 focus:outline-none " onclick="checkout();">Checkout</button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<script type="text/javascript">
    buildTable();
</script>

<?php
require_once __DIR__ . '/html/footer.php';
?>