<?php

if(!isset($checkout_procedure_page)) {
    unset($_SESSION['checkout_csrf_token']);
    unset($_SESSION['checkout_next_step']);
    unset($_SESSION['checkout_info']);
}

?>