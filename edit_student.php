<?php
session_start();

include("php/config.php");

// Ensure only admins can access this page
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Get student ID from query string
$student_id = $_GET['id'] ?? null;

// Fetch student details for editing
if ($student_id) {
    $stmt = $conn->prepare("SELECT username, fname, lname, email, contact, password FROM users WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();

    if (!$student) {
        echo "<script>alert('Student not found!'); window.location.href = 'manage_student.php'; </script>";
        exit();
    }
} else {
    echo "<script>alert('Invalid student ID!'); window.location.href = 'manage_student.php'; </script>";
    exit();
}

// Update student details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uname = mysqli_real_escape_string($conn, $_POST['uname']);
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $stmt = $conn->prepare("UPDATE users SET username = ?, fname = ?, lname = ?, email = ?, contact = ?, password = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $uname, $fname, $lname, $email, $contact, $password, $student_id);

    if ($stmt->execute()) {
        echo "<script>alert('Student updated successfully!'); window.location.href = 'manage_student.php'; </script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/css/all.min.css">
    <link rel="stylesheet" href="style/css/fontawesome.min.css">
    <title>Edit Student</title>
</head>
<body>
    <?php require("php/admin_nav.php"); ?>

    <main>
        <div class="main-box top">
            <div class="box1">
                <p>Edit Student</p>
            </div>

            <div class="card-panel">
                <div class="box2">
                    <form action="" method="post" class="form-box">
                        <div class="field">
                            <label for="uname">Username:</label>
                            <input type="text" name="uname" id="uname" value="<?= htmlspecialchars($student['username']) ?>" required>
                        </div>

                        <div class="field">
                            <label for="fname">First Name:</label>
                            <input type="text" name="fname" id="fname" value="<?= htmlspecialchars($student['fname']) ?>" required>
                        </div>

                        <div class="field">
                            <label for="lname">Last Name:</label>
                            <input type="text" name="lname" id="lname" value="<?= htmlspecialchars($student['lname']) ?>" required>
                        </div>

                        <div class="field">
                            <label for="email">Email:</label>
                            <input type="email" name="email" id="email" value="<?= htmlspecialchars($student['email']) ?>" required>
                        </div>

                        <div class="field">
                            <label for="contact">Contact:</label>
                            <input type="text" name="contact" id="contact" value="<?= htmlspecialchars($student['contact']) ?>" required>
                        </div>

                        <div class="field">
                            <label for="password">Password:</label>
                            <input type="password" name="password" id="password" value="<?= htmlspecialchars($student['password']) ?>" required>
                        </div>

                        <div class="field">
                            <button type="submit" name="submit" class="btn">Update Student</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
