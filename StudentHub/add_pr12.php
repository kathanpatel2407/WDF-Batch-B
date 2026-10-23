
<?php
include "db_pr12.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $event_date = $_POST["event_date"];
    $venue = trim($_POST["venue"]);
    $posterName = null;

    if ($title == "" || $description == "" ||
        $event_date == "" || $venue == "") {

        $message = "Please fill all required fields.";
        $messageType = "error";

    } else {
        if (isset($_FILES["poster"]) &&
            $_FILES["poster"]["error"] != UPLOAD_ERR_NO_FILE) {

            if ($_FILES["poster"]["error"] != UPLOAD_ERR_OK) {
                $message = "Poster upload failed.";
                $messageType = "error";

            } elseif ($_FILES["poster"]["size"] > 2 * 1024 * 1024) {
                $message = "Poster size must be less than or equal to 2 MB.";
                $messageType = "error";

            } else {
                $allowedTypes = [
                    "image/jpeg" => "jpg",
                    "image/png" => "png",
                    "image/webp" => "webp"
                ];

                $tmpName = $_FILES["poster"]["tmp_name"];
                $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($tmpName);

                if (!isset($allowedTypes[$mimeType])) {
                    $message = "Only JPG, PNG and WEBP posters are allowed.";
                    $messageType = "error";
                } else {
                    $uploadDir = __DIR__ . "/uploads_pr12/";

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $posterName = bin2hex(random_bytes(16)) .
                                  "." . $allowedTypes[$mimeType];

                    if (!move_uploaded_file(
                        $tmpName,
                        $uploadDir . $posterName
                    )) {
                        $message = "Unable to save the poster.";
                        $messageType = "error";
                    }
                }
            }
        }

        if ($messageType != "error") {
            $stmt = $conn->prepare(
                "INSERT INTO events_pr12
                (title, description, event_date, venue, poster)
                VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssss",
                $title,
                $description,
                $event_date,
                $venue,
                $posterName
            );

            try {
                if ($stmt->execute()) {
                    $message = "Event added successfully!";
                    $messageType = "success";
                } else {
                    $message = "Failed to add event.";
                    $messageType = "error";
                }
            } catch (mysqli_sql_exception $e) {
                $message = "Database error: unable to add event.";
                $messageType = "error";
            }

            $stmt->close();

            if ($messageType == "error" && $posterName !== null) {
                $posterPath = __DIR__ . "/uploads_pr12/" . $posterName;
                if (is_file($posterPath)) {
                    unlink($posterPath);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Event</title>
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
            border: none;
            border-radius: 5px;
            background: #1e3a5f;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background: #3498db;
        }

        .success {
            color: green;
            text-align: center;
        }

        .error {
            color: red;
            text-align: center;
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
    <h2>Add New Event</h2>

    <?php if ($message != "") { ?>
        <p class="<?php echo htmlspecialchars($messageType); ?>">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Event Title *</label>
        <input type="text" name="title" required>

        <label>Description *</label>
        <textarea name="description" rows="4" required></textarea>

        <label>Event Date *</label>
        <input type="date" name="event_date" required>

        <label>Venue *</label>
        <input type="text" name="venue" required>

        <label>Event Poster (JPG, PNG or WEBP; max 2 MB)</label>
        <input type="file" name="poster"
               accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

        <button type="submit">Add Event</button>
    </form>

    <a href="index_pr12.php">View All Events</a>
</div>

</body>
</html>