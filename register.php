<?php
session_start();

include("php/config.php");

$username = $fname = $lname = $email = $contact = "";

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $cpassword = mysqli_real_escape_string($conn, $_POST['cpassword']);
    $user_type = $_POST['user_type'];

    $select = "SELECT * FROM users WHERE email = '$email' OR username = '$username'"; // Check if username or email already exists
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $error_message = "User already exists with this email or username!";
    } else {
        if ($password != $cpassword) {
            $error_message = "Passwords do not match!";
        } else {
            // Insert the new user into the database
            $insert = "INSERT INTO users (username, fname, lname, email, contact, password, user_type) 
                       VALUES('$username', '$fname', '$lname', '$email', '$contact', '$password', '$user_type')";
            if (mysqli_query($conn, $insert)) {
                $_SESSION['success_message'] = "Registration successful! You can now log in.";
                header('location:login.php');
                exit;
            } else {
                $error_message = "An error occurred while registering. Please try again.";
            }
        }
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
    <title>Register</title>
</head>
<body>
<div class="container">
        <div class="box form-box">
            <header class="header">REGISTER</header>
            <form action="" method="post">
                <!-- Display Error Message -->
                <?php if (isset($error_message)) { ?>
                    <div class="message error"><?php echo $error_message; ?></div>
                <?php } ?>

                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Enter Username"
                        pattern="[A-Za-z0-9\s]{4,50}" value="<?php echo htmlspecialchars($username); ?>" autocomplete="off" required>

                    <label for="fname">Firstname</label>
                    <input type="text" name="fname" id="fname" placeholder="Enter Firstname"
                        pattern="[A-Za-z\s]{2,50}" value="<?php echo htmlspecialchars($fname); ?>" autocomplete="off" required>

                    <label for="lname">Lastname</label>
                    <input type="text" name="lname" id="lname" placeholder="Enter Lastname"
                        pattern="[A-Za-z\s]{2,50}" value="<?php echo htmlspecialchars($lname); ?>" autocomplete="off" required>

                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="Enter Email"
                        title="Needs an acceptable email" value="<?php echo htmlspecialchars($email); ?>" autocomplete="off" required>

                    <label for="contact">Contact</label>
                    <input type="tel" name="contact" id="contact" placeholder="09xx-xxx-xxxx"
                        pattern="[0][9][0-9]{9}" title="Contact number should start at 09 and must be 11 numbers"
                        value="<?php echo htmlspecialchars($contact); ?>" autocomplete="off" required>

                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter Password"
                        pattern="[A-Za-z0-9?=*.!@$%^&(){}:;<>,.?/~_+-=|]{8,}" minlength="8"
                        title="Password should be at least 8 characters" autocomplete="off" required>

                    <label for="cpassword">Confirm Password</label>
                    <input type="password" name="cpassword" id="cpassword" placeholder="Confirm Password"
                        pattern="[A-Za-z0-9?=*.!@$%^&(){}:;<>,.?/~_+-=|]{8,}" minlength="8"
                        title="Password should be at least 8 characters" autocomplete="off" required>

                    <select name="user_type">
                        <option value="student">Student</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Register">
                </div>

                <div class="links">
                    Already have an account? <a href="login.php">Login</a>
                </div>

                <div class="links">
                    <a href="landing.php">Go to home</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
