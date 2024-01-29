<?php
require_once __DIR__ . '/utils.php';

$book_id = $_GET["id"];
$quantity = $_GET["qty"];

if (!is_numeric($book_id)) {
    die('Invalid book id');
}

if (!is_numeric($quantity)) {
    die('Invalid book quantity');
}

$books_matching = execute_query('SELECT * FROM `books` WHERE id=:id', ['id' => $book_id])->fetchAll();

if (count($books_matching) == 0) {
    //no books found, send back nothing
    exit;
}

$book = $books_matching[0];
?>

<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600" id="book_<?php echo $book["id"] ?>">
    <td class="p-4">
        <img src="<?php echo $book["image"] ?>" class="w-16 md:w-24 max-w-full max-h-full rounded-lg" alt="product image">
    </td>
    <td class="px-6 py-4 w-80 font-semibold text-gray-900 dark:text-white">
        <?php echo $book['title'] ?>
    </td>
    <td class="px-6 py-4">
        <div>
            <input type="number" id="input_<?php echo $book['id'] ?>"
                class="bg-gray-50 w-14 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block px-2.5 py-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                value="<?php echo $quantity ?>" required min=0 max=10
                oninput="updateItem('<?php echo $book['id'] ?>');"
            >
        </div>
    </td>
    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
        <?php echo $book['price'] / 100 ?> €
    </td>
    <td class="px-6 py-4">
        <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
            onclick="removeFromCart('<?php echo $book['id'] ?>');"
        >Remove</button>
    </td>
</tr>