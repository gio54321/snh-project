<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Logging
{
    static $instance = null;
    static function instance()
    {
        if (self::$instance == null) {
            self::$instance = new Logger('logger');
            $lastRebootDate = exec("uptime -s");
            $lastRebootDate = str_replace(":", "-", $lastRebootDate);
            $lastRebootDate = str_replace(" ", "-", $lastRebootDate);
            self::$instance->pushHandler(new StreamHandler('/log/' . $lastRebootDate . '.log', Logger::INFO));
        }
        return self::$instance;
    }
}

function __log_unauth_data($other_data)
{
    if (!isset($other_data)) {
        $other_data = [];
    }

    return array_merge([
        "request" => $_SERVER['REQUEST_URI'],
        "client_ip" => get_client_ip()
    ], $other_data);
}

function __log_auth_data($other_data)
{
    if (!isset($other_data)) {
        $other_data = [];
    }

    return array_merge([
        "request" => $_SERVER['REQUEST_URI'],
        "client_ip" => get_client_ip(),
        "user_id" => $_SESSION['user_id'],
        "username" => $_SESSION['username']
    ], $other_data);
}

function log_error_auth($message, $other_data = null)
{
    $data = __log_auth_data($other_data);
    $log = Logging::instance();

    $log->error($message, $data);
}

function log_error_unauth($message, $other_data = null)
{
    $data = __log_unauth_data($other_data);
    $log = Logging::instance();

    $log->error($message, $data);
}

function log_error($message, $other_data = null)
{
    if (is_logged_in()) {
        log_error_auth($message, $other_data);
    } else {
        if (!isset($other_data)) {
            $other_data = [];
        }
        log_error_unauth($message, array_merge($other_data, ["user_id" => "(not authenticated)"]));
    }
}

function log_warning_auth($message, $other_data = null)
{
    $data = __log_auth_data($other_data);
    $log = Logging::instance();

    $log->warning($message, $data);
}

function log_warning_unauth($message, $other_data = null)
{
    $data = __log_unauth_data($other_data);
    $log = Logging::instance();

    $log->warning($message, $data);
}

function log_warning($message, $other_data = null)
{
    if (is_logged_in()) {
        log_warning_auth($message, $other_data);
    } else {
        if (!isset($other_data)) {
            $other_data = [];
        }
        log_warning_unauth($message, array_merge($other_data, ["user_id" => "(not authenticated)"]));
    }
}

function log_info_auth($message, $other_data = null)
{
    $data = __log_auth_data($other_data);
    $log = Logging::instance();

    $log->info($message, $data);
}

function log_info_unauth($message, $other_data = null)
{
    $data = __log_unauth_data($other_data);
    $log = Logging::instance();

    $log->info($message, $data);
}

function log_info($message, $other_data = null)
{
    if (is_logged_in()) {
        log_info_auth($message, $other_data);
    } else {
        if (!isset($other_data)) {
            $other_data = [];
        }
        log_info_unauth($message, array_merge($other_data, ["user_id" => "(not authenticated)"]));
    }
}
