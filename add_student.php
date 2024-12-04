<?php
session_start();

include("php/config.php");

// Ensure only admins can access this page
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['submit'])) {
    $uname = mysqli_real_escape_string($conn, $_POST['uname']);
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Use prepared statement for INSERT
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, fname, lname, email, contact, password) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssss", $uname, $fname, $lname, $email, $contact, $password);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Student added successfully!'); window.location.href = 'add_student.php'; </script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: " . mysqli_error($conn);
    }
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
    <title>Add Student</title>
</head>
<body>
    <?php require("php/admin_nav.php"); ?>

    <main>
        <div class="main-box top">
            <div class="box1">
                <p>Add Student</p>
            </div>

            <div class="card-panel">
                <div class="box2">
                    <form action="" method="post" class="form-box">
                        <div class="field">
                            <label for="uname">Username:</label>
                            <input type="text" name="uname" id="uname" required>
                        </div>

                        <div class="field">
                            <label for="fname">First Name:</label>
                            <input type="text" name="fname" id="fname" required>
                        </div>

                        <div class="field">
                            <label for="lname">Last Name:</label>
                            <input type="text" name="lname" id="lname" required>
                        </div>

                        <div class="field">
                            <label for="email">Email:</label>
                            <input type="email" name="email" id="email" required>
                        </div>

                        <div class="field">
                            <label for="contact">Contact:</label>
                            <input type="text" name="contact" id="contact" required>
                        </div>

                        <div class="field">
                            <label for="password">Password:</label>
                            <input type="password" name="password" id="password" required>
                        </div>

                        <div class="field">
                            <button type="submit" name="submit" value="Add Student" class="btn">Add Student</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
