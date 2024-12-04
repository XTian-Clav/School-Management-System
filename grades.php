<?php
session_start();
include("php/config.php");

// Ensure the user is logged in as a student
if (!isset($_SESSION['student'])) {
    header("Location: login.php");
    exit();
}

// Fetch the student ID from the database using the username in the session
$username = $_SESSION['student'];
$sql = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($student_id);
$stmt->fetch();
$stmt->close();

// Ensure the student ID was retrieved
if (!$student_id) {
    echo "Error: Could not retrieve student ID.";
    exit();
}

// Query to fetch grades, subject names, and subject codes for the logged-in student
$sql = "SELECT g.grade, s.subject_name, s.subject_code 
        FROM grades g
        INNER JOIN subjects s ON g.subject_id = s.id
        WHERE g.student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
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
    <title>Grades Dashboard</title>
</head>
<body>
    <?php require("php/nav.php"); ?>

<main>
    <div class="main-box top">
        <div class="box1">
            <p>Grades</p>
        </div>
        <div class="card-panel">
            <div class="grid-cards">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="card">';
                        echo '<h5> ' . htmlspecialchars($row["subject_code"]) . ' </h5>';
                        echo '<h3> ' . htmlspecialchars($row["subject_name"]) . ' </h3>';
                        echo '<button>Grade: ' . htmlspecialchars($row["grade"]) . '</button>';
                        echo '</div>';
                    }
                } else {
                    echo "<p>No grades found. Add a new one!</p>";
                }
                ?>
            </div>
        </div>
    </div>
</main>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
