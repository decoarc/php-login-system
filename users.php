<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$config = parse_ini_file(__DIR__ . '/.env');

$host = $config['DB_HOST'];
$user = $config['DB_USER'];
$pass = $config['DB_PASS'];
$db   = $config['DB_NAME'];

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error){
    die("Connect Error");
}

$sql = "SELECT id, user, email FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>All Users</title>
        <style>
            table {
                border-collapse: collapse;
                width: 50%;
            }
            th, td{
                border: 1px solid #333;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #ddd;
            }
        </style>
    </head>
    <body>
        <h2>Users List</h2>
        <?php
        if ($result && $result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>User</th><th>E-mail</th><th>Actions</th></tr>";
            while ($row = $result->fetch_assoc()){
                $idEsc = (int)$row["id"];
                $userEsc = htmlspecialchars($row["user"], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $emailEsc = htmlspecialchars($row["email"], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                echo "<tr>";
                echo "<td>" . $idEsc . "</td>";
                echo "<td>" . $userEsc . "</td>";
                echo "<td>" . $emailEsc . "</td>";
                echo "<td>
                        <form action='del_user.php' method='POST' onsubmit=\"return confirm('Are you sure?');\">
                        <input type='hidden' name='id' value='" . $idEsc . "'>
                        <input type='hidden' name='csrf_token' value='" . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "'>
                        <button type='submit'>Delete</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No User registed";
        }

        $conn->close();
        ?>
    </body>
</html>