<?php
require_once __DIR__ . '/utils.php';
global $book_id;
global $book;
global $owned;

if (!isset($_GET["id"])) {
    log_warning("Book page requested without book id");

    header('Location: /', true, 400);
    exit;
}

$book_id = $_GET["id"];
if (!is_numeric($book_id)) {
    log_warning("Book page requested with non numeric book id", [
        "book_id" => $_GET['id']
    ]);

    http_response_code(400);
    die('Invalid book id');
}

$books_matching = execute_query('SELECT * FROM `books` WHERE id=:id', ['id' => $book_id])->fetchAll();

if (count($books_matching) == 0) {
    log_warning("Book not found", [
        "book_id" => $book_id
    ]);

    header('Location: /', true, 404);
    exit;
}

$book = $books_matching[0];

$owned = false;
if (is_logged_in()) {
    $result = execute_query('SELECT * FROM owned_books WHERE user_id=:user_id AND book_id=:book_id', [
        'user_id' => $_SESSION['user_id'],
        'book_id' => $book_id
    ])->fetchAll();

    if (count($result) > 0) {
        $owned = true;
    }
}


require_once __DIR__ . '/html/header.php';
?>

<div class="flex items-center justify-center mt-10 mb-10">
    <div class="grid grid-cols-2 gap-4">
        <div class="grid place-items-center ml-32">
            <img style="height: 36rem;" class="p-8 rounded-t-lg" src="<?php echo htmlspecialchars($book["image"]) ?>" alt="product image" />
        </div>
        <div class="px-5 pt-8 mr-32">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900">
                <?php echo htmlspecialchars($book['title']) ?>
            </h2>

            <span class="text-3xl text-gray-900">
                <?php echo htmlspecialchars($book['price']) / 100 ?> €
            </span>

            <br>
            <div class="mt-8">
                <h5 class="text-xl font-semibold text-gray-800">Description</h5>
                <p class="text-justify"><?php echo htmlspecialchars($book['description']) ?></p>
            </div>
            <br>
            <form action="/download_book.php" method="get">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($book_id) ?>" />
                <?php if ($owned) { ?>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                        Download
                    </button>
                <?php } else { ?>
                    <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2" onclick="addToCart(<?php echo $book['id'] ?>);">Add to cart</button>
                <?php } ?>
            </form>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/html/footer.php';
?>