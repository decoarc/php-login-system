<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

echo "<h2>Wellcome to Dashboard, " . $_SESSION['user_name'] . "!</h2>";
echo "<a href='logout.php'>Sair</a>";
?>
