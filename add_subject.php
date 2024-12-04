<?php
session_start();

include("php/config.php");

// Ensure only admins can access this page
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['submit'])) {
    $subject_code = mysqli_real_escape_string($conn, $_POST['subject_code']);
    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);

    $query = mysqli_query($conn, "INSERT INTO subjects (subject_code, subject_name) VALUES ('$subject_code', '$subject_name')");

    if ($query) {
        echo "<script>alert('Subject added successfully!'); window.location.href = 'add_subject.php'; </script>";
    } else {
        echo "<p class='error'>Error: " . mysqli_error($conn) . "</p>";
    }
}
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
<title>Add Subject</title>
</head>
<body>
    <?php require("php/admin_nav.php"); ?>

    <!-- Main Content -->
    <main>
        <div class="main-box top">
            <div class="box1">
                <p>Add a New Subject</p>
            </div>

            <div class="card-panel">
                <div class="box2">
                <form action="" method="post" class="form-box">
                    <div class="field">
                        <label for="subject_code">Subject Code:</label>
                        <input type="text" name="subject_code" id="subject_code" required>
                    </div>
                    <div class="field">
                        <label for="subject_name">Subject Name:</label>
                        <input type="text" name="subject_name" id="subject_name" required>
                    </div>
                    <div class="field">
                        <button type="submit" name="submit" class="btn">Add Subject</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
