<?php
// Start output buffering to prevent errors with FPDF output
ob_start();

// Start the session and include necessary files
session_start();
require('php/fpdf.php');
include("php/config.php");

// Ensure the user is logged in as an admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch all students (excluding admins) for selection
$sql = "SELECT id, fname, lname FROM users WHERE user_type != 'admin'";
$studentsResult = $conn->query($sql);

// Handle form submission to generate the report card
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];

    // Fetch student details
    $sql = "SELECT id, fname, lname, email, contact FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($student_id, $fname, $lname, $student_email, $student_contact);
    $stmt->fetch();
    $stmt->close();

    if (!$student_id) {
        echo "Error: Could not retrieve student details.";
        exit();
    }

    // Combine first name and last name into a single variable for the full name
    $student_name = $fname . ' ' . $lname;

    // Fetch academic info
    $sql = "SELECT college, course, year_level FROM academic_info WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($student_college, $student_course, $student_year_level);
    $stmt->fetch();
    $stmt->close();

    // Fetch grades and subject names
    $sql = "SELECT g.grade, s.subject_name
            FROM grades g
            INNER JOIN subjects s ON g.subject_id = s.id
            WHERE g.student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(true, 20);

    // Page Header
    $font = 'Courier';
    $pdf->SetFont($font, 'B', 20);
    $pdf->SetTextColor(0, 51, 102); // Navy blue
    $pdf->Cell(0, 10, 'STUDENT REPORT CARD', 0, 1, 'C');
    $pdf->Ln(10);

    // Student Info Section
    $pdf->SetFont($font, 'B', 12);
    $pdf->SetTextColor(0, 0, 0); // Black
    $pdf->Cell(0, 10, 'Student Information', 0, 1, 'L');
    $pdf->Ln(2);

    $pdf->SetFont($font, '', 12);
    $pdf->Cell(50, 10, 'Student ID:', 0, 0);
    $pdf->Cell(100, 10, htmlspecialchars($student_id), 0, 1);

    $pdf->Cell(50, 10, 'Name:', 0, 0);
    $pdf->Cell(100, 10, htmlspecialchars($student_name), 0, 1);

    $pdf->Cell(50, 10, 'Email:', 0, 0);
    $pdf->Cell(100, 10, htmlspecialchars($student_email), 0, 1);

    $pdf->Cell(50, 10, 'Contact Number:', 0, 0);
    $pdf->Cell(100, 10, htmlspecialchars($student_contact), 0, 1);

    $pdf->Ln(2);

    // Academic Info
    $pdf->SetFont($font, 'B', 12);
    $pdf->Cell(0, 10, 'Academic Information', 0, 1, 'L');
    $pdf->Ln(2);

    $pdf->SetFont($font, '', 12);
    $pdf->Cell(50, 10, 'College:', 0, 0);
    $pdf->Cell(100, 10, htmlspecialchars($student_college), 0, 1);

    $pdf->Cell(50, 10, 'Course:', 0, 0);
    $pdf->Cell(100, 10, htmlspecialchars($student_course), 0, 1);

    $pdf->Cell(50, 10, 'Year Level:', 0, 0);
    $pdf->Cell(100, 10, htmlspecialchars($student_year_level), 0, 1);

    $pdf->Ln(15);

    // Grades Table
    $subjectCellWidth = 150;
    $gradeCellWidth = 40;

    $pdf->SetFont($font, 'B', 14);
    $pdf->SetTextColor(0, 51, 102);
    $pdf->Cell(0, 10, 'GRADES SUMMARY', 0, 1, 'C');
    $pdf->Ln(5);

    // Table Header
    $pdf->SetFont($font, 'B', 12);
    $pdf->SetFillColor(0, 51, 102);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell($subjectCellWidth, 10, 'Subject', 1, 0, 'C', true);
    $pdf->Cell($gradeCellWidth, 10, 'Grade', 1, 1, 'C', true);

    // Initialize variables for calculating the average
    $totalGrades = 0;
    $gradeCount = 0;

    // Table Data
    $pdf->SetFont($font, '', 12);
    $pdf->SetTextColor(0, 0, 0);
    $lightBlue = [240, 250, 255];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pdf->SetFillColor($lightBlue[0], $lightBlue[1], $lightBlue[2]);
            $pdf->Cell($subjectCellWidth, 10, htmlspecialchars($row["subject_name"]), 1, 0, 'C', true);
            $pdf->Cell($gradeCellWidth, 10, htmlspecialchars($row["grade"]), 1, 1, 'C', true);

            // Add the grade to the total and increment the count
            $totalGrades += $row["grade"];
            $gradeCount++;
        }

        // Calculate the average
        $averageGrade = $totalGrades / $gradeCount;

        // Display the average below the table
        $pdf->Ln(5);
        $pdf->SetFont($font, 'B', 12);
        $pdf->Cell($subjectCellWidth, 10, 'Average Grade', 1, 0, 'C', true);
        $pdf->Cell($gradeCellWidth, 10, number_format($averageGrade, 2), 1, 1, 'C', true);
    } else {
        $pdf->Cell($subjectCellWidth + $gradeCellWidth, 10, 'No subjects found', 1, 1, 'C', false);
    }

    // Footer
    $pdf->SetY(-30);
    $pdf->SetFont($font, 'I', 10);
    $pdf->SetTextColor(150, 150, 150);
    $pdf->Cell(0, 10, 'Generated on ' . date('Y-m-d H:i:s'), 0, 0, 'C');

    // Output PDF and clear buffer
    ob_end_clean();
    $pdf->Output('I', 'Student_COG.pdf');
    exit();
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
    <title>Generate Report Card</title>
</head>
<body>
    <?php require("php/admin_nav.php"); ?>

    <main>
        <div class="main-box top">
            <div class="box1">
                <p>Generate Report Card</p>
            </div>

            <div class="card-panel">
                <div class="box2">
                    <form action="" method="post" class="form-box">
                        <div class="field">
                            <label for="student_id">Select Student:</label>
                            <select name="student_id" id="student_id" required>
                                <?php while ($row = $studentsResult->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>">
                                        <?php echo htmlspecialchars($row['fname'] . ' ' . $row['lname']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="field">
                            <button type="submit" class="btn">Generate COG</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>

