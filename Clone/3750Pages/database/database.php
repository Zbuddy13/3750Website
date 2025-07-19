<?php
$env = parse_ini_file('/app/secure_data/.env');
//print_r($env);
$mysqli = new mysqli($env["DB_HOST"], $env["DB_USER"], $env["DB_PASS"], $env["DB_NAME"], $env["DB_PORT"]);
if ($mysqli->connect_errno) {
    // Using connect_error for the error message
    die("Connection failed: " . $mysqli->connect_error);
}
echo "Successfully connected to MySQL using MySQLi";
$mysqli->close();
?>
