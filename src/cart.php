<?php
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/utils/checkout_utils.php';

$error = "";

function do_checkout()
{
    global $error;

    if (!is_logged_in()) {
        header('Location: /login.php', true, 401);
        exit;
    }

    $data = json_decode($_POST['items']);

    $checkout = Checkout::reset();
    foreach ($data as $pair) {
        $book_id = $pair[0];
        $quantity = $pair[1];
        $item = new CheckoutItem($book_id, $quantity);
        array_push($checkout->items, $item);
    }

    set_checkout_next_step("checkout_login");
    header('Location: /checkout_login.php');
    exit;

    //old code!
    /*$userid = $_SESSION['user_id'];
    if (!isset($userid)) {
        $error = "Invalid session user id";
        return;
    }

    $date = date('Y-m-d H:i:s');

    execute_query('DELETE FROM checkout_details WHERE user_id=:id', ['id' => $userid]);
    execute_query('DELETE FROM checkout WHERE user_id=:id', ['id' => $userid]);
    $result = execute_query('INSERT INTO checkout (user_id, date) VALUES (:user_id, :date)', [
        'date' => $date,
        'user_id' => $userid
    ]);

    if (!$result) {
        $error = "Error in creating the checkout order [0]";
        return;
    }

    foreach ($data as $pair) {
        $book_id = $pair[0];
        $quantity = $pair[1];
        $result = execute_query('INSERT INTO checkout_details (user_id, book_id, quantity) VALUES (:user_id, :book, :quantity)', [
            'user_id' => $userid,
            'book' => $book_id,
            'quantity' => $quantity
        ]);

        if (!$result) {
            $error = "Error in creating the checkout order [2]";
            execute_query('DELETE FROM checkout_details WHERE user_id=:id', ['id' => $userid]);
            execute_query('DELETE FROM checkout WHERE user_id=:id', ['id' => $userid]);
            return;
        }
    }

    set_checkout_next_step("checkout_login");
    header('Location: /checkout_login.php');
    exit;*/
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    do_checkout();
}

require_once __DIR__ . '/html/header.php';
?>

<div class="flex flex-col items-center justify-center mt-10 mb-10">
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
                    <th scope="col" class="px-6 py-3">
                        <span class="sr-only">Action</span>
                    </th>
                </tr>
            </thead>
            <tbody id="cart_table_body">
                <tr id="cart_table_example" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600" id="book_<?php echo $book["id"] ?>">
                    <td class="p-4">
                        <img id="image_example" src="" class="w-16 md:w-24 max-w-full max-h-full rounded-lg" alt="product image">
                    </td>
                    <td id="title_example" class="px-6 py-4 w-80 font-semibold text-gray-900 dark:text-white"></td>
                    <td class="px-6 py-4">
                        <div>
                            <input type="number" id="input_example"
                                class="bg-gray-50 w-14 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block px-2.5 py-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                value="" required min=0 max=10
                                oninput="updateItem(<?php echo $book['id'] ?>, <?php echo $book['price'] / 100 ?>);"
                            >
                        </div>
                    </td>
                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white" id="price_example"></td>
                    <td class="px-6 py-4">
                        <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                            id="remove_example"
                        >Remove</button>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="p-4"></td>
                    <td class="px-6 py-4 w-80 font-semibold text-gray-900 dark:text-white">
                        Total
                    </td>
                    <td class="px-6 py-4"></td>
                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white" id="total_price"></td>
                    <td class="px-6 py-4">
                        <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                            onclick="checkout();"
                        >Checkout</button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<script src="/static/cart_page.js"></script>

<?php
require_once __DIR__ . '/html/footer.php';
?>