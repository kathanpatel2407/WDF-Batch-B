<?php
include "db_pr11.php";

$message = "";
$messageType = "";

$name = "";
$email = "";
$course = "";
$phone = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    if ($name == "" || $email == "" || $course == "") {
        $message = "Please fill all required fields.";
        $messageType = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";

    } else {

        $sql = "INSERT INTO students_pr11 (name, email, course, phone)
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $message = "Database error. Please try again.";
            $messageType = "error";

        } else {

            $stmt->bind_param("ssss", $name, $email, $course, $phone);

            if ($stmt->execute()) {
                $message = "Student added successfully!";
                $messageType = "success";

                $name = "";
                $email = "";
                $course = "";
                $phone = "";

            } else {
                if ($stmt->errno == 1062) {
                    $message = "This email address is already registered.";
                } else {
                    $message = "Failed to add student. Please try again.";
                }

                $messageType = "error";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>

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
            box-shadow: 0 4px 12px #0002;
        }

        h2 {
            text-align: center;
            color: #1e3a5f;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #333;
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

        .success {
            color: green;
            background: #e8f7e8;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
        }

        .error {
            color: red;
            background: #fdeaea;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
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
    </style>
</head>

<body>
<div class="container">

    <h2>Add New Student</h2>

    <?php if ($message != "") { ?>
        <p class="<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?>
        </p>
    <?php } ?>

    <form method="POST" action="">

        <label for="name">Student Name *</label>
        <input
            type="text"
            id="name"
            name="name"
            placeholder="Enter student name"
            value="<?php echo htmlspecialchars($name, ENT_QUOTES, "UTF-8"); ?>"
            required
        >

        <label for="email">Email Address *</label>
        <input
            type="email"
            id="email"
            name="email"
            placeholder="Enter email address"
            value="<?php echo htmlspecialchars($email, ENT_QUOTES, "UTF-8"); ?>"
            required
        >

        <label for="course">Course *</label>
        <input
            type="text"
            id="course"
            name="course"
            placeholder="Enter course name"
            value="<?php echo htmlspecialchars($course, ENT_QUOTES, "UTF-8"); ?>"
            required
        >

        <label for="phone">Phone Number</label>
        <input
            type="tel"
            id="phone"
            name="phone"
            placeholder="Enter phone number"
            value="<?php echo htmlspecialchars($phone, ENT_QUOTES, "UTF-8"); ?>"
        >

        <button type="submit">Add Student</button>

    </form>

    <a href="index_pr11.php">Back to Student List</a>

</div>
</body>
</html>

<?php
$conn->close();
?>
