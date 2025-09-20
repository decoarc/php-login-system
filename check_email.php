<?php
$config = parse_ini_file(__DIR__ . '/.env');

$host = $config['DB_HOST'];
$user = $config['DB_USER'];
$pass = $config['DB_PASS'];
$db   = $config['DB_NAME'];

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error){
    die("Connect Error" . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt -> bind_param("s", $email);
    $stmt -> execute();
    $result = $stmt -> get_result();

    echo ($result->num_rows > 0) ? "exists" : "ok";

    $stmt -> close();
}

$conn -> close();

?>