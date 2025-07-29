<?php
// PHP code for the login functionality
session_start();
// Load environment variables from .env file
// This file is expected to be in a secure_data directory outside of public web access
$env = parse_ini_file('/app/secure_data/.env');
// Define the database name for persons
$DB_NAME = "project_three";
// Start the connection
$conn = new mysqli($env["DB_HOST"], $env["DB_USER"], $env["DB_PASS"], $DB_NAME, $env["DB_PORT"]);
// Check the connection has failed
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Set the email and password from the form
    $email = mysqli_real_escape_string($conn, filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    // Set the sql query and run it
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    // Checks if the query was successful and if a user with that email was found
    if ($result && mysqli_num_rows($result) > 0) {
        // Set the user from the the query
        $user = mysqli_fetch_assoc($result);
        // If the users account is locked direct to forgot password page
        if ($user['is_locked']) {
            $error = "Account is locked. Please <a href='forgot.php'>reset your password</a> to unlock.";
        } 
        // If the users account is not locked
        else {
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Reset failed attempts on successful login
                $login_count = $user['login_count'] + 1;
                $last_login = date('Y-m-d H:i:s');
                // Run the query to update the database
                $update_sql = "UPDATE users SET login_count = $login_count, last_login = '$last_login', failed_attempts = 0, is_locked = 0 WHERE email = '$email'";
                if (mysqli_query($conn, $update_sql)) {
                    // Set cookie for successful login (expires in 30 days)
                    setcookie('user_login', $email, time() + (30 * 24 * 60 * 60), "/");
                    // Set the session user id and username
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    // Direct the user to the main search page
                    header("Location: search.php");
                    exit();
                }
                // If there was an error in the db
                else {
                    $error = "Error updating login info: " . mysqli_error($conn);
                }
            } 
            // If the password attempt is incorrect
            else {
                // Increment failed attempts
                $failed_attempts = $user['failed_attempts'] + 1;
                $is_locked = $failed_attempts >= 3 ? 1 : 0;
                // Update the failed_attempts on the database side
                $update_sql = "UPDATE users SET failed_attempts = $failed_attempts, is_locked = $is_locked WHERE email = '$email'";
                mysqli_query($conn, $update_sql);
                // If the account is locked, prompt reset
                if ($is_locked) {
                    $error = "Account locked due to too many failed attempts. Please <a href='forgot.php'>reset your password</a>.";
                } 
                // If incorrect and not locked, display attempts remaining
                else {
                    $error = "Invalid email or password. Attempts remaining: " . (3 - $failed_attempts);
                }
            }
        }
    } 
    // If error for either email or password
    else {
        $error = "Invalid email or password";
    }
}
// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>P3: User Login</title>
    <!-- Import elements -->
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
            <h2>User Login</h2>
            <!-- If the error is set then display -->
            <?php if (isset($error)) { ?>
                <p class="error"><?php echo $error; ?></p>
            <?php } ?>
            <!-- Display the login container -->
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group submit-group">
                    <input type="submit" value="Login">
                </div>
            </form>
            <!-- Display forgot password link -->
            <div class="forgot-password-link">
                <p><a href="forgot.php">Forgot Password?</a></p>
            </div>
            <!-- Display signup link -->
            <div class="signup-link">
                <p>Don't have an account? <a href="create.php">Create Account</a></p>
            </div>
        </div>
    </div>
</body>
</html>