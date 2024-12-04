<?php
session_start();

include("php/config.php");

if (isset($_SESSION['success_message'])) {
    echo "<div class='success-message'>" . $_SESSION['success_message'] . "</div>";
    unset($_SESSION['success_message']);
}

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $select = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);

        if ($row['user_type'] == 'admin') {
            $_SESSION['admin'] = $row['username']; // Set admin session
            header('location:admin_home.php');
            exit;
        } elseif ($row['user_type'] == 'student') {
            $_SESSION['student'] = $row['username']; // Set student session
            header('location:home.php');
            exit;
        }
    } else {
        $error[] = 'Incorrect username or password!';
    }
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
    <title>Login</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header class="header">LOGIN</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Enter your Username" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter Password" autocomplete="off" required>
                </div>

                <div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="login">
                </div>

                <div class="links">
                        Don't have an account? <a href="register.php">Register</a>
                    </div>

                    <div class="links">
                        <a href="landing.php">Go to home</a>
                    </div>
            </form>
        </div>
    </div>
</body>
</html>