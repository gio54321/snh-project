<?php
require_once __DIR__ . '/utils.php';


$books = execute_query('SELECT * FROM books')->fetchAll();

require_once __DIR__ . '/html/header.php';
?>

<div class="flex items-center justify-center mt-10 mb-10">
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <?php foreach ($books as $book) { ?>
            <div class="w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow">
                <a href="#">
                    <img class="p-8 rounded-t-lg" src="/static/the_great_gatsby.jpg" alt="product image" />
                </a>
                <div class="px-5 pb-5">
                    <a href="#">
                        <h5 class="text-xl font-semibold tracking-tight text-gray-900">
                            <?php echo $book['title'] ?>
                        </h5>
                    </a>
                    <div class="flex items-center justify-between">
                        <span class="text-3xl text-gray-900">
                            <?php echo $book['price'] / 100 ?> €
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