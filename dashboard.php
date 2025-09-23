<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <div class="container">
      <div class="card">
        <div class="card-header">
          <h2 class="card-title">Welcome, <?php echo htmlspecialchars($_SESSION['user_name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>!</h2>
          <p class="card-subtitle">What would you like to do?</p>
        </div>
        <div class="card-body">
          <div class="actions">
            <a class="button" href="users.php">View users</a>
            <a class="button secondary" href="register.php">Create user</a>
            <a class="button danger" href="logout.php">Logout</a>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
