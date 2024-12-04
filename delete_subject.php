<?php
session_start();

include("php/config.php");

// Ensure only admins can access this page
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_subject.php");
    exit();
}

$id = $_GET['id'];

// Delete the subject record
$query = mysqli_prepare($conn, "DELETE FROM subjects WHERE id = ?");
mysqli_stmt_bind_param($query, "i", $id);

if (mysqli_stmt_execute($query)) {
    echo "<script>
            alert('Subject record deleted successfully.');
            window.location.href = 'manage_subject.php';
          </script>";
} else {
    echo "<script>
            alert('Error deleting record: " . mysqli_error($conn) . "');
            window.location.href = 'manage_subject.php';
          </script>";
}

mysqli_stmt_close($query);
?>
