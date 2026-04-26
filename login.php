<?php
// =============================================
// login.php - Login Page
// Users (admin/teacher) log in here.
// PHP checks username & password against DB.
// =============================================
 
session_start();       // Start a session to remember the logged-in user
include 'db.php';      // Include database connection
 
// Prevent back button and browser caching
header("Cache-Control: no-cache, no-store, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

$error = "";           // Will hold any error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
    // Get the submitted username and password
    // mysqli_real_escape_string prevents SQL injection
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
 
    // Query the users table for matching credentials
    $sql    = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $sql);
 
    if (mysqli_num_rows($result) == 1) {
        // Login success - save user info in session
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role']      = $user['role'];
 
        // Redirect to main page
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Records</title>
    <link rel="stylesheet" href="styles.css">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="login-page">
 
<div class="login-container">
    <h2>Student Record System</h2>
    <h3> Admin Login</h3>
 
    <!-- Show error message if login failed -->
    <?php if ($error): ?>
        <div class="alert error" id="loginError"><?= $error ?></div>
    <?php endif; ?>
 
    <form id="loginForm" action="login.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter username" required>
            <span class="field-error" id="usernameError"></span>
        </div>
 
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>
            <span class="field-error" id="passwordError"></span>
        </div>
 
        <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>
 
    <p class="hint">Demo: admin / admin123</p>
</div>
 
<script src="script.js"></script>
<script>
    // ---- jQuery: Validate login form before submitting ----
    $("#loginForm").on("submit", function(e) {
        let valid = true;
 
        // Clear previous errors first
        $(".field-error").text("");
 
        // Check username field
        if ($("#username").val().trim() === "") {
            $("#usernameError").text("Username is required.");
            valid = false;
        }
 
        // Check password field
        if ($("#password").val().trim() === "") {
            $("#passwordError").text("Password is required.");
            valid = false;
        }
 
        // If invalid, stop form from submitting
        if (!valid) {
            e.preventDefault();
        }
    });
 
    // ---- jQuery: Fade in the login box on page load ----
    $(".login-container").hide().fadeIn(600);
 
    // ---- jQuery: Auto-hide error alert after 3 seconds ----
    $("#loginError").delay(3000).fadeOut(500);
</script>
</body>
</html>