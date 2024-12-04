<?php
session_start();

include("php/config.php");

// Ensure only students can access this page
if (!isset($_SESSION['student'])) {
    header("Location: login.php");
    exit();
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
    <title>College Dashboard</title>
</head>
<body>
    <?php require("php/nav.php"); ?>

    <main>
        <div class="main-box top">
            <div class="box1">
                <p>Colleges</p>
            </div>

            <div class="card-panel">
                <div class="grid-cards">
                    <?php
                    // Example subjects assigned to the faculty - replace with dynamic data if needed
                    $faculty_members = [
                        ["name" => "College of Arts and Humanities"],
                        ["name" => "College of Sciences"],
                        ["name" => "College of Teacher Education"],
                        ["name" => "College of Criminal Justice Education"],
                        ["name" => "College of Engineering Architecture and Technology"],
                        ["name" => "College of Hospitality Management and Tourism"],
                        ["name" => "College of Business and Accountancy", "Members"],
                        ["name" => "College of Nursing and Health Sciences"],
                    ];

                    foreach ($faculty_members as $faculty) {
                        echo '<div class="card">';
                        echo '<h4>' . $faculty["name"] . '</h4>';
                        echo '<button>View Details</button>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
