
<?php
include "db_pr12.php";

$sql = "SELECT * FROM events_pr12 ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Event Management</title>
    <style>
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
            border-radius: 12px;
            box-shadow: 0 4px 12px #0002;
        }

        h2 {
            text-align: center;
            color: #1e3a5f;
        }

        .add-btn {
            display: inline-block;
            padding: 10px 15px;
            background: #1e3a5f;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background: #1e3a5f;
            color: white;
        }

        tr:nth-child(even) {
            background: #f2f6fa;
        }

        .poster {
            width: 100px;
            height: 70px;
            object-fit: cover;
            border-radius: 5px;
        }

        .action {
            display: inline-block;
            margin: 3px;
            text-decoration: none;
        }

        .edit {
            color: #1769aa;
        }

        .delete {
            color: #c0392b;
        }

        .message {
            text-align: center;
            color: green;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Event Management System</h2>

    <a class="add-btn" href="add_pr12.php">+ Add Event</a>

    <?php if (isset($_GET["message"])) { ?>
        <p class="message">
            <?php echo htmlspecialchars($_GET["message"]); ?>
        </p>
    <?php } ?>

    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Date</th>
            <th>Venue</th>
            <th>Poster</th>
            <th>Actions</th>
        </tr>

        <?php if ($result && $result->num_rows > 0) { ?>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo (int)$row["id"]; ?></td>

                    <td>
                        <?php echo htmlspecialchars($row["title"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["description"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["event_date"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["venue"]); ?>
                    </td>

                    <td>
                        <?php if (!empty($row["poster"]) &&
                            is_file(__DIR__ . "/uploads_pr12/" . basename($row["poster"]))) { ?>

                            <img class="poster"
                                 src="uploads_pr12/<?php echo rawurlencode(basename($row["poster"])); ?>"
                                 alt="Event poster">

                        <?php } else { ?>
                            No poster
                        <?php } ?>
                    </td>

                    <td>
                        <a class="action edit"
                           href="edit_pr12.php?id=<?php echo (int)$row["id"]; ?>">
                           Edit
                        </a>

                        |

                        <a class="action delete"
                           href="delete_pr12.php?id=<?php echo (int)$row["id"]; ?>">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="7">No events found.</td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>