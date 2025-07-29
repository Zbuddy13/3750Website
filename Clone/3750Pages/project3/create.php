<?php
// PHP code to create a user account for Project 3
session_start();
// Load environment variables from .env file
// This file is expected to be in a secure_data directory outside of public web access
$env = parse_ini_file('/app/secure_data/.env');
// Define the database name for persons
$DB_NAME = "project_three";
// Establish connection to the database
$conn = new mysqli($env["DB_HOST"], $env["DB_USER"], $env["DB_PASS"], $DB_NAME, $env["DB_PORT"]);
// Test connection and kill if error
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Handle form submission for creating a user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Grab all form elements, escape them so they cannot be used for attack, and set them to variables
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $security_question = mysqli_real_escape_string($conn, trim($_POST['security_question']));
    $security_answer = mysqli_real_escape_string($conn, trim($_POST['security_answer']));
    // Check to make sure all fields are filled
    if (empty($name) || empty($email) || empty($password) || empty($security_question) || empty($security_answer)) {
        $error = "All fields are required.";
    } 
    // Make sure the email is in email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } 
    // Make sure the passwords match
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } 
    // Make sure password is of length 8
    elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    }
    // If all passes clear
    else {
        // Check if email already exists
        $sql = "SELECT id FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        // If the email exist, return and error
        if ($result && mysqli_num_rows($result) > 0) {
            $error = "Email already registered.";
        } 
        // If everything checks out, create the account in the database
        else {
            // Hash the password and answer and timestamp the creation
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $hashed_answer = password_hash(strtolower($security_answer), PASSWORD_DEFAULT);
            $created_at = date('Y-m-d H:i:s');
            // Create the sql insert string
            $insert_sql = "INSERT INTO users (name, email, password, created_at, login_count, last_login, failed_attempts, is_locked, security_question, security_answer) 
                          VALUES ('$name', '$email', '$hashed_password', '$created_at', 0, NULL, 0, 0, '$security_question', '$hashed_answer')";
            // Attempt to insert user
            if (mysqli_query($conn, $insert_sql)) {
                $success = "Account created successfully! Please <a href='login.php'>log in</a>.";
            } 
            // If insertion fails, return error
            else {
                $error = "Error creating account: " . mysqli_error($conn);
            }
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
    <title>P3: Create Account</title>
    <!-- Import necessary elements -->
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="login.css">
    <script type="text/javascript" src="navbar.js"></script>
</head>
<body>
    <!-- Add nav bar -->
    <div id="navbar"></div>
    <div class="login-system">
        <div class="login-container">
            <h2>Create Account</h2>
            <!-- If there is an error durring account creation display at top -->
            <?php if (isset($error)) { ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php } ?>
            <!-- If account succeeds, display login prompt at top -->
            <?php if (isset($success)) { ?>
                <p class="success"><?php echo $success; ?></p>
            <?php } ?>
            <!-- Create the form to be used to create a new account -->
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <label for="security_question">Security Question:</label>
                    <select id="security_question" name="security_question" required>
                        <option value="">Select a question</option>
                        <option value="What is the name of your first pet?">What is the name of your first pet?</option>
                        <option value="What was your childhood nickname?">What was your childhood nickname?</option>
                        <option value="Where were you born?">Where were you born?</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="security_answer">Security Answer:</label>
                    <input type="text" id="security_answer" name="security_answer" required>
                </div>
                <div class="form-group submit-group">
                    <input type="submit" value="Sign Up">
                </div>
            </form>
            <!-- If the user already has an account, offer link to login -->
            <div class="login-link">
                <p>Already have an account? <a href="login.php">Log in</a></p>
            </div>
        </div>
    </div>
</body>
</html>