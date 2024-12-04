<?php
session_start();
include("php/config.php");

// Ensure only students can access this page
if (!isset($_SESSION['student'])) {
    header("Location: login.php");
    exit();
}

// Query to fetch user data
$userQuery = "SELECT id, username, fname, lname, email, contact FROM users WHERE username = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("s", $_SESSION['student']);
$stmt->execute();
$userResult = $stmt->get_result();

// Fetch user details
if ($userRow = $userResult->fetch_assoc()) {
    $uname = $userRow['username'];
    $fname = $userRow['fname'];
    $lname = $userRow['lname'];
    $email = $userRow['email'];
    $contact = $userRow['contact'];
} else {
    die("User data not found.");
}
$stmt->close();

// Query to fetch academic info
$academicQuery = "SELECT college, course, year_level FROM academic_info WHERE id = ?";
$stmt = $conn->prepare($academicQuery);
$stmt->bind_param("i", $userRow['id']);
$stmt->execute();
$academicResult = $stmt->get_result();

if ($academicRow = $academicResult->fetch_assoc()) {
    $college = $academicRow['college'];
    $course = $academicRow['course'];
    $year_level = $academicRow['year_level'];
} else {
    $college = "N/A";
    $course = "N/A";
    $year_level = "N/A";
}
$stmt->close();

// Query to fetch personal details
$personalQuery = "SELECT address, birthdate, religion, occupation FROM personal_details WHERE id = ?";
$stmt = $conn->prepare($personalQuery);
$stmt->bind_param("i", $userRow['id']);
$stmt->execute();
$personalResult = $stmt->get_result();

if ($personalRow = $personalResult->fetch_assoc()) {
    $address = $personalRow['address'];
    $birthdate = $personalRow['birthdate'];
    $religion = $personalRow['religion'];
    $occupation = $personalRow['occupation'];
} else {
    $address = "N/A";
    $birthdate = "N/A";
    $religion = "N/A";
    $occupation = "N/A";
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
    <title>Student Profile</title>
</head>
<body>
    <?php require("php/nav.php"); ?>

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
                            <img src="style/profile.jpg" width="180" height="170" alt="profile" class="profile2">

                            <p>Fullname: <b><?php echo htmlspecialchars($fname); ?></b> <b><?php echo htmlspecialchars($lname); ?></b></p>
                            <p>College: <b><?php echo htmlspecialchars($college); ?></b></p>
                            <p>Course: <b><?php echo htmlspecialchars($course); ?></b></p>
                            <p>Year Level: <b><?php echo htmlspecialchars($year_level); ?></b></p>
                            <br>
                            <p>Address: <b><?php echo htmlspecialchars($address); ?></b></p>
                            <p>Date of Birth: <b><?php echo htmlspecialchars($birthdate); ?></b></p>
                            <p>Religion: <b><?php echo htmlspecialchars($religion); ?></b></p>
                            <p>Occupation: <b><?php echo htmlspecialchars($occupation); ?></b></p>
                            <br>
                            <p>Email: <b><?php echo htmlspecialchars($email); ?></b></p>
                            <p>Contact Number: <b><?php echo htmlspecialchars($contact); ?></b></p>
                        </td>
                    </tr>

                    <tr class="table-row">
                        <td colspan="3" style="text-align: center;">
                            <button class="btn save-btn" onclick="window.location.href='edit_profile.php'">Edit Profile</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
