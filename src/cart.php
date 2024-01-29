<?php
require_once __DIR__ . '/utils.php';

require_once __DIR__ . '/html/header.php';
?>

<div class="flex items-center justify-center mt-10 mb-10">        
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
                    <th scope="col" class="px-6 py-3">
                        <span class="sr-only">Action</span>
                    </th>
                </tr>
            </thead>
            <tbody id="cart_table_body">
                <tr id="cart_table_example" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600" id="book_<?php echo $book["id"] ?>">
                    <td class="p-4">
                        <img id="image_example" src="" class="w-16 md:w-24 max-w-full max-h-full rounded-lg" alt="product image">
                    </td>
                    <td id="title_example" class="px-6 py-4 w-80 font-semibold text-gray-900 dark:text-white"></td>
                    <td class="px-6 py-4">
                        <div>
                            <input type="number" id="input_example"
                                class="bg-gray-50 w-14 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block px-2.5 py-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                value="" required min=0 max=10
                                oninput="updateItem(<?php echo $book['id'] ?>, <?php echo $book['price'] / 100 ?>);"
                            >
                        </div>
                    </td>
                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white" id="price_example"></td>
                    <td class="px-6 py-4">
                        <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                            id="remove_example"
                        >Remove</button>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="p-4"></td>
                    <td class="px-6 py-4 w-80 font-semibold text-gray-900 dark:text-white">
                        Total
                    </td>
                    <td class="px-6 py-4"></td>
                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white" id="total_price"></td>
                    <td class="px-6 py-4">
                        <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                            onclick="checkout();"
                        >Checkout</button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<script src="/static/cart_page.js"></script>

<?php
require_once __DIR__ . '/html/footer.php';
?>