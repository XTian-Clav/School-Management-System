<?php
session_start(); // Make sure this is the first line of the PHP script

include("php/config.php");

// Ensure only students can access this page
if (!isset($_SESSION['student'])) {
    header("Location: login.php");
    exit();
}

$searchResults = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get search input
    $searchQuery = isset($_POST['search_query']) ? "%" . $_POST['search_query'] . "%" : "%";

    // Build the base query with exclusion of admin
    $query = "
    SELECT 
        u.username, u.fname, u.lname, u.user_type
    FROM users u
    WHERE (u.username LIKE ? OR u.fname LIKE ? OR u.lname LIKE ?)
    AND u.user_type != ?
    ";

    // Prepare and bind the query
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt === false) {
        die("Error preparing statement: " . mysqli_error($conn));
    }

    // Bind the parameters
    $adminType = 'admin';
    mysqli_stmt_bind_param($stmt, "ssss", $searchQuery, $searchQuery, $searchQuery, $adminType);

    // Execute the query
    if (mysqli_stmt_execute($stmt)) {
        // Fetch results
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $searchResults[] = $row;
        }
    } else {
        echo "Error executing query: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/css/all.min.css">
    <title>Search</title>
</head>
<body>
    <?php require("php/nav.php"); ?>

    <main>
        <div class="main-box top">
            <div class="box1">
                <p>Search Students</p>
            </div>

            <div class="card-panel">
                <form method="POST" action="search.php">
                    <input type="text" name="search_query" placeholder="Enter Student Name" required class="search-field">
                    <button type="submit" class="search-btn"> Search</button>
                </form>

                <?php if (!empty($searchResults)) { ?>
                    <div class="table-container">
                        <table class="table styled-table">
                            <thead>
                                <tr class="table-header">
                                    <th>Username</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($searchResults as $result) { ?>
                                    <tr class="table-row">
                                        <td><?php echo htmlspecialchars($result['username']); ?></td>
                                        <td><?php echo htmlspecialchars($result['fname']); ?></td>
                                        <td><?php echo htmlspecialchars($result['lname']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') { ?>
                    <p class='message'>No results found for your search.</p>
                <?php } ?>
            </div>
        </div>
    </main>
</body>
</html>
