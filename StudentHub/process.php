<?php
if ($_SERVER["REQUEST_METHOD"] == "POST")
{

    $errors = [];
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $message = trim($_POST["message"] ?? "");

    $name = htmlspecialchars($name);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $mobile = preg_replace("/[^0-9]/", "", $mobile);
    $course = htmlspecialchars($course);
    $message = htmlspecialchars($message);

    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    if (strlen($mobile) != 10) {
        $errors[] = "Mobile number must contain 10 digits.";
    }

    if (empty($course)) {
        $errors[] = "Please select a course.";
    }

    // If there are errors
    if (!empty($errors)) {

        echo "<h2 style='color:red;'>Error!</h2>";

        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }

        echo "<br><a href='index.html'>Go Back</a>";

    } else {

        // Current date and time
        $date = date("Y-m-d H:i:s");

        // Data to store
        $data = [
            $name,
            $email,
            $mobile,
            $course,
            $message,
            $date
        ];

        // CSV file name
        $file = "registrations.csv";

        // Check whether file already exists
        $fileExists = file_exists($file);

        // Open CSV file
        $fp = fopen($file, "a");

        if ($fp !== false) {

            // Add header if file is new
            if (!$fileExists) {
                fputcsv($fp, [
                    "Name",
                    "Email",
                    "Mobile",
                    "Course",
                    "Message",
                    "Date"
                ]);
            }

            // Add user data
            fputcsv($fp, $data);

            // Close file
            fclose($fp);

            // Success message
            echo "<h2 style='color:green;'>Success!</h2>";
            echo "<p>Your details have been submitted successfully.</p>";
            echo "<a href='index.html'>Submit Another Response</a>";

        } else {

            echo "<h2 style='color:red;'>Error!</h2>";
            echo "<p>Unable to save the data.</p>";
        }
    }

} else {

    echo "Invalid request.";
}
?>
