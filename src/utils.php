<?php
session_start();

// restrict access to this file to only be accessed by including it
if (count(get_included_files()) == ((version_compare(PHP_VERSION, '5.0.0', '>=')) ? 1 : 0)) {
    die('Direct access not permitted');
}

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
    //return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
}
