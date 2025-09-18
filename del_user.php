<?php
$config = parse_ini_file(__DIR__ . '/.env');

$host = $config['DB_HOST'];
$user = $config['DB_USER'];
$pass = $config['DB_PASS'];
$db   = $config['DB_NAME'];

$conn = new mysqli($host, $user, $pass, $db);

$id = $_POST["id"];

$sql = "DELETE FROM users WHERE id = $id";
if ($conn->query($sql) ===TRUE) {
    echo "user deleted successfully";
} else{
    echo "Error: " . $conn->error; 
}

$conn->close();

header("Location: users.php");
exit;
?>