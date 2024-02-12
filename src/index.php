<?php
require_once __DIR__ . '/utils.php';

$error = "";

if (isset($_SESSION['index_page_error'])) {
    $error = $_SESSION['index_page_error'];
    unset($_SESSION['index_page_error']);
}

$books = execute_query('SELECT * FROM books')->fetchAll();
require_once __DIR__ . '/html/header.php';
?>

<div class="flex flex-col items-center justify-center mt-10 mb-10">
    <?php if ($error !== "") { ?>
        <div class="flex items-start mb-6 text-sm font-bold text-red-500">
            <?php echo $error ?>
        </div>
    <?php } ?>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <?php foreach ($books as $book) { ?>
            <div class="w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow">
                <div class="grid place-items-center">
                    <a href="/book.php?id=<?php echo $book['id'] ?>">
                        <img class="p-8 rounded-t-lg h-96" src="<?php echo $book["image"] ?>" alt="product image" />
                    </a>
                </div>
                <div class="px-5 pb-5">
                    <a href="/book.php?id=<?php echo $book['id'] ?>">
                        <h5 class="text-xl font-semibold tracking-tight text-gray-900">
                            <?php echo $book['title'] ?>
                        </h5>
                    </a>
                    <div class="flex items-center justify-between mt-4">
                        <span class="text-3xl text-gray-900">
                            <?php echo $book['price'] / 100 ?> €
                        </span>
                        <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2" onclick="addToCart(<?php echo $book['id'] ?>);">Add to cart</button>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php
require_once __DIR__ . '/html/footer.php';
?>