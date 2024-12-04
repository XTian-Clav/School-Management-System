<?php
session_start();

include("php/config.php");

// Ensure only admins can access this page
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch students excluding admin (ID = 1)
$students = mysqli_query($conn, "SELECT id, CONCAT(fname, ' ', lname) AS fullname FROM users WHERE id != 1");

// Fetch subjects
$subjects = mysqli_query($conn, "SELECT id, subject_name FROM subjects");

if (isset($_POST['submit'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $subject_id = mysqli_real_escape_string($conn, $_POST['subject_id']);
    $grade = mysqli_real_escape_string($conn, $_POST['grade']);

    $query = mysqli_query($conn, "INSERT INTO grades (student_id, subject_id, grade) VALUES ('$student_id', '$subject_id', '$grade')");

    if ($query) {
        echo "<script>alert('Grade added successfully!'); window.location.href = 'add_student.php'; </script>";
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
    <title>Add Grade</title>
</head>
<body>
    <?php require("php/admin_nav.php"); ?>

    <main>
        <div class="main-box top">
            <div class="box1">
                <p>Add a New Grade</p>
            </div>

            <div class="card-panel">
                <div class="box2">
                    <form action="" method="post" class="form-box">
                        <div class="field">
                            <label for="student_id">Student:</label>
                            <select name="student_id" id="student_id" required>
                                <option value="">Select Student</option>
                                <?php while ($student = mysqli_fetch_assoc($students)): ?>
                                    <option value="<?php echo $student['id']; ?>"><?php echo $student['fullname']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="field">
                            <label for="subject_id">Subject:</label>
                            <select name="subject_id" id="subject_id" required>
                                <option value="">Select Subject</option>
                                <?php while ($subject = mysqli_fetch_assoc($subjects)): ?>
                                    <option value="<?php echo $subject['id']; ?>"><?php echo $subject['subject_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="field">
                            <label for="grade">Grade:</label>
                            <input type="number" name="grade" id="grade" required>
                        </div>

                        <div class="field">
                            <button type="submit" name="submit" value="Add Grade" class="btn">Add Grade</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
