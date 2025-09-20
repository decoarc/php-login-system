<?php

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
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>User</th><th>E-mail</th></tr>";
            while ($row = $result->fetch_assoc()){
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["user"] . "</td>";
                echo "<td>" . $row["email"] . "</td>";
                echo "<td>
                        <form action='del_user.php' method='POST' onsubmit=\"return confirm('Are you sure?');\">
                        <input type='hidden' name='id' value='" . $row["id"] . "'>
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