
<?php
session_start();

if (isset($_SESSION["user_id"])) {
    require_once "db_pr14.php";

    try {
        $log = $pdo->prepare(
            "INSERT INTO audit_logs (user_id, action, details)
             VALUES (?, ?, ?)"
        );

        $log->execute([
            (int) $_SESSION["user_id"],
            "LOGOUT",
            "User logged out"
        ]);
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

header("Location: login_pr14.php");
exit;
?>
