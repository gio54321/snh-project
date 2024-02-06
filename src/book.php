<?php
require_once __DIR__ . '/utils.php';

function try_download_file() {
    if (!is_logged_in()) {
        exit;
    }

    if (!isset($_POST["id"])) {
        exit;
    }

    $result = execute_query('SELECT * FROM owned_books WHERE user_id=:user_id AND book_id=:book_id', [
        'user_id' => $_SESSION['user_id'],
        'book_id' => $_POST["id"]
    ])->fetchAll();

    if (count($result) == 0) {
        exit;
    }

    $result = execute_query('SELECT file FROM books WHERE id=:book_id', [
        'book_id' => $_POST["id"]
    ])->fetch();

    if (!isset($result['file'])) {
        exit;
    }

    $file = $result['file'];
    if (file_exists($file)) {    
        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="downloaded.pdf"');
        readfile($file);
        exit;
    } else {
        exit;
    }
}

function prepare_book_page() {
    global $book_id;
    global $book;
    global $owned;

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
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try_download_file();
}

prepare_book_page();

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
            <form action="/book.php" method="post">
                <input type="hidden" name="id" value="<?php echo $book_id; ?>" />
                <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                    onclick="addToCart(<?php echo $book['id'] ?>);"
                >Add to cart</button>
                <?php if ($owned) { ?>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        Download
                    </button>
                <?php } ?>
            </form>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/html/footer.php';
?>