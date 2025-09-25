<?php

// read the .env file
$config = parse_ini_file(__DIR__ . '/.env');
// get the values to connect to the database

$host = $config['DB_HOST'];
$user = $config['DB_USER'];
$pass = $config['DB_PASS'];
$db   = $config['DB_NAME'];

// connect to the database
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error){ // if the connection fails, die
    die("Connect Error");
}


// initialize the response variable
$response = ["status" => "", "message" => ""];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user']; // get the user from the post request
    $email = $_POST['email']; // get the email from the post request
    $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT); // hash the password

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    // check if the email is already in the database

    if ($check->num_rows > 0){
        $response = ["status" => "error", "message" => "E-mail not available"]; // if the email is already in the database, return an error
    } else {
        $sql = $conn->prepare("INSERT INTO users (user, email, pass) VALUES (?, ?, ?)"); // insert the user into the database
        $sql->bind_param("sss", $user, $email, $pass);
        

        if ($sql->execute()) { // if the user is inserted into the database, return a success message
            $response = ["status" => "success", "message" => "New user successfully registered"];
        } else { // if the user is not inserted into the database, return an error
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
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <div class="container">
      <div class="card">
        <div class="card-header">
          <h2 class="card-title">Create account</h2>
          <p class="card-subtitle">Start using the app</p>
        </div>
        <div class="card-body">
          <form id="registerForm" method="post" class="form">
            <input class="input" type="text" name="user" placeholder="User" required />
            <div>
              <input class="input" type="email" id="email" name="email" placeholder="E-mail" required />
              <span id="emailMsg" class="helper"></span>
            </div>
            <div style="display: grid; grid-template-columns: 1fr auto; gap: 8px; align-items: center;">
              <input class="input" type="password" id="pass" name="pass" placeholder="Password" required />
              <button type="button" id="togglePass" name="togglePass" class="button secondary" style="padding: 10px 12px;">👁️</button>
            </div>
            <div class="actions">
              <button type="submit" class="button">Register</button>
              <p id="formMsg" class="helper"></p>
              <p class="helper">Want to go back? <a class="link" href="login.php">Login</a></p>
            </div>
          </form>
        </div>
      </div>
    </div>
  </body>

  <script>
    const passInput = document.getElementById("pass");
    const togglePass = document.getElementById("togglePass");
 // toggle the password visibility
    togglePass.addEventListener("click", () => {
      if (passInput.type === "password") { // if the password is hidden, show it
        passInput.type = "text";
        togglePass.textContent = "🙈";
      } else { // if the password is shown, hide it
        passInput.type = "password";
        togglePass.textContent = "👁️";
      }
    });

     // check if the email is already in the database
    document.getElementById("email").addEventListener("blur", function () { 
      const email = this.value;
      const msgSpan = document.getElementById("emailMsg"); // get the email message span

      // if the email is empty, return
      if (email.length === 0) {
        msgSpan.textContent = "";
        return;
      }
      // fetch the email from the database
      fetch("check_email.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" }, // set the content type to application/x-www-form-urlencoded
        body: "email=" + encodeURIComponent(email), // encode the email
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.status === "ok" && data.available === false) { // if the email is already in the database, return an error
            msgSpan.textContent = " E-mail not available.";
            msgSpan.style.color = "#fca5a5";
          } else if (data.status === "ok" && data.available === true) { // if the email is not in the database, return a success message
            msgSpan.textContent = " E-mail available.";
            msgSpan.style.color = "#86efac";
          } else {
            msgSpan.textContent = " Could not check e-mail.";
            msgSpan.style.color = "#fde68a";
          }
        })
        .catch(() => {
          msgSpan.textContent = " Could not check e-mail.";
          msgSpan.style.color = "#fde68a";
        });
    });

    document
      .getElementById("registerForm")
      .addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch("", { // send the form data to the server
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            const msg = document.getElementById("formMsg"); // get the form message span
            msg.textContent = data.message;
            msg.className = 'feedback ' + (data.status === 'success' ? 'success' : 'error'); // set the class name to feedback success or error
          })
          .catch((error) => console.error("Erro:", error));
      });
  </script>
</html>
