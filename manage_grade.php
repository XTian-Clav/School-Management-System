<?php
session_start();
include("php/config.php");

// Ensure only admins can access this page
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch grades with student username and subject name
$query = mysqli_prepare($conn, "
    SELECT 
        g.id AS grade_id, 
        u.fname,
        u.lname, 
        s.subject_name, 
        g.grade 
    FROM grades g
    JOIN users u ON g.student_id = u.id
    JOIN subjects s ON g.subject_id = s.id
    ORDER BY u.fname ASC, u.lname ASC
");
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);
mysqli_stmt_close($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/css/all.min.css">
    <link rel="stylesheet" href="style/css/fontawesome.min.css">
    <title>Manage Grades</title>
</head>
<body>
    <?php require("php/admin_nav.php"); ?>

    <main>
        <div class="main-box top">
            <div class="box1">
                <p>Manage Grades</p>
            </div>

            <div class="card-panel">
                <div class="table-container">
                    <table class="table styled-table">
                        <thead>
                            <tr class="table-header">
                                <th>Student Name</th>
                                <th>Subject Name</th>
                                <th>Grade</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr class="table-row">
                                    <td><?php echo htmlspecialchars($row['fname']); ?> <?php echo htmlspecialchars($row['lname']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['grade']); ?></td>
                                    <td>
                                        <a href="edit_grade.php?id=<?php echo $row['grade_id']; ?>" class="action-link">Edit</a> | 
                                        <a href="delete_grade.php?id=<?php echo $row['grade_id']; ?>" class="action-link" onclick="return confirm('Are you sure?');">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
