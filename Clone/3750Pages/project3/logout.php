<?php
// PHP code for the log out functionality
session_start();
// Unset all session variables
$_SESSION = array();
// Destroy the session
session_destroy();
// Remove the cookie
setcookie('user_login', '', time() - 3600, "/");
// Redirect to login page
header("Location: login.php");
exit();
?>