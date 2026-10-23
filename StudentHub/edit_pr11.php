<?php
include "db_pr11.php";

$message = "";

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    if ($name == "" || $email == "" || $course == "") {
        $message = "Please fill all required fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";

    } else {

        $stmt = $conn->prepare(
            "UPDATE students_pr11
             SET name = ?, email = ?, course = ?, phone = ?
             WHERE id = ?"
        );

        if (!$stmt) {
            $message = "Database query failed.";

        } else {
            $stmt->bind_param(
                "ssssi",
                $name,
                $email,
                $course,
                $phone,
                $id
            );

            try {
                if ($stmt->execute()) {
                    $stmt->close();
                    $conn->close();

                    header(
                        "Location: index_pr11.php?message=" .
                        urlencode("Student updated successfully!")
                    );
                    exit;

                } else {
                    $message = "Failed to update student.";
                    $stmt->close();
                }

            } catch (mysqli_sql_exception $e) {
                $message = "Update failed. This email may already belong to another student.";
                $stmt->close();
            }
        }
    }

    // Preserve entered values if validation or update fails.
    $student["name"] = $name;
    $student["email"] = $email;
    $student["course"] = $course;
    $student["phone"] = $phone;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #eef3f8;
            margin: 0;
            padding: 30px 15px;
        }

        .container {
            max-width: 450px;
            margin: auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
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

        input {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #3498db;
        }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            background: #1e3a5f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #3498db;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #1e3a5f;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .message {
            color: #c0392b;
            background: #fdeaea;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
<div class="container">

    <h2>Edit Student</h2>

    <?php if ($message != "") { ?>
        <p class="message">
            <?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?>
        </p>
    <?php } ?>

    <form method="POST" action="">

        <label for="name">Name *</label>
        <input
            type="text"
            id="name"
            name="name"
            value="<?php echo htmlspecialchars($student["name"] ?? "", ENT_QUOTES, "UTF-8"); ?>"
            required
        >

        <label for="email">Email *</label>
        <input
            type="email"
            id="email"
            name="email"
            value="<?php echo htmlspecialchars($student["email"] ?? "", ENT_QUOTES, "UTF-8"); ?>"
            required
        >

        <label for="course">Course *</label>
        <input
            type="text"
            id="course"
            name="course"
            value="<?php echo htmlspecialchars($student["course"] ?? "", ENT_QUOTES, "UTF-8"); ?>"
            required
        >

        <label for="phone">Phone</label>
        <input
            type="text"
            id="phone"
            name="phone"
            maxlength="15"
            value="<?php echo htmlspecialchars($student["phone"] ?? "", ENT_QUOTES, "UTF-8"); ?>"
        >

        <button type="submit">Update Student</button>

    </form>

    <a href="index_pr11.php">Back to Student List</a>

</div>
</body>
</html>

<?php
$conn->close();
?>
