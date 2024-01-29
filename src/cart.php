<?php
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/html/header.php';
?>

<div class="flex items-center justify-center mt-10 mb-10">
        <!--button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
            onclick="alert(0);"
        >Checkout</button-->
        
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
                </tbody>
            </table>
        </div>
</div>
<script src="/static/cart_page.js"></script>

<?php
require_once __DIR__ . '/html/footer.php';
?>