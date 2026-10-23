<?php
include "db_pr11.php";

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id <= 0) {
    die("Invalid student ID.");
}

$stmt = $conn->prepare(
    "SELECT * FROM students_pr11 WHERE id = ?"
);

if (!$stmt) {
    die("Database query failed.");
}

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$student = $result->fetch_assoc();

$stmt->close();

if (!$student) {
    die("Student record not found.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["confirm"])) {

        $stmt = $conn->prepare(
            "DELETE FROM students_pr11 WHERE id = ?"
        );

        if (!$stmt) {
            $message = "Database query failed.";
        } else {
            $stmt->bind_param("i", $id);

            if ($stmt->execute() && $stmt->affected_rows > 0) {
                $stmt->close();
                $conn->close();

                header(
                    "Location: index_pr11.php?message=" .
                    urlencode("Student deleted successfully!")
                );
                exit;
            } else {
                $message = "Failed to delete student.";
            }

            $stmt->close();
        }

    } elseif (isset($_POST["cancel"])) {

        $conn->close();

        header(
            "Location: index_pr11.php?message=" .
            urlencode("Deletion cancelled.")
        );
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Student</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #eef3f8;
            margin: 0;
            padding: 40px 15px;
        }

        .container {
            max-width: 450px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 12px #0002;
        }

        h2 {
            color: #c0392b;
        }

        .details {
            text-align: left;
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            overflow-wrap: anywhere;
        }

        .details p {
            margin: 10px 0;
        }

        button {
            padding: 10px 20px;
            margin: 10px 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .delete {
            background: #c0392b;
            color: white;
        }

        .delete:hover {
            background: #a93226;
        }

        .cancel {
            background: #1e3a5f;
            color: white;
        }

        .cancel:hover {
            background: #294f7d;
        }

        .error {
            color: red;
            background: #fdeaea;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
<div class="container">

    <h2>Confirm Deletion</h2>

    <p>Are you sure you want to delete this student?</p>

    <div class="details">
        <p>
            <strong>ID:</strong>
            <?php echo (int) $student["id"]; ?>
        </p>

        <p>
            <strong>Name:</strong>
            <?php echo htmlspecialchars($student["name"], ENT_QUOTES, "UTF-8"); ?>
        </p>

        <p>
            <strong>Email:</strong>
            <?php echo htmlspecialchars($student["email"], ENT_QUOTES, "UTF-8"); ?>
        </p>

        <p>
            <strong>Course:</strong>
            <?php echo htmlspecialchars($student["course"], ENT_QUOTES, "UTF-8"); ?>
        </p>
    </div>

    <?php if ($message != "") { ?>
        <p class="error">
            <?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?>
        </p>
    <?php } ?>

    <form method="POST" action="">
        <button
            type="submit"
            name="confirm"
            value="yes"
            class="delete"
        >
            Yes, Delete
        </button>

        <button
            type="submit"
            name="cancel"
            value="yes"
            class="cancel"
        >
            Cancel
        </button>
    </form>

</div>
</body>
</html>

<?php
$conn->close();
?>
