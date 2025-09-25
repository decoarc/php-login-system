<?php
session_start();

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

// initialize the message variable
$message = "";

// if the request method is post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email']; // get the email from the post request
    $pass = $_POST['pass']; // get the password from the post request

    // prepare the sql statement
    $sql = "SELECT id, user, pass FROM users WHERE email=?"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if(password_verify($pass, $user['pass'])){ // verify the password (the password from the post request and the password from the database)
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['user_name'] = $user['user']; 
            $stmt->close();
            $conn->close();
            header('Location: dashboard.php');
            exit;
        } else{
            $message = "Wrong password!";
        }
    } else{
        $message = "User not found!";
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
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <div class="container">
      <div class="card">
        <div class="card-header">
          <h2 class="card-title">Sign in</h2>
          <p class="card-subtitle">Access your account</p>
        </div>
        <div class="card-body">
          <?php if($message): ?>
            <p class="feedback error"><?php echo htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></p> 
          <?php endif; ?>
          <form method="POST" class="form">
            <input class="input" type="email" name="email" placeholder="E-mail" required />
            <input class="input" type="password" name="pass" placeholder="Password" required />
            <div class="actions">
              <button class="button" type="submit">Log In</button>
              <p class="helper">No account? <a class="link" href="register.php">Sign Up</a></p>
            </div>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
