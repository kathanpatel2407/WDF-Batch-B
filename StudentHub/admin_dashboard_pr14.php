
<?php
session_start();
require_once "db_pr14.php";

// Allow admin users only
if (
    !isset($_SESSION["user_id"]) ||
    ($_SESSION["role"] ?? "") !== "admin"
) {
    http_response_code(403);
    exit("Access denied. Admin access is required.");
}

$_SESSION["user_id"] = $user["id"];
$_SESSION["role"] = $user["role"];

// Record dashboard access
$log = $pdo->prepare(
    "INSERT INTO audit_logs (user_id, action, details)
     VALUES (?, ?, ?)"
);
$log->execute([
    $userId,
    "VIEW_DASHBOARD",
    "Admin opened the dashboard"
]);

// Load dynamic statistics
$totalUsers = (int) $pdo->query(
    "SELECT COUNT(*) FROM users"
)->fetchColumn();

$totalStudents = (int) $pdo->query(
    "SELECT COUNT(*) FROM students"
)->fetchColumn();

$totalEvents = (int) $pdo->query(
    "SELECT COUNT(*) FROM events"
)->fetchColumn();

// Latest five registrations
$latest = $pdo->query(
    "SELECT id, name, email, role, created_at
     FROM users
     ORDER BY created_at DESC, id DESC
     LIMIT 5"
)->fetchAll();

// Latest audit records
$audit = $pdo->query(
    "SELECT a.action, a.details, a.created_at, u.name
     FROM audit_logs a
     LEFT JOIN users u ON a.user_id = u.id
     ORDER BY a.id DESC
     LIMIT 10"
)->fetchAll();

function e($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Dashboard</title>
<style>
    * { box-sizing: border-box; }

    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #f1f5f9;
        color: #1e293b;
    }

    header {
        background: #1e3a5f;
        color: white;
        padding: 20px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    header h1 { margin: 0; font-size: 25px; }

    nav {
        background: white;
        padding: 14px 30px;
        box-shadow: 0 2px 5px #0000000d;
    }

    nav a {
        display: inline-block;
        margin: 5px 18px 5px 0;
        color: #1d4ed8;
        text-decoration: none;
        font-weight: bold;
    }

    main {
        max-width: 1100px;
        margin: 25px auto;
        padding: 0 18px;
    }

    .cards {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .card, section {
        background: white;
        padding: 22px;
        border-radius: 10px;
        box-shadow: 0 3px 12px #0000000b;
        margin-bottom: 22px;
    }

    .card p { color: #64748b; margin: 0 0 12px; }
    .card h2 { font-size: 32px; margin: 0; color: #1d4ed8; }

    .table-wrap { overflow-x: auto; }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 12px;
    }

    th, td {
        padding: 12px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
    }

    th { background: #eff6ff; }

    .logout {
        color: white;
        background: #dc2626;
        padding: 9px 13px;
        border-radius: 5px;
        text-decoration: none;
    }

    @media (max-width: 650px) {
        .cards { grid-template-columns: 1fr; }
        header { padding: 18px; }
    }
</style>
</head>
<body>

<header>
    <div>
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?= e($_SESSION["name"] ?? "Administrator") ?></p>
    </div>
    <a class="logout" href="logout_pr14.php">Logout</a>
</header>
<nav>
    <?php if (($_SESSION["role"] ?? "") === "admin"): ?>
        <a href="admin_dashboard_pr14.php">Dashboard</a>
        <a href="manage_students_pr14.php">Manage Students</a>
        <a href="manage_events_pr14.php">Manage Events</a>
        <a href="audit_logs_pr14.php">Audit Logs</a>
    <?php elseif (($_SESSION["role"] ?? "") === "student"): ?>
        <a href="student_dashboard_pr14.php">My Dashboard</a>
        <a href="events_pr14.php">View Events</a>
        <a href="profile_pr14.php">My Profile</a>
    <?php endif; ?>

    <a href="logout_pr14.php">Logout</a>
</nav>  

<main>
    <div class="cards">
        <div class="card">
            <p>Total Users</p>
            <h2><?= $totalUsers ?></h2>
        </div>

        <div class="card">
            <p>Total Students</p>
            <h2><?= $totalStudents ?></h2>
        </div>

        <div class="card">
            <p>Total Events</p>
            <h2><?= $totalEvents ?></h2>
        </div>
    </div>

    <section id="registrations">
        <h2>Latest Registrations</h2>
        <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registered At</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$latest): ?>
                <tr><td colspan="5">No registrations found.</td></tr>
            <?php else: ?>
                <?php foreach ($latest as $user): ?>
                <tr>
                    <td><?= (int) $user["id"] ?></td>
                    <td><?= e($user["name"]) ?></td>
                    <td><?= e($user["email"]) ?></td>
                    <td><?= e($user["role"]) ?></td>
                    <td><?= e($user["created_at"]) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </section>

    <section id="audit">
        <h2>Recent Audit Logs</h2>
        <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Admin/User</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$audit): ?>
                <tr><td colspan="4">No audit logs found.</td></tr>
            <?php else: ?>
                <?php foreach ($audit as $entry): ?>
                <tr>
                    <td><?= e($entry["name"] ?? "Deleted user") ?></td>
                    <td><?= e($entry["action"]) ?></td>
                    <td><?= e($entry["details"]) ?></td>
                    <td><?= e($entry["created_at"]) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </section>
</main>
</body>
</html>
