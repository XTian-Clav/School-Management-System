<?php
session_start();

include("php/config.php");

// Ensure only admins can access this page
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Get grade ID from query string
$grade_id = $_GET['id'] ?? null;

// Fetch grade details for editing
if ($grade_id) {
    $stmt = $conn->prepare("
        SELECT g.id, g.grade, s.subject_name, CONCAT(u.fname, ' ', u.lname) AS student_name
        FROM grades g
        JOIN subjects s ON g.subject_id = s.id
        JOIN users u ON g.student_id = u.id
        WHERE g.id = ?
    ");
    $stmt->bind_param("i", $grade_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $grade = $result->fetch_assoc();
    $stmt->close();

    if (!$grade) {
        echo "<script>alert('Grade record not found!'); window.location.href = 'manage_grade.php'; </script>";
        exit();
    }
} else {
    echo "<script>alert('Invalid grade ID!'); window.location.href = 'manage_grade.php'; </script>";
    exit();
}

// Update grade details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_grade = mysqli_real_escape_string($conn, $_POST['grade']);

    $stmt = $conn->prepare("UPDATE grades SET grade = ? WHERE id = ?");
    $stmt->bind_param("si", $new_grade, $grade_id);

    if ($stmt->execute()) {
        echo "<script>alert('Grade updated successfully!'); window.location.href = 'manage_grade.php'; </script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
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
<title>Edit Grade</title>
</head>
<body>
    <?php require("php/admin_nav.php"); ?>

    <!-- Main Content -->
    <main>
        <div class="main-box top">
            <div class="box1">
                <p>Edit Grade</p>
            </div>

            <div class="card-panel">
                <div class="box2">
                    <form action="" method="post" class="form-box">
                        <div class="field">
                            <label>Student Name:</label>
                            <input type="text" value="<?= htmlspecialchars($grade['student_name']) ?>" disabled>
                        </div>
                        <div class="field">
                            <label>Subject Name:</label>
                            <input type="text" value="<?= htmlspecialchars($grade['subject_name']) ?>" disabled>
                        </div>
                        <div class="field">
                            <label for="grade">Grade:</label>
                            <input type="text" name="grade" id="grade" value="<?= htmlspecialchars($grade['grade']) ?>" required>
                        </div>
                        <div class="field">
                            <button type="submit" name="submit" class="btn">Update Grade</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
