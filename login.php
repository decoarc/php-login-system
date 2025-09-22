<?php
session_start();

$config = parse_ini_file(__DIR__ . '/.env');

$host = $config['DB_HOST'];
$user = $config['DB_USER'];
$pass = $config['DB_PASS'];
$db   = $config['DB_NAME'];

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error){
    die("Connect Error: " . $conn->connect_error);
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            $message = "Login successful! Welcome, " . $user['user'] . ". <a href='dashboard.php'>Go to Dashboard</a>";
        } else{
            $message = "Wrong password! <a href='login.php'>Go back</a>";
        }
    } else{
        $message = "User not found! <a href='login.php'>Go back</a>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Login</title>
  </head>
  <body>
    <h2>Login</h2>
    <?php if($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST">
      <input type="email" name="email" placeholder="E-mail" required /><br />
      <input type="password" name="pass" placeholder="Password" required /><br />
      <button type="submit">Log In</button>
    </form>
    <p>No account? <a href="register.php">Sign Up</a></p>
  </body>
</html>
