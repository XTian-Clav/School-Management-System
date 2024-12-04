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
    $id = $adminRow['id'];  // Store the admin ID for use in the update query
    $uname = $adminRow['username'];
    $fname = $adminRow['fname'];
    $lname = $adminRow['lname'];
    $email = $adminRow['email'];
    $contact = $adminRow['contact'];
} else {
    die("Admin data not found.");
}
$stmt->close();

// Update admin details if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedUname = $_POST['username'];
    $updatedFname = $_POST['fname'];
    $updatedLname = $_POST['lname'];
    $updatedEmail = $_POST['email'];
    $updatedContact = $_POST['contact'];

    // Update query for admin
    $updateadminQuery = "UPDATE users SET username = ?, fname = ?, lname = ?, email = ?, contact = ? WHERE id = ?";
    $stmt = $conn->prepare($updateadminQuery);
    $stmt->bind_param("sssssi", $updatedUname, $updatedFname, $updatedLname, $updatedEmail, $updatedContact, $id);
    $stmt->execute();

    $_SESSION['admin'] = $updatedUname;

    header("Location: admin_profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/css/all.min.css">
    <title>Edit Profile</title>
</head>
<body>
    <?php require("php/admin_nav.php"); ?>

    <main>
        <div class="main-box top">
            <div class="box1">
                <p>Edit Profile</p>
            </div>

            <div class="card-panel">
                <form method="POST" action="">
                    <table class="styled-table styled-table-no-hover">
                        <thead>
                            <tr class="table-header">
                                <th colspan="3">Personal Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-row">
                                <td class="field">
                                    <div class="field input">
                                        <label for="uname">Username:</label>
                                        <input type="text" id="uname" name="username" value="<?php echo htmlspecialchars($uname); ?>" required>
                                    </div>
                                    <div class="field input">
                                        <label for="fname">First Name:</label>
                                        <input type="text" id="fname" name="fname" value="<?php echo htmlspecialchars($fname); ?>" required>
                                    </div>
                                    <div class="field input">
                                        <label for="lname">Last Name:</label>
                                        <input type="text" id="lname" name="lname" value="<?php echo htmlspecialchars($lname); ?>" required>
                                    </div>
                                </td>

                                <td class="field">
                                    <div class="field input">
                                        <label for="email">Email:</label>
                                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                                    </div>
                                    <div class="field input">
                                        <label for="contact">Contact:</label>
                                        <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($contact); ?>" required>
                                    </div>
                                </td>
                            </tr>

                            <tr class="table-row">
                                <td colspan="3" style="text-align: center;">
                                    <button type="submit" class="btn save-btn">Save Changes</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
