
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'smart_student_db';
$port = (int)(getenv('DB_PORT') ?: 4000);

// Debian system CA certificates
$caPath = '/etc/ssl/certs/ca-certificates.crt';

mysqli_report(MYSQLI_REPORT_OFF);

$conn = mysqli_init();
$conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 15);

// Enable TLS and CA verification
$conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);

if (!$conn->ssl_set(null, null, $caPath, null, null)) {
    error_log('Could not configure database TLS');
    http_response_code(500);
    die('Database TLS configuration failed.');
}

if (!$conn->real_connect(
    $host,
    $username,
    $password,
    $database,
    $port,
    null,
    MYSQLI_CLIENT_SSL
)) {
    error_log('Database connection failed: ' . $conn->connect_error);
    http_response_code(500);
    die('Secure database connection failed. Check database configuration.');
}

$conn->set_charset('utf8mb4');

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function is_logged_in() {
    return isset($_SESSION['admin_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
