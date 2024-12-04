<?php
session_start();

include("php/config.php");

// Ensure only admins can access this page
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Get subject ID from query string
$subject_id = $_GET['id'] ?? null;

// Fetch subject details for editing
if ($subject_id) {
    $stmt = $conn->prepare("SELECT subject_code, subject_name FROM subjects WHERE id = ?");
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $subject = $result->fetch_assoc();
    $stmt->close();

    if (!$subject) {
        echo "<script>alert('Subject not found!'); window.location.href = 'manage_subject.php'; </script>";
        exit();
    }
} else {
    echo "<script>alert('Invalid subject ID!'); window.location.href = 'manage_subject.php'; </script>";
    exit();
}

// Update subject details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_code = mysqli_real_escape_string($conn, $_POST['subject_code']);
    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);

    $stmt = $conn->prepare("UPDATE subjects SET subject_code = ?, subject_name = ? WHERE id = ?");
    $stmt->bind_param("ssi", $subject_code, $subject_name, $subject_id);

    if ($stmt->execute()) {
        echo "<script>alert('Subject updated successfully!'); window.location.href = 'manage_subject.php'; </script>";
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
<title>Edit Subject</title>
</head>
<body>
    <?php require("php/admin_nav.php"); ?>

    <!-- Main Content -->
    <main>
        <div class="main-box top">
            <div class="box1">
                <p>Edit Subject</p>
            </div>

            <div class="card-panel">
                <div class="box2">
                <form action="" method="post" class="form-box">
                    <div class="field">
                        <label for="subject_code">Subject Code:</label>
                        <input type="text" name="subject_code" id="subject_code" value="<?= htmlspecialchars($subject['subject_code']) ?>" required>
                    </div>
                    <div class="field">
                        <label for="subject_name">Subject Name:</label>
                        <input type="text" name="subject_name" id="subject_name" value="<?= htmlspecialchars($subject['subject_name']) ?>" required>
                    </div>
                    <div class="field">
                        <button type="submit" name="submit" class="btn">Update Subject</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
