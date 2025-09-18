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

$user = $_POST['user'];
$email = $_POST['email'];
$pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (user, email, pass) VALUES ('$user','$email', '$pass')";

if ($conn->query($sql) === TRUE){
    echo "user registered successfully";
    echo "<br><a href='register.html'>Back</a>";
} else {
    echo "Error: " .$conn->error;
}

$conn->close();

?>