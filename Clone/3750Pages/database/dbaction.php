<?php
// Load environment variables from .env file
// This file is expected to be in a secure_data directory outside of public web access
$env = parse_ini_file('/app/secure_data/.env');
// Define the database name for persons
$DB_NAME = "person_db";
// Create connection to mysql
$conn = new mysqli($env["DB_HOST"], $env["DB_USER"], $env["DB_PASS"], '', $env["DB_PORT"]);
// Check connection to make sure it was made
if ($conn->connect_error) {
    header("Location: dbinterface.php?status=error&message=" . urlencode("Connection failed: " . $conn->connect_error));
    exit();
}
// Create database if it doesn't exist already
$conn->query("CREATE DATABASE IF NOT EXISTS $DB_NAME");
$conn->select_db($DB_NAME);
// Create table if it doesn't exist in the database
$sql = "CREATE TABLE IF NOT EXISTS Person (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE
)";
// Check to see if the table was created successfuly, if not error
if (!$conn->query($sql)) {
    header("Location: dbinterface.php?status=error&message=" . urlencode("Table creation failed: " . $conn->error));
    exit();
}
// Handle form submissions by setting the action variable with the hidden value from the form
$action = isset($_POST['action']) ? $_POST['action'] : '';
// If the user request to add a person to the database
if ($action === 'add') {
    // Use escape real string to avoid injection attacks
    // Set the first, last name and email variables passed from dbinterface
    $first_name = $conn->real_escape_string(filter_var($_POST['first_name']));
    $last_name = $conn->real_escape_string(filter_var($_POST['last_name']));
    $email = $conn->real_escape_string(filter_var($_POST['email']));
    // If all these are set
    if ($first_name && $last_name && $email) {
        // Assemble the query to insert into the database
        $sql = "INSERT INTO Person (first_name, last_name, email) VALUES ('$first_name', '$last_name', '$email')";
        // If it was added successfuly
        if ($conn->query($sql)) {
            echo '<div class="alert success">Person added successfully</div>';
        }
        // If there was an error with the input
        else {
            echo '<div class="alert error">Insert failed: ' . htmlspecialchars($conn->error) . '</div>';
        }
    } 
    // If all the elements were not set
    else {
        echo '<div class="alert error">Invalid input data</div>';
    }
    // Include dbinterface.php to display the form and results
    include 'dbinterface.php';
} 
// If the user wants to view all the records in the database
elseif ($action === 'view_all') {
    // Assemble the query
    $sql = "SELECT first_name, last_name, email FROM Person ORDER BY last_name";
    // Set the result variable with the return from the query
    $result = $conn->query($sql);
    $results = [];
    // If there are results
    if ($result) {
        // Put each row in the array results
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
        // Display success at the top of the page
        echo '<div class="alert success">Records retrieved successfully</div>';
        // Pass results to dbinterface.php
        $_GET['results'] = urlencode(json_encode($results));
        include 'dbinterface.php';
    }
    // If the query failed, display at the top of the page
    else {
        echo '<div class="alert error">Query failed: ' . htmlspecialchars($conn->error) . '</div>';
        include 'dbinterface.php';
    }
}
// If the user request a search
elseif ($action === 'search') {
    // Grab the search string from the request making sure to avoid injection
    $search_last_name = $conn->real_escape_string(filter_var($_POST['search_last_name']));
    // If the search term is not empty
    if ($search_last_name) {
        // Assemble the query
        $sql = "SELECT first_name, last_name, email FROM Person WHERE LOWER(last_name) LIKE LOWER('%$search_last_name%')";
        // Set the result variable to the query return
        $result = $conn->query($sql);
        $results = [];
        // If there is a result, put each row into the array results
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            // Echo the success alert
            echo '<div class="alert success">Search completed successfully</div>';
            // Pass results to dbinterface.php
            $_GET['results'] = urlencode(json_encode($results));
            include 'dbinterface.php';
        }
        // If there was an issue with the query
        else {
            echo '<div class="alert error">Search failed: ' . htmlspecialchars($conn->error) . '</div>';
            include 'dbinterface.php';
        }
    }
    // If the user submitted an empty search 
    else {
        echo '<div class="alert error">Please enter a last name to search</div>';
        include 'dbinterface.php';
    }
} 
// If the user submits an invalid action
else {
    echo '<div class="alert error">Invalid action</div>';
    include 'dbinterface.php';
}
// Close the connection after done with query
$conn->close();
?>