<?php

// restrict access to this file to only be accessed by including it
if (count(get_included_files()) == ((version_compare(PHP_VERSION, '5.0.0', '>=')) ? 1 : 0)) {
    die('Direct access not permitted');
}

use PHPMailer\PHPMailer\PHPMailer;

require 'vendor/autoload.php';

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
