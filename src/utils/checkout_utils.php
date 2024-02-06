<?php

function update_checkout_csrf_token() {
    $_SESSION['checkout_csrf_token'] = bin2hex(random_bytes(32));
}

function check_checkout_csrf_token() {
    if (!isset($_POST['checkout_csrf_token']) ||
        $_POST['checkout_csrf_token'] !== $_SESSION['checkout_csrf_token']
    ) {
        unset($_SESSION['checkout_csrf_token']);
        return false;
    } else {
        unset($_SESSION['checkout_csrf_token']);
        return true;
    }
}

function unset_checkout_csrf_token() {
    unset($_SESSION['checkout_csrf_token']);
    unset($_SESSION['checkout_next_step']);
}

function get_checkout_csrf_token() {
    return $_SESSION['checkout_csrf_token'];
}

function set_checkout_next_step($step_string) {
    $_SESSION['checkout_next_step'] = $step_string;
}

function check_checkout_next_step($step_string) {
    if (!isset($_SESSION['checkout_next_step']) ||
        $_SESSION['checkout_next_step'] !== $step_string
    ) {
        return false;
    } else {
        return true;
    }
}

class Checkout {
    private $info = NULL;

    static $instance = null;
    static function instance()
    {
        if (self::$instance == null) {
            self::$instance = new Checkout();
        }
        return self::$instance->info;
    }

    static function reset() {
        unset($_SESSION['checkout_info']);
        self::$instance = null;
        return self::instance();
    }

    function __construct() {
        if (isset($_SESSION['checkout_info'])) {
            $this->info = unserialize($_SESSION['checkout_info']);
        } else {
            $this->info = new CheckoutInformation($_SESSION['user_id'], date('Y-m-d H:i:s'));
        }
    }

    function __destruct() {
        $_SESSION['checkout_info'] = serialize($this->info);
    }
}

function reset_checkout_information() {
    $_SESSION['checkout_info'] = NULL;
    return new CheckoutInformation($_SESSION['user_id'], date('Y-m-d H:i:s'));
}

function get_checkout_information() {
    return unserialize($_SESSION['checkout_info']);
}

function set_checkout_information($checkout_info) {
    $_SESSION['checkout_info'] = serialize($checkout_info);
}

class CheckoutItem {
    public $book_id;
    public $quantity;

    function __construct($book_id, $quantity) {
        $this->book_id = $book_id;
        $this->quantity = $quantity;
    }
}

class CheckoutShipping {
    public $fullname;
    public $address;
    public $city;
    public $zipcode;
    public $country;
    public $phone_number;

    function destroy() {
        $this->fullname = "";
        $this->address = "";
        $this->city = "";
        $this->zipcode = "";
        $this->country = "";
        $this->phone_number = "";
    }
}

class CheckoutBilling {
    public $card_owner;
    public $card_number;
    public $expiry_date;
    public $secret_code;

    function destroy() {
        $this->card_owner = "";
        $this->card_number = "";
        $this->expiry_date = "";
        $this->secret_code = "";
    }
}

class CheckoutInformation {
    public $user;
    public $date;
    public $items = [];
    public $shipping = NULL;
    public $billing = NULL;

    function __construct($user, $date) {
        $this->user = $user;
        $this->date = $date;
    }

    function __destruct() {
        if ($this->shipping !== NULL) {
            $this->shipping.destroy();
        }
        if ($this->billing !== NULL) {
            $this->billing.destroy();
        }
    }
}

?>