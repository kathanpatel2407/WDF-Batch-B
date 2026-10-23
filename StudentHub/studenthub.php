<?php

include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $student_id = $_POST["student_id"];
    $event_id = $_POST["event_id"];

    if (empty($student_id) || empty($event_id)) {
        $message = "Please select a student and an event.";
    } else {

        $sql = "INSERT INTO registrations (student_id, event_id)
                VALUES (?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $student_id, $event_id);

        if ($stmt->execute()) {
            $message = "Registration successful!";
        } else {
            $message = "Registration failed. Student may already be registered.";
        }

        $stmt->close();
    }
}

$students = $conn->query("SELECT * FROM students");
$events = $conn->query("SELECT * FROM events");

?>

<!DOCTYPE html>
<html>
<head>

    <title>StudentHub Registration</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1e3a5f, #3498db);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: white;
            width: 450px;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        h1 {
            text-align: center;
            color: #1e3a5f;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 7px;
            font-weight: bold;
        }

        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-size: 15px;
        }

        button {
            width: 100%;
            margin-top: 25px;
            padding: 13px;
            background: #1e3a5f;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #3498db;
        }

        .message {
            text-align: center;
            padding: 10px;
            margin-bottom: 15px;
            background: #e8f8ef;
            color: #198754;
            border-radius: 7px;
            font-weight: bold;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>StudentHub Event Registration</h1>

    <?php if ($message != ""): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <label>Select Student</label>

        <select name="student_id" required>

            <option value="">-- Select Student --</option>

            <?php while ($student = $students->fetch_assoc()): ?>

                <option value="<?php echo $student["student_id"]; ?>">
                    <?php echo htmlspecialchars($student["name"]); ?>
                </option>

            <?php endwhile; ?>

        </select>

        <label>Select Event</label>

        <select name="event_id" required>

            <option value="">-- Select Event --</option>

            <?php while ($event = $events->fetch_assoc()): ?>

                <option value="<?php echo $event["event_id"]; ?>">
                    <?php echo htmlspecialchars($event["event_name"]); ?>
                </option>

            <?php endwhile; ?>

        </select>

        <button type="submit">
            Register for Event
        </button>

    </form>

</div>

</body>
</html>
