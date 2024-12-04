<?php
session_start();
include("php/config.php");

// Ensure only admins can access this page
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch the logged-in admin's details
$query = mysqli_prepare($conn, "SELECT username, fname, lname, email, contact FROM users WHERE Id = ?");
mysqli_stmt_bind_param($query, "i", $id);
mysqli_stmt_execute($query);
mysqli_stmt_bind_result($query, $res_Uname, $res_Fname, $res_Lname, $res_Email, $res_Contact);
mysqli_stmt_fetch($query);
mysqli_stmt_close($query);

// Fetch all subjects
$subject_query = mysqli_prepare($conn, "SELECT id, subject_code, subject_name FROM subjects");
mysqli_stmt_execute($subject_query);
$result = mysqli_stmt_get_result($subject_query);
mysqli_stmt_close($subject_query);
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
    <title>Manage Subjects</title>
</head>
<body>
    <?php require("php/admin_nav.php"); ?>

    <main>
        <div class="main-box top">
            <div class="box1">
                <p>Manage Subjects</p>
            </div>

            <div class="card-panel">
                <div class="table-container">
                    <table class="table styled-table">
                        <thead>
                            <tr class="table-header">
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr class="table-row">
                                    <td><?php echo htmlspecialchars($row['subject_code']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                                    <td>
                                        <a href="edit_subject.php?id=<?php echo $row['id']; ?>" class="action-link">Edit</a> | 
                                        <a href="delete_subject.php?id=<?php echo $row['id']; ?>" class="action-link" onclick="return confirm('Are you sure?');">Delete</a>
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
