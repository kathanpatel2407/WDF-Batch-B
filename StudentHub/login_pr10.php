<?php
session_start();
require "db_pr10.php";

if (isset($_SESSION["user_id"])) {
    if ($_SESSION["role"] == "admin") {
        header("Location: admin_pr10.php");
    } else {
        header("Location: student_pr10.php");
    }
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT id, username, password, role
         FROM login_users_pr10
         WHERE username = ?"
    );

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["last_activity"] = time();

            if ($user["role"] == "admin") {
                header("Location: admin_pr10.php");
            } else {
                header("Location: student_pr10.php");
            }
            exit();
        }
    }

    $error = "Invalid username or password.";
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }

        .login {
            width: 300px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #1e3a5f;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input {
            width: 95%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #1e3a5f;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #294f7c;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="login">
        <h2>Login Page</h2>

        <?php if ($error != "") { ?>
            <p class="error">
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </p>
        <?php } ?>

        <form method="POST" autocomplete="off">
            <label for="username">Username</label>
            <input type="text" id="username" name="username"
                   autocomplete="username" required>

            <br><br>

            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   autocomplete="current-password" required>

            <br><br>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
