<?php
require_once __DIR__ . '/utils.php';

function try_download_file() {
    if (!is_logged_in()) {
        log_warning_unauth("Unauthenticated download request");
        
        http_response_code(403);
        exit;
    }

    if (!isset($_POST["id"])) {
        log_warning_auth("No book id provided for download request");

        http_response_code(400);
        exit;
    }

    $result = execute_query('SELECT * FROM owned_books WHERE user_id=:user_id AND book_id=:book_id', [
        "user_id" => $_SESSION['user_id'],
        "username" => $_SESSION['username'],
        "book_id" => $_POST["id"]
    ])->fetchAll();

    if (count($result) == 0) {
        log_warning_auth("Unauthorized book download request", [
            "book_id" => $_POST["id"]
        ]);
        
        http_response_code(403);
        exit;
    }

    $result = execute_query('SELECT file FROM books WHERE id=:book_id', [
        'book_id' => $_POST["id"]
    ])->fetch();

    if (!isset($result['file'])) {
        log_error_auth("Book file not found", [
            'book_id' => $_POST["id"]
        ]);
        
        http_response_code(500);
        exit;
    }

    $file = $result['file'];
    if (file_exists($file)) {
        log_info_auth("Book downloaded", [
            "book_id" => $_POST["id"]
        ]);

        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="downloaded.pdf"');
        readfile($file);
        exit;
    } else {
        log_error_auth("Book file does not exist", [
            'book_id' => $_POST["id"],
            'file' => $file
        ]);

        http_response_code(500);
        exit;
    }
}

function prepare_book_page() {
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