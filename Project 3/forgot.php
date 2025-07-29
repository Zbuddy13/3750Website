<?php
// PHP code for forgot password functionality
session_start();
// Load environment variables from .env file
// This file is expected to be in a secure_data directory outside of public web access
$env = parse_ini_file('/app/secure_data/.env');
// Define the database name for persons
$DB_NAME = "project_three";
// Create connection to database
$conn = new mysqli($env["DB_HOST"], $env["DB_USER"], $env["DB_PASS"], $DB_NAME, $env["DB_PORT"]);
// If the connection fails, kill it and error
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Handle forgot password form submission
$step = isset($_POST['step']) ? $_POST['step'] : 'email';
// For the first step, validate the email and set the security question if found
if ($step === 'email' && $_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize the email and put into variable
    $email = mysqli_real_escape_string($conn, filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
    // Create and run the query
    $sql = "SELECT security_question FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    // If the security question is returned
    if ($result && mysqli_num_rows($result) > 0) {
        // Fetch the user data in an array
        $user = mysqli_fetch_assoc($result);
        // Store the email in the session
        $_SESSION['reset_email'] = $email;
        // Store the security question in the session
        $_SESSION['security_question'] = $user['security_question'];
        // Change the step in order to display the correct html
        $step = 'question';
    } 
    // If no account is found in the database, error
    else {
        $error = "No account found with that email.";
    }
} 
// Second step, check the security answer for the account and see if it matches
elseif ($step === 'question' && $_SERVER["REQUEST_METHOD"] == "POST") {
    // Grab the email from the session, set the answer from html, and store the new password
    $email = $_SESSION['reset_email'];
    $answer = mysqli_real_escape_string($conn, trim($_POST['security_answer']));
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    // If the passwords are empty
    if (empty($answer) || empty($new_password)) {
        $error = "All fields are required.";
    }
    // If the passwords do not match
    elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } 
    // If the password is not at least 8 characters
    elseif (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } 
    // If it passes the above requrements
    else {
        // Create and run query
        $sql = "SELECT security_answer FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);
        // If the security answer matches
        if (password_verify(strtolower($answer), $user['security_answer'])) {
            // Update the db with a hashed password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            // Update the failed attempts and locked counter
            $update_sql = "UPDATE users SET password = '$hashed_password', failed_attempts = 0, is_locked = 0 WHERE email = '$email'";
            // If the query is successful
            if (mysqli_query($conn, $update_sql)) {
                // Unset the email and security question from the session
                unset($_SESSION['reset_email']);
                unset($_SESSION['security_question']);
                // Set the success message and reset to display email entry html
                $success = "Password reset successfully! Please <a href='login.php'>log in</a>.";
                $step = 'email';
            } 
            // If there was a db error
            else {
                $error = "Error resetting password: " . mysqli_error($conn);
            }
        } 
        // If the security answer is incorrect
        else {
            $error = "Incorrect security answer.";
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <!-- Import style and js -->
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="login.css">
    <script type="text/javascript" src="navbar.js"></script>
</head>
<body>
    <!-- Add nav bar -->
    <div id="navbar"></div>
    <!-- Add login body and container -->
    <div class="login-system">
        <div class="login-container">
            <h2>Forgot Password</h2>
            <!-- Display error or success messages if set -->
            <?php if (isset($error)) { ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php } ?>
            <?php if (isset($success)) { ?>
                <p class="success"><?php echo $success; ?></p>
            <?php } ?>
            <!-- If it is set to the initial email entry step -->
            <?php if ($step === 'email') { ?>
                <!-- Submit form to itself -->
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="step" value="email">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group submit-group">
                        <input type="submit" value="Next">
                    </div>
                </form>
            <?php } 
                // If switches to the question step
                elseif ($step === 'question') { ?>
                <!-- Display the form to enter security answer and new password -->
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="step" value="question">
                    <div class="form-group">
                        <label>Security Question:</label>
                        <p><?php echo htmlspecialchars($_SESSION['security_question']); ?></p>
                    </div>
                    <div class="form-group">
                        <label for="security_answer">Security Answer:</label>
                        <input type="text" id="security_answer" name="security_answer" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password:</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="form-group submit-group">
                        <input type="submit" value="Reset Password">
                    </div>
                </form>
                <!-- Add login link to send user to login page -->
            <?php } ?>
            <div class="login-link">
                <p><a href="login.php">Back to Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>