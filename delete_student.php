<?php
session_start();

include("php/config.php");

// Ensure only admins can access this page
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Get the ID of the user to delete
$id = $_GET['id'];

// Prevent deletion of admin accounts
$query = mysqli_prepare($conn, "DELETE FROM users WHERE id = ? AND user_type != ?");
$user_type = 'admin';
mysqli_stmt_bind_param($query, "is", $id, $user_type);

if (mysqli_stmt_execute($query)) {
    // Check if a row was actually deleted
    if (mysqli_stmt_affected_rows($query) > 0) {
        echo "<script>
                alert('User record deleted successfully.');
                window.location.href = 'manage_student.php';
              </script>";
    } else {
        echo "<script>
                alert('No record deleted. The user might not exist or is an admin.');
                window.location.href = 'manage_student.php';
              </script>";
    }
} else {
    echo "<script>
            alert('Error deleting record: " . mysqli_error($conn) . "');
            window.location.href = 'manage_student.php';
          </script>";
}

mysqli_stmt_close($query);
?>