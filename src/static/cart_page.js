function buildTable() {
    var table_elem = document.getElementById('cart_table_example').cloneNode(true);
    var table = document.getElementById('cart_table_body');
    if (table == null) {
        console.log("table not found");
        return;
    }
    
    if (table_elem == null) {
        console.log("table_elem not found");
        return;
    }

    table.innerHTML = null;
    var items = getItems();

    for (const [book_id, quantity] of items.entries()) {
        var new_node = makeNewNode(table_elem, book_id);
        table.innerHTML += new_node.outerHTML;

        __httpGetBookData(book_id,
            (json) => {
                var price = Number(json['price']);

                var image_item = document.getElementById("image_" + book_id);
                image_item.src = json['image'];
                var title_item = document.getElementById("title_" + book_id);
                title_item.innerHTML = json['title'];
                var input_item = document.getElementById("input_" + book_id);
                input_item.value = quantity;
                input_item.onchange = () => updateItem(book_id, price);
                var price_item = document.getElementById("price_" + book_id);
                price_item.innerHTML = (price * quantity).toLocaleString(undefined, { minimumFractionDigits: 2 }) + " €";
                var remove_item = document.getElementById("remove_" + book_id);
                remove_item.onclick = () => removeFromCart(book_id);

                updateTotal();
            }
        );
    }
}

function makeNewNode(table_elem, book_id) {
    var new_node = table_elem.cloneNode(true);
    new_node.id = "book_" + book_id;
    renameChildren(new_node, "image_example", "image_" + book_id);
    renameChildren(new_node, "title_example", "title_" + book_id);
    renameChildren(new_node, "input_example", "input_" + book_id);
    renameChildren(new_node, "price_example", "price_" + book_id);
    renameChildren(new_node, "remove_example", "remove_" + book_id);
    return new_node;
}

function renameChildren(node, child_id, new_child_id) {
    for (let i = 0; i < node.childNodes.length; i++) {
        let child = node.childNodes[i];
        if (child.id == child_id)
            child.id = new_child_id;
        else
            renameChildren(child, child_id, new_child_id);
    }      
}

function __httpGetBookData(book_id, callback)
{
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open( "GET", "/get_book_info.php?id=" + book_id, true);
    xmlHttp.send( null );
    xmlHttp.onreadystatechange = function() { 
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
            callback(JSON.parse(xmlHttp.responseText));
    }
}

function removeFromCart(book_id) {
    var book = document.getElementById("book_" + book_id);
    book.outerHTML = null;
    itemToCart(Number(book_id), 0);
    updateTotal();
}

function updateItem(book_id, price) {
    var quantity = Number(document.getElementById("input_" + book_id).value);
    var price_item = document.getElementById("price_" + book_id);
    price_item.innerHTML = (price * quantity).toLocaleString(undefined, { minimumFractionDigits: 2 }) + " €";
    itemToCart(Number(book_id), quantity);
    updateTotal();
}

function updateTotal() {
    var total = 0;
    var items = getItems();
    for (const [book_id, quantity] of items.entries()) {
        var price_str = document.getElementById("price_" + book_id).innerText;
        var price = Number(price_str.split(" ")[0]); 
        total += price;
    }

    var total_price_item = document.getElementById("total_price");
    total_price_item.innerHTML = (total).toLocaleString(undefined, { minimumFractionDigits: 2 }) + " €";   
}

function checkout() {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "/cart.php");
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = () => {
        if (xhr.readyState == 4 && xhr.status == 200) {
            window.location = xhr.responseURL;
        } else if (xhr.readyState == 4 && xhr.status == 401) {
            window.location = xhr.responseURL;
        } else {
            console.log(`Error: ${xhr.status}`);
        }
    };
    xhr.send("items=" + __getCart().toJson());
}

buildTable()