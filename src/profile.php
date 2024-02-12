<?php
require_once __DIR__ . '/utils.php';

if (!is_logged_in()) {
    log_warning_unauth("Profile page get request from anonymous user");

    header('Location: /', true, 200);
    exit;
}

$owned_books = execute_query(
    'SELECT * FROM books JOIN (SELECT * FROM owned_books where user_id=:user_id) AS T ON T.book_id=id',
    [
        "user_id" => $_SESSION['user_id']
    ]
)->fetchAll();

require_once __DIR__ . '/html/header.php';
?>

<div class="flex flex-col items-center justify-center mt-10 mb-10">
    <span class="font-bold text-2xl mb-4"> Profile </span>

    <div class="w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow pt-8">
        <div class="flex flex-col items-center pb-8">
            <img class="w-24 h-24 mb-3 rounded-full shadow-lg" src="/static/profile.webp" alt="Profile picture" />
            <h5 class="text-xl font-medium text-gray-900">
                <?php echo htmlspecialchars($_SESSION['username']) ?>
            </h5>
            <a href="/change_password.php">
                <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mt-4">Change password</button>
            </a>
        </div>
    </div>
    <span class="font-bold text-2xl mb-4 mt-16"> Owned books </span>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <?php foreach ($owned_books as $book) { ?>
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
                    <form action="/download_book.php" method="get" class="mt-5 text-right">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($book['id']) ?>" />
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                            Download
                        </button>
                    </form>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php
require_once __DIR__ . '/html/footer.php';
?>