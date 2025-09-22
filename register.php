<?php

$config = parse_ini_file(__DIR__ . '/.env');

$host = $config['DB_HOST'];
$user = $config['DB_USER'];
$pass = $config['DB_PASS'];
$db   = $config['DB_NAME'];

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error){
    die("Connect Error: " . $conn->connect_error);
}


$response = ["status" => "", "message" => ""];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0){
        $response = ["status" => "error", "message" => "E-mail not available"];
    } else {
        $sql = $conn->prepare("INSERT INTO users (user, email, pass) VALUES (?, ?, ?)");
        $sql->bind_param("sss", $user, $email, $pass);

        if ($sql->execute()) {
            $response = ["status" => "success", "message" => "New user successfully registered"];
        } else {
            $response = ["status" => "error", "message" => "User can't be registered"];
        }
    }

    $check->close();
    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Register the User</title>
  </head>
  <body>
    <h2>Register</h2>
    <form id="registerForm" method="post">
      <input type="text" name="user" placeholder="User" required /><br />
      <input type="email" id="email" name="email" placeholder="E-mail" required />
      <span id="emailMsg"></span><br />
      <input type="password" id="pass" name="pass" placeholder="Password" required />
      <button type="button" id="togglePass" name="togglePass">👁️</button><br />
      <button type="submit">Register</button>
      <p id="formMsg"></p>
    </form>
  </body>

  <script>
    const passInput = document.getElementById("pass");
    const togglePass = document.getElementById("togglePass");

    togglePass.addEventListener("click", () => {
      if (passInput.type === "password") {
        passInput.type = "text";
        togglePass.textContent = "🙈";
      } else {
        passInput.type = "password";
        togglePass.textContent = "👁️";
      }
    });

    document.getElementById("email").addEventListener("blur", function () {
      let email = this.value;
      let msgSpan = document.getElementById("emailMsg");

      if (email.length === 0) {
        msgSpan.textContent = "";
        return;
      }

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

      xhr.onload = function () {
        if (xhr.status === 200) {
          let data = JSON.parse(xhr.responseText);
          if (data.status === "error" && data.message.includes("E-mail")) {
            msgSpan.textContent = " E-mail not available.";
            msgSpan.style.color = "red";
          } else {
            msgSpan.textContent = " E-mail available.";
            msgSpan.style.color = "green";
          }
        }
      };

      xhr.send("email=" + encodeURIComponent(email));
    });

    document
      .getElementById("registerForm")
      .addEventListener("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        fetch("", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            let msg = document.getElementById("formMsg");
            msg.textContent = data.message;
            msg.style.color = data.status === "success" ? "green" : "red";
          })
          .catch((error) => console.error("Erro:", error));
      });
  </script>
</html>
