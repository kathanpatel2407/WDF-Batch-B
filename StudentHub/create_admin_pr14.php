
<?php
require_once "db_pr14.php";

$name = "Admin";
$email = "admin@example.com";
$password = password_hash("Admin@123", PASSWORD_DEFAULT);
$role = "admin";

$stmt = $pdo->prepare(
    "SELECT id FROM users WHERE email = ?"
);
$stmt->execute([$email]);

if ($stmt->fetch()) {
    $stmt = $pdo->prepare(
        "UPDATE users SET password = ?, role = ?, name = ?
         WHERE email = ?"
    );
    $stmt->execute([$password, $role, $name, $email]);
    echo "Admin account updated successfully.";
} else {
    $stmt = $pdo->prepare(
        "INSERT INTO users (name, email, password, role, created_at)
         VALUES (?, ?, ?, ?, NOW())"
    );
    $stmt->execute([$name, $email, $password, $role]);
    echo "Admin account created successfully.";
}
?>
