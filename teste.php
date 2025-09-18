<?php
$config = parse_ini_file(__DIR__ . '/.env');

$host = $config['DB_HOST'];
$user = $config['DB_USER'];
$pass = $config['DB_PASS'];
$db   = $config['DB_NAME'];

$conn = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("Error: " . $mysqli->connect_error);
}
echo "Connected to MySQL successfully!";
?>
