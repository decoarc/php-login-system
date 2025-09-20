<?php
$config = parse_ini_file(__DIR__ . '/.env');

$host = $config['DB_HOST'];
$user = $config['DB_USER'];
$pass = $config['DB_PASS'];
$db   = $config['DB_NAME'];

$conn = new mysqli($host, $user, $pass, $db);

$email = $_POST['email'];
$pass = $_POST['pass'];

$sql = "SELECT id, user, pass FROM users WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if(password_verify($pass, $user['pass'])){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['user'];
        echo "Login has been sucessuful! Welcome, " . $user['user'] . "";
        echo "<br><a href='dashboard.php'>Go to Dashboard</a>";
    } else{
        echo "Wrong passworld!";
        echo "<br><a href='login.html'>Go back</a>";
    }
} else{
    echo "User not found!";
    echo "<br><a href='login.html'>Go back</a>";
}

$stmt->close();
$conn->close();
?>