
<?php
include "db_pr12.php";

$message = "";
$messageType = "";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    die("Invalid event ID.");
}

$stmt = $conn->prepare("SELECT * FROM events_pr12 WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

if (!$event) {
    die("Event not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $event_date = $_POST["event_date"];
    $venue = trim($_POST["venue"]);
    $newPoster = $event["poster"];

    if ($title == "" || $description == "" ||
        $event_date == "" || $venue == "") {
        $message = "Please fill all required fields.";
        $messageType = "error";
    } else {
        $uploadDir = __DIR__ . "/uploads_pr12/";
        $uploadedNewPoster = false;

        if (isset($_FILES["poster"]) &&
            $_FILES["poster"]["error"] != UPLOAD_ERR_NO_FILE) {

            if ($_FILES["poster"]["error"] != UPLOAD_ERR_OK) {
                $message = "Poster upload failed.";
                $messageType = "error";
            } elseif ($_FILES["poster"]["size"] > 2 * 1024 * 1024) {
                $message = "Poster size must not exceed 2 MB.";
                $messageType = "error";
            } else {
                $allowedTypes = [
                    "image/jpeg" => "jpg",
                    "image/png" => "png",
                    "image/webp" => "webp"
                ];

                $mimeType = (new finfo(FILEINFO_MIME_TYPE))
                    ->file($_FILES["poster"]["tmp_name"]);

                if (!isset($allowedTypes[$mimeType])) {
                    $message = "Only JPG, PNG and WEBP posters are allowed.";
                    $messageType = "error";
                } else {
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $newPoster = bin2hex(random_bytes(16)) .
                                 "." . $allowedTypes[$mimeType];

                    if (move_uploaded_file(
                        $_FILES["poster"]["tmp_name"],
                        $uploadDir . $newPoster
                    )) {
                        $uploadedNewPoster = true;
                    } else {
                        $message = "Unable to save the new poster.";
                        $messageType = "error";
                    }
                }
            }
        }

        if ($messageType != "error") {
            $stmt = $conn->prepare(
                "UPDATE events_pr12
                 SET title = ?, description = ?, event_date = ?,
                     venue = ?, poster = ?
                 WHERE id = ?"
            );

            $stmt->bind_param(
                "sssssi",
                $title,
                $description,
                $event_date,
                $venue,
                $newPoster,
                $id
            );

            try {
                if ($stmt->execute()) {
                    $message = "Event updated successfully!";
                    $messageType = "success";
                    $oldPoster = $event["poster"];
                    $event["poster"] = $newPoster;

                    if ($uploadedNewPoster && !empty($oldPoster)) {
                        $oldPath = $uploadDir . basename($oldPoster);
                        if (is_file($oldPath)) {
                            unlink($oldPath);
                        }
                    }

                    $event["title"] = $title;
                    $event["description"] = $description;
                    $event["event_date"] = $event_date;
                    $event["venue"] = $venue;
                } else {
                    $message = "Failed to update event.";
                    $messageType = "error";
                }
            } catch (mysqli_sql_exception $e) {
                $message = "Database error. Event was not updated.";
                $messageType = "error";
            }

            $stmt->close();

            if ($messageType == "error" && $uploadedNewPoster) {
                $newPath = $uploadDir . $newPoster;
                if (is_file($newPath)) {
                    unlink($newPath);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Event</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef3f8;
            padding: 30px;
        }

        .container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px #0002;
        }

        h2 {
            text-align: center;
            color: #1e3a5f;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: #1e3a5f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .message {
            text-align: center;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .poster {
            width: 140px;
            display: block;
            margin-top: 10px;
            border-radius: 5px;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Event</h2>

    <?php if ($message != "") { ?>
        <p class="message <?php echo htmlspecialchars($messageType); ?>">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Event Title *</label>
        <input type="text" name="title"
               value="<?php echo htmlspecialchars($event["title"]); ?>"
               required>

        <label>Description *</label>
        <textarea name="description" rows="4" required><?php
            echo htmlspecialchars($event["description"]);
        ?></textarea>

        <label>Event Date *</label>
        <input type="date" name="event_date"
               value="<?php echo htmlspecialchars($event["event_date"]); ?>"
               required>

        <label>Venue *</label>
        <input type="text" name="venue"
               value="<?php echo htmlspecialchars($event["venue"]); ?>"
               required>

        <label>Current Poster</label>

        <?php if (!empty($event["poster"]) &&
            is_file(__DIR__ . "/uploads_pr12/" . basename($event["poster"]))) { ?>
            <img class="poster"
                 src="uploads_pr12/<?php echo rawurlencode(basename($event["poster"])); ?>"
                 alt="Current event poster">
        <?php } else { ?>
            <p>No poster uploaded.</p>
        <?php } ?>

        <label>Replace Poster (optional, max 2 MB)</label>
        <input type="file" name="poster"
               accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

        <button type="submit">Update Event</button>
    </form>

    <a href="index_pr12.php">Back to Events</a>
</div>

</body>
</html>