function buildTable() {
    var table = document.getElementById('cart_table_body');
    if (table == null) {
        console.log("table not found");
        return;
    }

    var items = getItems();

    for (const [book_id, quantity] of items.entries()) {
        __httpGetBookData(book_id, quantity,
            (html) => table.innerHTML += html
        );
    }
}

function __httpGetBookData(book_id, quantity, callback)
{
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open( "GET", "/cart_get_book_private.php?id=" + book_id + "&qty=" + quantity, true);
    xmlHttp.send( null );
    xmlHttp.onreadystatechange = function() { 
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
            callback(xmlHttp.responseText);
    }
}

function removeFromCart(book_id) {
    var book = document.getElementById("book_" + book_id);
    book.outerHTML = null;
    itemToCart(Number(book_id), 0);
}

function updateItem(book_id) {
    var quantity = Number(document.getElementById("input_" + book_id).value);
    itemToCart(Number(book_id), quantity);
}

buildTable()