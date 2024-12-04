<?php
session_start();

include("php/config.php");

// Ensure only admins can access this page
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch all users except admins
$query = mysqli_prepare($conn, "SELECT id, username, fname, lname, email, contact FROM users WHERE user_type != ?");
$user_type = 'admin'; // Assuming 'admin' is the value in the user_type column for admins
mysqli_stmt_bind_param($query, "s", $user_type);
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
    <title>Manage Student</title>
</head>
<body>
    <?php require("php/admin_nav.php"); ?>

    <main>
        <div class="main-box top">
            <div class="box1">
                <p>Manage Students</p>
            </div>

            <div class="card-panel">
                <div class="table-container">
                    <table class="table styled-table">
                        <thead>
                            <tr class="table-header">
                                <th>Username</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>User Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr class="table-row">
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td><?php echo htmlspecialchars($row['fname']); ?></td>
                                    <td><?php echo htmlspecialchars($row['lname']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['contact']); ?></td>
                                    <td><?php echo $row['id'] == 1 ? 'Admin' : 'Student'; ?></td>
                                    <td>
                                        <a href="edit_student.php?id=<?php echo $row['id']; ?>" class="action-link">Edit</a> | 
                                        <a href="delete_student.php?id=<?php echo $row['id']; ?>" class="action-link" onclick="return confirm('Are you sure?');">Delete</a>
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
