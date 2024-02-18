<?php

// Start session.
session_start();

switch ($_SERVER["SERVER_NAME"]) {
    case "dev.plat4minc.com": // Development environment.
        define("DB_HOST", "database-1.ccoqixglkw7l.us-east-2.rds.amazonaws.com");
        define("DB_PORT", "3306");
        define("DB_USERNAME", "awg825y6_plat4m");
        define("DB_PASSWORD", "Plat4minc");
        define("DB_NAME", "awg825y6_plat4m");
        break;
    case "mystore.plat4minc.com": // Production environment.
        define("DB_HOST", "plat4minc.cnf2nhyetoks.us-west-1.rds.amazonaws.com");
        define("DB_PORT", "3306");
        define("DB_USERNAME", "plat4minc");
        define("DB_PASSWORD", "ZzFF2jaWzBgrHfKU");
        define("DB_NAME", "awg825y6_plat4m");
        break;
    default: // Local environment.
        define("DB_HOST", "localhost"); // Host.
        define("DB_PORT", "3306"); // Port.3306
        define("DB_USERNAME", "awg825y6_plat4m"); // Username.-:awg825y6_plat4m
        define("DB_PASSWORD", "He@ding@t4OOkmph"); // Password.-:He@ding@t4OOkmph
        define("DB_NAME", "awg825y6_plat4m"); // DB name
}

// Order status constants.
define("ORDER_COMPLETED", "Complete");
define("ORDER_IN_PROCESSING", "In-Processing");
define("ORDER_CANCELLED", "Cancel");

// Allowed image extensions.
define("ALLOWED_IMAGE_EXTENSIONS", [
    "gif",
    "GIF",
    "jpg",
    "JPG",
    "jpeg",
    "JPEG",
    "png",
    "PNG"
]);

/**
 * Creates DB connection.
 * @param string $host DB host.
 * @param string $port DB port.
 * @param string $username DB username.
 * @param string $password DB password.
 * @param string $dbName DB name.
 * @return object PDO object.
 */
function createDBConn($host, $port, $username, $password, $dbName)
{
    $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];
    return (new PDO($dsn, $username, $password, $options));
}

/**
 * Returns DB connection.
 * @return object PDO object.
 */
function getDBConn()
{
    static $conn = NULL;

    if ($conn === NULL) {
        $conn = createDBConn(DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD, DB_NAME);
    }

    return $conn;
}

$db = getDBConn();

// TODO: Remove this global connection.
// Make DB connection.
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Stop if connection fails.
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

switch ($_SERVER["SERVER_NAME"]) {
    case "dev.plat4minc.com": // Development environment.
        $serveradd = 'https://' . $_SERVER['SERVER_NAME'] . '/app_dashboard/JSON/';
        $serverimg = 'https://' . $_SERVER['SERVER_NAME'] . '/';
        break;
    case "mystore.plat4minc.com": // Production environment.
        $serveradd = 'https://' . $_SERVER['SERVER_NAME'] . '/app_dashboard/JSON/';
        $serverimg = 'https://' . $_SERVER['SERVER_NAME'] . '/';
        break;
    default: // Local environment.
        $serveradd = 'http://' . $_SERVER['SERVER_NAME'] . '/app_dashboard/JSON/';
        $serverimg = 'http://' . $_SERVER['SERVER_NAME'] . '/';
}

// $serveradd = 'http://' . $_SERVER['SERVER_NAME'] . '/app_dashboard/JSON/';
// $serverimg = 'http://' . $_SERVER['SERVER_NAME'] . '/';
$defimg = $serverimg . 'uploads/default-image/defaultimage.png';

function db()
{
    static $conn;

    if ($conn === NULL) {
        $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    }

    return $conn;
}

function debugLog($message)
{
    $date = date("ymd");
    $file = "/var/log/plat4m/logs/portal-{$date}.log";

    if (is_array($message)) {
        $message = json_encode($message);
    }

    file_put_contents($file, $message . "\n\n", FILE_APPEND | LOCK_EX);
}
