<?php
session_start();
include("php/config.php");

// Ensure only admins can access this page
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Query to fetch admin data
$adminQuery = "SELECT id, username, fname, lname, email, contact FROM users WHERE username = ?";
$stmt = $conn->prepare($adminQuery);
$stmt->bind_param("s", $_SESSION['admin']);
$stmt->execute();
$adminResult = $stmt->get_result();

// Fetch admin details
if ($adminRow = $adminResult->fetch_assoc()) {
    $uname = $adminRow['username'];
    $fname = $adminRow['fname'];
    $lname = $adminRow['lname'];
    $email = $adminRow['email'];
    $contact = $adminRow['contact'];
} else {
    die("Admin data not found.");
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/css/all.min.css">
    <title>Admin Profile</title>
</head>
<body>
    <?php require("php/admin_nav.php"); ?>

    <main>
        <div class="main-box top">
            <div class="top">
                <div class="box1">
                    <p>Username: <b><?php echo htmlspecialchars($uname); ?></b></p>
                </div>
            </div>
                    
            <table class="styled-table styled-table-no-hover">
                <thead>
                    <tr class="table-header">
                        <th colspan="3" style="text-align: center;">Personal Info</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-row">
                        <td class="field">
                            <img src="style/admin.jpg" width="180" height="170" alt="profile" class="profile2">

                            <p>Fullname: <b><?php echo htmlspecialchars($fname); ?></b> <b><?php echo htmlspecialchars($lname); ?></b></p>
                            <br>
                            <p>Email: <b><?php echo htmlspecialchars($email); ?></b></p>
                            <p>Contact Number: <b><?php echo htmlspecialchars($contact); ?></b></p>
                        </td>
                    </tr>

                    <tr class="table-row">
                        <td colspan="3" style="text-align: center;">
                            <button class="btn save-btn" onclick="window.location.href='edit_admin_profile.php'">Edit Profile</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
