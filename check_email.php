<?php
header('Content-Type: application/json');

// if the request method is not post, return an error
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// get the email from the post request
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
// if the email is empty or the email is not a valid email, return an error
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid email']);
    exit;
}

// read the .env file
$config = parse_ini_file(__DIR__ . '/.env');
// get the values to connect to the database

$host = $config['DB_HOST'];
$user = $config['DB_USER'];
$pass = $config['DB_PASS'];
$db   = $config['DB_NAME'];

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error){ // if the connection fails, return an error
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection error']);
    exit;
}

// prepare the sql statement
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?"); // check if the email is already in the database
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

$isTaken = $stmt->num_rows > 0;

$stmt->close();
$conn->close();

echo json_encode(['status' => 'ok', 'available' => !$isTaken]);
exit;
?>