<?php
require_once __DIR__ . '/utils.php';

if (!isset($_GET['q']) || !is_string($_GET['q'])) {
    log_info("Search page query not set");

    header('Location: /');
    exit;
}


$books = execute_query('SELECT * FROM books WHERE LOWER(title) LIKE CONCAT("%", :q, "%")', ['q' => $_GET['q']])->fetchAll();
require_once __DIR__ . '/html/header.php';
?>

<div class="flex items-center justify-center mt-10 mb-10 text-xl font-semibold">
    Search results for "<?php echo htmlspecialchars($_GET['q']) ?>" (<?php echo count($books) ?> items)
</div>
<div class="flex items-center justify-center mt-10 mb-10">
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <?php foreach ($books as $book) { ?>
            <div class="w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow">
                <div class="grid place-items-center">
                    <a href="/book.php?id=<?php echo htmlspecialchars($book['id']) ?>">
                        <img class="p-8 rounded-t-lg h-96" src="<?php echo htmlspecialchars($book["image"]) ?>" alt="product image" />
                    </a>
                </div>
                <div class="px-5 pb-5">
                    <a href="/book.php?id=<?php echo htmlspecialchars($book['id']) ?>">
                        <h5 class="text-xl font-semibold tracking-tight text-gray-900">
                            <?php echo htmlspecialchars($book['title']) ?>
                        </h5>
                    </a>
                    <div class="flex items-center justify-between mt-4">
                        <span class="text-3xl text-gray-900">
                            <?php echo htmlspecialchars($book['price'] / 100) ?> €
                        </span>
                        <a href="#" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Add to cart</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php
require_once __DIR__ . '/html/footer.php';
?>