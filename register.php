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

$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0){
    echo json_encode(["status" => "error", "message" => "Este email já está em uso."]);
} else {
    $sql = $conn->prepare("INSERT INTO users (user, email, pass) VALUES (?, ?, ?)");
    $sql->bind_param("sss", $user, $email, $pass);

    if ($sql->execute()) {
        echo json_encode(["status" => "success", "message" => "Usuário registrado com sucesso!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Erro ao registrar usuário: " . $conn->error]);
    }

}

$conn->close();

?>