
<?php
session_start();
require_once "db_pr14.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Please enter your email and password.";
    } else {
        $stmt = $pdo->prepare(
            "SELECT id, name, email, password, role
             FROM users
             WHERE email = ?
             LIMIT 1"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["role"] = strtolower(trim($user["role"]));

            if ($_SESSION["role"] === "admin") {
                header("Location: admin_dashboard_pr14.php");
                exit;
            }

            if ($_SESSION["role"] === "student") {
                header("Location: student_dashboard_pr14.php");
                exit;
            }

            session_unset();
            session_destroy();
            $error = "Your account does not have an allowed role.";
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StudentHub</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            background: #eef2ff;
        }
        .login-box {
            width: 90%;
            max-width: 400px;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 22px #0002;
        }
        h2 { text-align: center; color: #253b80; }
        label { display: block; margin-top: 15px; }
        input {
            width: 100%;
            padding: 12px;
            margin-top: 7px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            width: 100%;
            padding: 12px;
            margin-top: 22px;
            border: none;
            border-radius: 6px;
            background: #253b80;
            color: white;
            cursor: pointer;
        }
        .error { color: #c62828; text-align: center; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>StudentHub Login</h2>

        <?php if ($error !== ""): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   required autocomplete="username">

            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   required autocomplete="current-password">

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
