<?php
session_start();

include("php/config.php");

// Ensure only students can access this page
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

// Query to fetch subjects with grades for the logged-in student
$sql = "SELECT DISTINCT s.subject_code, s.subject_name 
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
    <title>Subjects</title>
</head>
<body>
    <?php require("php/nav.php"); ?>

    <main>
        <div class="main-box top">
            <!-- Display subjects -->
            <div class="box1">
                <p>Enrolled Subjects</p>
            </div>
            <div class="card-panel">
                <div class="grid-cards">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="card">';
                            echo '<h5>' . htmlspecialchars($row["subject_code"]) . '</h5>';
                            echo '<h3>' . htmlspecialchars($row["subject_name"]) . '</h3>';
                            echo '<button>View Details</button>';
                            echo '</div>';
                        }
                    } else {
                        echo "<p>No subjects with grades found.</p>";
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
