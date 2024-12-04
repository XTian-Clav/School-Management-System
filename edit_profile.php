<?php
session_start();
include("php/config.php");

// Ensure only students can access this page
if (!isset($_SESSION['student'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data
$userQuery = "SELECT id, username, fname, lname, email, contact FROM users WHERE username = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("s", $_SESSION['student']);
$stmt->execute();
$userResult = $stmt->get_result();

if ($userRow = $userResult->fetch_assoc()) {
    $id = $userRow['id'];
    $uname = $userRow['username'];
    $fname = $userRow['fname'];
    $lname = $userRow['lname'];
    $email = $userRow['email'];
    $contact = $userRow['contact'];
} else {
    die("User data not found.");
}
$stmt->close();

// Fetch academic info
$academicQuery = "SELECT college, course, year_level FROM academic_info WHERE id = ?";
$stmt = $conn->prepare($academicQuery);
$stmt->bind_param("i", $id);
$stmt->execute();
$academicResult = $stmt->get_result();

if ($academicRow = $academicResult->fetch_assoc()) {
    $college = $academicRow['college'];
    $course = $academicRow['course'];
    $year_level = $academicRow['year_level'];
} else {
    $college = $course = $year_level = "N/A";
}
$stmt->close();

// Fetch personal details
$personalQuery = "SELECT address, birthdate, religion, occupation FROM personal_details WHERE id = ?";
$stmt = $conn->prepare($personalQuery);
$stmt->bind_param("i", $id);
$stmt->execute();
$personalResult = $stmt->get_result();

if ($personalRow = $personalResult->fetch_assoc()) {
    $address = $personalRow['address'];
    $birthdate = $personalRow['birthdate'];
    $religion = $personalRow['religion'];
    $occupation = $personalRow['occupation'];
} else {
    $address = $birthdate = $religion = $occupation = "N/A";
}
$stmt->close();

// Update user details if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedUname = $_POST['username'];
    $updatedFname = $_POST['fname'];
    $updatedLname = $_POST['lname'];
    $updatedEmail = $_POST['email'];
    $updatedContact = $_POST['contact'];
    $updatedAddress = $_POST['address'];
    $updatedBirthdate = $_POST['birthdate'];
    $updatedReligion = $_POST['religion'];
    $updatedOccupation = $_POST['occupation'];
    $updatedCollege = $_POST['college'];
    $updatedCourse = $_POST['course'];
    $updatedYearLevel = $_POST['year_level'];

    // Update query for users table
    $updateUserQuery = "UPDATE users SET username = ?, fname = ?, lname = ?, email = ?, contact = ? WHERE id = ?";
    $stmt = $conn->prepare($updateUserQuery);
    $stmt->bind_param("sssssi", $updatedUname, $updatedFname, $updatedLname, $updatedEmail, $updatedContact, $id);
    $stmt->execute();

    // Update or insert for personal_details
    $personalQuery = "SELECT id FROM personal_details WHERE id = ?";
    $stmt = $conn->prepare($personalQuery);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $personalResult = $stmt->get_result();

    if ($personalResult->num_rows > 0) {
        // Update existing record
        $updatePersonalQuery = "UPDATE personal_details SET address = ?, birthdate = ?, religion = ?, occupation = ? WHERE id = ?";
        $stmt = $conn->prepare($updatePersonalQuery);
        $stmt->bind_param("ssssi", $updatedAddress, $updatedBirthdate, $updatedReligion, $updatedOccupation, $id);
        $stmt->execute();
    } else {
        // Insert new record
        $insertPersonalQuery = "INSERT INTO personal_details (id, address, birthdate, religion, occupation) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertPersonalQuery);
        $stmt->bind_param("issss", $id, $updatedAddress, $updatedBirthdate, $updatedReligion, $updatedOccupation);
        $stmt->execute();
    }

    // Update or insert for academic_info
    $academicQuery = "SELECT id FROM academic_info WHERE id = ?";
    $stmt = $conn->prepare($academicQuery);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $academicResult = $stmt->get_result();

    if ($academicResult->num_rows > 0) {
        // Update existing record
        $updateAcademicQuery = "UPDATE academic_info SET college = ?, course = ?, year_level = ? WHERE id = ?";
        $stmt = $conn->prepare($updateAcademicQuery);
        $stmt->bind_param("sssi", $updatedCollege, $updatedCourse, $updatedYearLevel, $id);
        $stmt->execute();
    } else {
        // Insert new record
        $insertAcademicQuery = "INSERT INTO academic_info (id, college, course, year_level) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertAcademicQuery);
        $stmt->bind_param("isss", $id, $updatedCollege, $updatedCourse, $updatedYearLevel);
        $stmt->execute();
    }

    // Update the session with the new username
    $_SESSION['student'] = $updatedUname;

    // Redirect to profile page
    header("Location: profile.php");
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
    <?php require("php/nav.php"); ?>

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
                                <th colspan="3" style="text-align: center;">Personal Info</th>
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
                                    <div class="field input">
                                        <label for="contact">Contact:</label>
                                        <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($contact); ?>" required>
                                    </div>
                                </td>

                                <td class="field">
                                    <div class="field input">
                                        <label for="college">College:</label>
                                        <input type="text" id="college" name="college" value="<?php echo htmlspecialchars($college); ?>">
                                    </div>
                                    <div class="field input">
                                        <label for="course">Course:</label>
                                        <input type="text" id="course" name="course" value="<?php echo htmlspecialchars($course); ?>">
                                    </div>
                                    <div class="field input">
                                        <label for="year_level">Year Level:</label>
                                        <input type="text" id="year_level" name="year_level" value="<?php echo htmlspecialchars($year_level); ?>">
                                    </div>
                                    <div class="field input">
                                        <label for="email">Email:</label>
                                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                                    </div>
                                </td>

                                <td class="field">
                                    <div class="field input">
                                        <label for="address">Address:</label>
                                        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>">
                                    </div>
                                    <div class="field input">
                                        <label for="birthdate">Birthdate:</label>
                                        <input type="date" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($birthdate); ?>">
                                    </div>
                                    <div class="field input">
                                        <label for="religion">Religion:</label>
                                        <input type="text" id="religion" name="religion" value="<?php echo htmlspecialchars($religion); ?>">
                                    </div>
                                    <div class="field input">
                                        <label for="occupation">Occupation:</label>
                                        <input type="text" id="occupation" name="occupation" value="<?php echo htmlspecialchars($occupation); ?>">
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
