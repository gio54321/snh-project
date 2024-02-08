class Cart {
    constructor(string) {
        this.items = new Map();
        if (string != null) {
            var parsed = JSON.parse(string);
            if (parsed != null) {
                this.items = new Map(parsed);
            }
        }
    }

    toJson() {
        return JSON.stringify([...this.items.entries()]);
    }

    addItem(item_id, quantity) {
        if (!this.items.has(item_id)) {
            this.items.set(item_id, Math.min(quantity, 10));
        } else {
            var old_quantity = this.items.get(item_id);
            this.items.set(item_id, Math.min(quantity + old_quantity, 10));
        }
    }

    removeItem(item_id, quantity) {
        if (!this.items.has(item_id)) {
            var new_amount = Math.max(this.items.get(item_id) - quantity, 0);
            if (new_amount > 0) {
                this.items.set(item_id, new_amount);
            } else {
                this.items.delete(item_id);
            }
        }
    }

    removeAllOfItem(item_id) {
        this.items.delete(item_id);
    }

    setItem(item_id, quantity) {
        if (quantity > 0) {
            this.items.set(item_id, Math.max(quantity, 10));
        } else {
            this.items.delete(item_id);
        }
    }
}

function __getCart() {
    return new Cart(localStorage.getItem("cart"));
}

function __setCart(cart) {
    localStorage.setItem("cart", cart.toJson());
}

function itemToCart(item_id, quantity) {
    var cart = __getCart();
    cart.setItem(item_id, quantity);
    __setCart(cart);
}

function addToCart(item_id) {
    var cart = __getCart();
    cart.addItem(item_id, 1);
    __setCart(cart);

    showAlert("Item added successfully to cart!");
    hideAlertTimed(3000);
}

function clearCart() {
    var cart = __getCart();
    cart.items = new Map();
    __setCart(cart);
}

function getItems() {
    return __getCart().items
}