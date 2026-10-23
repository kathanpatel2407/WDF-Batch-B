<?php
include "db_pr11.php";

$search = trim($_GET["search"] ?? "");
$courseFilter = trim($_GET["course"] ?? "");
$message = trim($_GET["message"] ?? "");

$sql = "SELECT * FROM students_pr11
        WHERE (name LIKE ? OR email LIKE ? OR course LIKE ?)
        AND course LIKE ?
        ORDER BY id DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Query preparation failed: " . htmlspecialchars($conn->error));
}

$searchTerm = "%" . $search . "%";
$courseTerm = "%" . $courseFilter . "%";

$stmt->bind_param(
    "ssss",
    $searchTerm,
    $searchTerm,
    $searchTerm,
    $courseTerm
);

if (!$stmt->execute()) {
    die("Query execution failed.");
}

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #eef3f8;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px #0002;
        }

        h2 {
            color: #1e3a5f;
            text-align: center;
        }

        .add-btn {
            display: inline-block;
            background: #1e3a5f;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .add-btn:hover {
            background: #294f7d;
        }

        .search-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            flex: 1;
            min-width: 180px;
        }

        button {
            padding: 10px 15px;
            background: #1e3a5f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #294f7d;
        }

        .clear-btn {
            padding: 10px;
            color: #1e3a5f;
            text-decoration: none;
            align-self: center;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #1e3a5f;
            color: white;
        }

        tr:nth-child(even) {
            background: #f2f6fa;
        }

        .message {
            text-align: center;
            color: green;
            background: #e8f7e8;
            padding: 10px;
            border-radius: 5px;
        }

        .delete-link {
            color: red;
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 15px;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <h2>Student Management System</h2>

    <a class="add-btn" href="add_pr11.php">+ Add Student</a>

    <form method="GET" action="index_pr11.php" class="search-form">
        <input
            type="text"
            name="search"
            placeholder="Search name, email or course"
            value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
        >

        <input
            type="text"
            name="course"
            placeholder="Filter by course"
            value="<?php echo htmlspecialchars($courseFilter, ENT_QUOTES, 'UTF-8'); ?>"
        >

        <button type="submit">Search / Filter</button>
        <a class="clear-btn" href="index_pr11.php">Clear</a>
    </form>

    <?php if ($message !== "") { ?>
        <p class="message">
            <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
        </p>
    <?php } ?>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Phone</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($result && $result->num_rows > 0) { ?>

                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo (int) $row["id"]; ?></td>

                        <td>
                            <?php echo htmlspecialchars($row["name"] ?? "", ENT_QUOTES, 'UTF-8'); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["email"] ?? "", ENT_QUOTES, 'UTF-8'); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["course"] ?? "", ENT_QUOTES, 'UTF-8'); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["phone"] ?? "", ENT_QUOTES, 'UTF-8'); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["created_at"] ?? "", ENT_QUOTES, 'UTF-8'); ?>
                        </td>

                        <td>
                            <a href="edit_pr11.php?id=<?php echo (int) $row["id"]; ?>">Edit</a>
                            |
                            <a
                                class="delete-link"
                                href="delete_pr11.php?id=<?php echo (int) $row["id"]; ?>"
                                onclick="return confirm('Are you sure you want to delete this student?');"
                            >Delete</a>
                        </td>
                    </tr>
                <?php } ?>

            <?php } else { ?>

                <tr>
                    <td colspan="7">No student records found.</td>
                </tr>

            <?php } ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
