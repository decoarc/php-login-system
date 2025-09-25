<?php
session_start();

// if the user is not logged in, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// if the request method is not post, redirect to the users page
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: users.php");
    exit;
}

// if the csrf token is not set or the csrf token is not equal to the session csrf token, redirect to the users page
if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    http_response_code(403);
    echo "Invalid CSRF token";
    exit;
}

// read the .env file
$config = parse_ini_file(__DIR__ . '/.env');
// get the values to connect to the database
$host = $config['DB_HOST'];
$user = $config['DB_USER'];
$pass = $config['DB_PASS'];
$db   = $config['DB_NAME'];

// connect to the database
$conn = new mysqli($host, $user, $pass, $db);

// if the connection fails, die
if ($conn->connect_error){
    die("Connect Error");
}

// get the id from the post request
$id = isset($_POST["id"]) ? (int)$_POST["id"] : 0;

// prepare the sql statement
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?"); // delete the user from the database
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

} else {

}

$stmt->close();
$conn->close();

header("Location: users.php");
exit;
?>