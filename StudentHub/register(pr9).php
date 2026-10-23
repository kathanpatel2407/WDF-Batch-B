<?php
include "db(pr9).php";

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $username)) {
        $message = "Username must be 3-20 characters.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $message = "Password must contain at least 8 characters.";
    } else {
        $stmt = $conn->prepare(
            "SELECT id FROM users WHERE username = ? OR email = ?"
        );

        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Username or email already exists!";
            $stmt->close();
        } else {
            $stmt->close();

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO users (username, email, password)
                 VALUES (?, ?, ?)"
            );

            $stmt->bind_param(
                "sss",
                $username,
                $email,
                $hashedPassword
            );

            if ($stmt->execute()) {
                $message = "Registration successful!";
                $success = true;
            } else {
                $message = "Registration failed. Please try again.";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Secure Registration</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 25px;
    background: linear-gradient(135deg, #101c3b, #2855a6, #7c3aed);
}

.container {
    width: 100%;
    max-width: 440px;
    padding: 35px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
}

.icon {
    width: 65px;
    height: 65px;
    margin: 0 auto 18px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 18px;
    background: #e8edff;
    color: #3455db;
    font-size: 32px;
}

h2 {
    text-align: center;
    color: #172554;
    font-size: 27px;
    margin-bottom: 8px;
}

.subtitle {
    text-align: center;
    color: #64748b;
    font-size: 14px;
    margin-bottom: 25px;
}

.message {
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 8px;
    font-size: 14px;
    text-align: center;
    background: <?php echo $success ? '#dcfce7' : '#fee2e2'; ?>;
    color: <?php echo $success ? '#166534' : '#991b1b'; ?>;
}

label {
    display: block;
    margin-bottom: 7px;
    color: #334155;
    font-size: 14px;
    font-weight: bold;
}

input {
    width: 100%;
    padding: 12px;
    margin-bottom: 18px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.3s, box-shadow 0.3s;
}

input:focus {
    border-color: #3455db;
    box-shadow: 0 0 0 3px rgba(52, 85, 219, 0.12);
}

button {
    width: 100%;
    padding: 13px;
    border: none;
    border-radius: 8px;
    background: linear-gradient(135deg, #3455db, #7c3aed);
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: transform 0.2s, opacity 0.2s;
}

button:hover {
    opacity: 0.9;
    transform: translateY(-2px);
}

.footer {
    text-align: center;
    margin-top: 22px;
    font-size: 12px;
    color: #64748b;
}

@media (max-width: 480px) {
    .container {
        padding: 25px 20px;
    }
}
</style>
</head>

<body>
<div class="container">

    <div class="icon">&#128274;</div>

    <h2>Create Account</h2>
    <p class="subtitle">Register securely to get started</p>

    <?php if ($message != "") { ?>
        <div class="message" role="status">
            <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php } ?>

    <form method="POST" onsubmit="return validateForm()">

        <label for="username">Username</label>
        <input type="text" id="username" name="username"
               placeholder="Choose a username"
               minlength="3" maxlength="20"
               pattern="[a-zA-Z0-9_]{3,20}" required>

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email"
               placeholder="Enter your email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password"
               placeholder="Minimum 8 characters"
               minlength="8" required>

        <button type="submit">Create Account &rarr;</button>

    </form>

    <div class="footer">
        &#128274; Your information is securely protected.
    </div>

</div>

<script>
function validateForm() {
    const username = document.getElementById("username").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;

    if (!/^[a-zA-Z0-9_]{3,20}$/.test(username)) {
        alert("Username must be 3-20 letters, numbers or underscores.");
        return false;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        alert("Please enter a valid email address.");
        return false;
    }

    if (password.length < 8) {
        alert("Password must contain at least 8 characters.");
        return false;
    }

    return true;
}
</script>

</body>
</html>
