<?php

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
session_start();

date_default_timezone_set('UTC');

//if the variable '$checkout_procedure_page' is not set (any value is fine), forcefully unset the checkout csrf token
//if this happens this means that the user switched/requested another page in the middle of the checkout request
//this should hopefully prevent unwanted page switching in the middle of the checkout procedure
require_once __DIR__ . '/utils/checkout_reset.php';

// restrict access to this file to only be accessed by including it
if (count(get_included_files()) == ((version_compare(PHP_VERSION, '5.0.0', '>=')) ? 1 : 0)) {
    die('Direct access not permitted');
}

use PHPMailer\PHPMailer\PHPMailer;

require 'vendor/autoload.php';
require_once __DIR__ . '/utils/logger.php';

// set some security headers
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// singleton class for database connection
class Database
{
    static $instance = null;
    static function instance()
    {
        if (self::$instance == null) {
            $user = getenv('MYSQL_USER');
            $pass = getenv('MYSQL_PASSWORD');
            self::$instance = new PDO('mysql:host=db;dbname=yasbs', $user, $pass);
        }
        return self::$instance;
    }
}

function execute_query($query, $params = [])
{
    $stmt = Database::instance()->prepare($query);
    $stmt->execute($params);
    return $stmt;
}

function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

function check_csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    if (!isset($_POST['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

// returns true if the email has been sent successfully
function send_mail($to, $subject, $message)
{
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = "apikey";
    $mail->Password = $_ENV['SENDGRID_API_KEY'];
    $mail->Host = "smtp.sendgrid.net";
    $mail->SMTPSecure = 'tls';
    $mail->From = $_ENV['SENDGRID_EMAIL'];
    $mail->FromName = 'YASBS';
    $mail->AddAddress($to);  // Add a recipient
    $mail->isHTML(true);
    $mail->Body    = $message;
    $mail->Subject = $subject;
    return $mail->Send();
}

//https://stackoverflow.com/questions/15699101/get-the-client-ip-address-using-php
function get_client_ip()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
