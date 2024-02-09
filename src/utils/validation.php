<?php

function validate_password_strength($password)
{
    // must be at least 8 characters long
    if (strlen($password) < 8) {
        return false;
    }
    // must contain at least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }
    // must contain at least one lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }
    // must contain at least one digit
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }
    return true;
}

function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
