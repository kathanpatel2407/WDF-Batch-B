
<?php
header("Content-Type: application/json; charset=UTF-8");

require_once "db_pr13.php";

function respond($success, $message, $data = null, $status = 200) {
    http_response_code($status);

    $response = [
        "success" => $success,
        "message" => $message
    ];

    if ($data !== null) {
        $response["data"] = $data;
    }

    echo json_encode($response);
    exit;
}

try {
    $method = $_SERVER["REQUEST_METHOD"];

    // GET: List all students
    if ($method === "GET") {
        $stmt = $pdo->query(
            "SELECT id, name, email, course
             FROM students ORDER BY id DESC"
        );

        respond(true, "Students fetched successfully", $stmt->fetchAll());
    }

    // POST: Add, update, or delete
    if ($method === "POST") {
        $input = json_decode(
            file_get_contents("php://input"),
            true
        );

        if (!is_array($input) || json_last_error() !== JSON_ERROR_NONE) {
            respond(false, "Invalid JSON request", null, 400);
        }

        $action = $input["action"] ?? "";

        // Delete a student
        if ($action === "delete") {
            $id = filter_var(
                $input["id"] ?? null,
                FILTER_VALIDATE_INT
            );

            if (!$id || $id < 1) {
                respond(false, "Valid student ID is required", null, 400);
            }

            $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                respond(false, "Student not found", null, 404);
            }

            respond(true, "Student deleted successfully");
        }

        // Validate input for add and update
        if (!in_array($action, ["add", "update"], true)) {
            respond(false, "Invalid action", null, 400);
        }

        $name = trim($input["name"] ?? "");
        $email = trim($input["email"] ?? "");
        $course = trim($input["course"] ?? "");

        if ($name === "" || $email === "" || $course === "") {
            respond(false, "All fields are required", null, 400);
        }

        if (
            strlen($name) > 100 ||
            strlen($email) > 150 ||
            strlen($course) > 100
        ) {
            respond(false, "Input exceeds the allowed length", null, 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            respond(false, "Enter a valid email address", null, 400);
        }

        // Add a student
        if ($action === "add") {
            $stmt = $pdo->prepare(
                "INSERT INTO students (name, email, course)
                 VALUES (?, ?, ?)"
            );

            $stmt->execute([$name, $email, $course]);

            respond(true, "Student added successfully", [
                "id" => (int) $pdo->lastInsertId(),
                "name" => $name,
                "email" => $email,
                "course" => $course
            ], 201);
        }

        // Update a student
        if ($action === "update") {
            $id = filter_var(
                $input["id"] ?? null,
                FILTER_VALIDATE_INT
            );

            if (!$id || $id < 1) {
                respond(false, "Valid student ID is required", null, 400);
            }

            $check = $pdo->prepare(
                "SELECT id FROM students WHERE id = ?"
            );
            $check->execute([$id]);

            if (!$check->fetch()) {
                respond(false, "Student not found", null, 404);
            }

            $stmt = $pdo->prepare(
                "UPDATE students
                 SET name = ?, email = ?, course = ?
                 WHERE id = ?"
            );

            $stmt->execute([$name, $email, $course, $id]);

            respond(true, "Student updated successfully");
        }
    }

    header("Allow: GET, POST");
    respond(false, "Method not allowed", null, 405);

} catch (PDOException $e) {
    if ($e->getCode() === "23000") {
        respond(false, "This email already exists", null, 409);
    }

    error_log($e->getMessage());
    respond(false, "A database error occurred", null, 500);

} catch (Throwable $e) {
    error_log($e->getMessage());
    respond(false, "An internal server error occurred", null, 500);
}
?>
