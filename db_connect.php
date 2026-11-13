<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
 * Local XAMPP defaults are kept for development.
 * On Render, configure all DB_* environment variables in the service dashboard.
 */
$isRender = getenv('RENDER') === 'true' || getenv('PORT') !== false;

$host = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$database = getenv('DB_NAME');
$port = getenv('DB_PORT');

if ($isRender && (!$host || !$username || !$database)) {
    http_response_code(500);
    error_log('Database configuration is incomplete. Set DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, and DB_PORT.');
    die('Server database configuration is incomplete. Please contact the administrator.');
}

$host = $host !== false && $host !== '' ? $host : 'localhost';
$username = $username !== false && $username !== '' ? $username : 'root';
$password = $password !== false ? $password : '';
$database = $database !== false && $database !== '' ? $database : 'smart_student_db';
$port = $port !== false && $port !== '' ? (int)$port : 3306;

mysqli_report(MYSQLI_REPORT_OFF);
$conn = mysqli_init();
$conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);

if (!$conn->real_connect($host, $username, $password, $database, $port)) {
    error_log('Database connection failed: ' . $conn->connect_error);
    http_response_code(500);
    die('Database connection failed. Check the DB_* environment variables and database availability.');
}

$conn->set_charset('utf8mb4');

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function is_logged_in()
{
    return isset($_SESSION['admin_id']);
}

function require_login()
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
