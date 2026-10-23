
<?php
include "db_pr12.php";

$message = "";
$event = null;

if (isset($_GET["id"])) {
    $id = (int) $_GET["id"];

    if ($id > 0) {
        $stmt = $conn->prepare("SELECT id, title, poster FROM events_pr12 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $event = $result->fetch_assoc();
        $stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirm_delete"])) {
    $id = (int) $_POST["id"];

    $stmt = $conn->prepare("SELECT poster FROM events_pr12 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $eventToDelete = $result->fetch_assoc();
    $stmt->close();

    if (!$eventToDelete) {
        header("Location: index_pr12.php?message=" . urlencode("Event not found."));
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM events_pr12 WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $poster = $eventToDelete["poster"];

        if ($poster && basename($poster) === $poster) {
            $posterPath = __DIR__ . "/uploads_pr12/" . $poster;

            if (is_file($posterPath)) {
                unlink($posterPath);
            }
        }

        $stmt->close();
        header("Location: index_pr12.php?message=" . urlencode("Event deleted successfully."));
        exit;
    } else {
        $message = "Failed to delete the event.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Event</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 40px 15px;
        }
        .container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px #00000015;
            text-align: center;
        }
        h2 { color: #b91c1c; }
        .event-name { font-weight: bold; }
        .message { color: #b91c1c; }
        button, .cancel {
            display: inline-block;
            padding: 11px 18px;
            margin: 8px 4px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 15px;
            cursor: pointer;
        }
        button { background: #dc2626; color: white; }
        .cancel { background: #e2e8f0; color: #1e293b; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Confirm Event Deletion</h2>

        <?php if ($message !== ""): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <?php if ($event): ?>
            <p>Are you sure you want to delete this event?</p>
            <p class="event-name">
                <?php echo htmlspecialchars($event["title"]); ?>
            </p>

            <form method="POST">
                <input type="hidden" name="id"
                       value="<?php echo (int) $event["id"]; ?>">

                <button type="submit" name="confirm_delete"
                        value="1">Yes, Delete</button>

                <a class="cancel" href="index_pr12.php">Cancel</a>
            </form>
        <?php else: ?>
            <p>Event not found or invalid event ID.</p>
            <a class="cancel" href="index_pr12.php">Back to Events</a>
        <?php endif; ?>
    </div>
</body>
</html>