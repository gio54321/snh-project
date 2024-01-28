<?php
require_once __DIR__ . '/utils.php';

if (!isset($_GET["id"])) {
    // no id, redirect to index
    header('Location: /');
    exit;
}


$book_id = $_GET["id"];
if (!is_numeric($book_id)) {
    die('Invalid book id');
}

$books_matching = execute_query('SELECT * FROM `books` WHERE id=:id', ['id' => $book_id])->fetchAll();

if (count($books_matching) == 0) {
    // no book found, redirect to index
    header('Location: /');
    exit;
}

$book = $books_matching[0];

require_once __DIR__ . '/html/header.php';
?>

<div class="flex items-center justify-center mt-10 mb-10">
    <div class="grid grid-cols-2 gap-4">
        <div class="grid place-items-center">
            <img style="height: 36rem;" class="p-8 rounded-t-lg" src="<?php echo $book["image"] ?>" alt="product image" />
        </div>
        <div class="px-5 pt-32">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900">
                <?php echo $book['title'] ?>
            </h2>

            <span class="text-3xl text-gray-900">
                <?php echo $book['price'] / 100 ?> €
            </span>

            <br>
            <div class="mt-16">
                <h5 class="text-xl font-semibold text-gray-800">Description</h5>
                <?php echo $book['description'] ?>
            </div>
            <br>
            <a href="#" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Add to cart</a>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/html/footer.php';
?>