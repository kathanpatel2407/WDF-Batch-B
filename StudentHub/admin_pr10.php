<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: login_pr10.php");
    exit();
}

if (time() - ($_SESSION["last_activity"] ?? time()) > 900) {
    session_unset();
    session_destroy();
    header("Location: login_pr10.php");
    exit();
}

$_SESSION["last_activity"] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        header {
            background: #1e3a5f;
            color: white;
            padding: 20px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h2 {
            margin: 0;
        }

        .logout {
            background: white;
            color: #1e3a5f;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .logout:hover {
            background: #e2e8f0;
        }

        .container {
            max-width: 900px;
            margin: 45px auto;
            padding: 20px;
        }

        .welcome {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 12px #00000012;
            border-left: 5px solid #2563eb;
        }

        .welcome h1 {
            color: #1e3a5f;
            margin-top: 0;
        }

        .cards {
            display: flex;
            gap: 20px;
            margin-top: 25px;
            flex-wrap: wrap;
        }

        .card {
            flex: 1;
            min-width: 180px;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 12px #00000010;
        }

        .card h3 {
            color: #2563eb;
            margin-top: 0;
        }

        footer {
            text-align: center;
            color: #64748b;
            margin-top: 40px;
            padding: 20px;
        }

        @media (max-width: 600px) {
            header {
                padding: 18px;
            }

            .container {
                margin: 20px auto;
                padding: 15px;
            }

            .welcome {
                padding: 22px;
            }
        }
    </style>
</head>

<body>

<header>
    <h2>Admin Panel</h2>
    <a class="logout" href="logout_pr10.php">Logout</a>
</header>

<div class="container">

    <div class="welcome">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION["username"], ENT_QUOTES, 'UTF-8'); ?>!</h1>
        <p>You have successfully logged in to the administrator dashboard.</p>
        <p><strong>Role:</strong> Administrator</p>
    </div>

    <div class="cards">

        <div class="card">
            <h3>Admin Access</h3>
            <p>You are logged in with administrator privileges.</p>
        </div>

        <div class="card">
            <h3>Secure Session</h3>
            <p>Your account is protected by PHP session authentication.</p>
        </div>

        <div class="card">
            <h3>Account Status</h3>
            <p>Your current session is active.</p>
        </div>

    </div>
</div>

<footer>Practical 10 | Secure Login System</footer>

</body>
</html>