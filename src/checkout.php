<?php
require_once __DIR__ . '/utils.php';

$error = "";

function do_checkout()
{
    global $error;

    if (!is_logged_in()) {
        header('Location: /login.php', true, 401);
        exit;
    }

    #get items from json
    $data = json_decode($_POST['items']);

    $userid = $_SESSION['user_id'];
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

    header('Location: /checkout.php', true, 200);
    exit;
}

$date = "";
$cart = [];
$total = 0;
function prepare_checkout_page() {
    global $date;
    global $cart;
    
    if (!is_logged_in()) {
        header('Location: /login.php');
        exit;
    }

    $userid = $_SESSION['user_id'];
    if (!isset($userid)) {
        $error = "Invalid session user id";
        return;
    }

    $date = execute_query('SELECT date FROM checkout WHERE user_id=:user_id', [
        'user_id' => $userid
    ])->fetch();

    if(!$date) {
        $error = "Invalid cart [0]";
        return;
    }

    $cart = execute_query(
    'SELECT books.id, checkout_details.quantity, books.title, books.price, books.image
    FROM checkout_details
    INNER JOIN books ON books.id=checkout_details.book_id
    WHERE user_id=:user_id', [
        'user_id' => $userid
    ])->fetchAll();

    if(!$cart) {
        $error = "Invalid cart [1]";
        return;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    do_checkout();
} else {
    prepare_checkout_page();
}

require_once __DIR__ . '/html/header.php';
?>

<?php if ($error !== "") { ?>
    <div class="flex items-start mb-6 text-sm font-bold text-red-500">
        <?php echo $error ?>
    </div>
<?php exit; }; ?>

<div class="flex flex-direction:column items-center justify-center mt-10 mb-10">        
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
                <?php foreach ($cart as $book) { ?>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600" id="book_<?php echo $book["id"] ?>">
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
                            <?php $value = ($book['quantity'] * $book['price'] / 100); $total += $value; echo $value . " €"; ?>
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
    <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Go back to cart</button>
    <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Go to Payment</button>
</div>

<?php
require_once __DIR__ . '/html/footer.php';
?>