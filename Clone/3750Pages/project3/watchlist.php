<?php
// PHP code to aquire watchlist from the database for a give user
session_start();
// Load environment variables from .env file
// This file is expected to be in a secure_data directory outside of public web access
$env = parse_ini_file('/app/secure_data/.env');
// Define the database name for persons
$DB_NAME = "project_three";
// Create connection
$conn = new mysqli($env["DB_HOST"], $env["DB_USER"], $env["DB_PASS"], $DB_NAME, $env["DB_PORT"]);
// Check if connection failed
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Set the action variable to the request
$action = isset($_GET['action']) ? $_GET['action'] : '';
// If grabbing from database
if ($action === 'get') {
    // Grab user id from session and create query
    $userID = $_SESSION['user_id'];
    $sql = "SELECT itemID, stock_ticker, stock_name, added_at FROM watchlist WHERE userID = ?";
    // Prepare the statement for injection
    $stmt = mysqli_prepare($conn, $sql);
    // Bind the user id
    mysqli_stmt_bind_param($stmt, "i", $userID);
    // Execute the statement
    mysqli_stmt_execute($stmt);
    // Set the results of the statement
    $result = mysqli_stmt_get_result($stmt);
    $watchlist = [];
    // For each row, put into the watchlist array
    while ($row = mysqli_fetch_assoc($result)) {
        $watchlist[] = $row;
    }
    // Json encode the data and send to the search
    echo json_encode($watchlist);
    // Close the statement
    mysqli_stmt_close($stmt);
}
// If we are adding to the database
elseif ($action === 'add') {
    // Read the request body, set the userid, symbol, and description 
    $input = json_decode(file_get_contents('php://input'), true);
    $userID = $_SESSION['user_id'];
    $symbol = mysqli_real_escape_string($conn, $input['symbol']);
    $description = mysqli_real_escape_string($conn, $input['description']);
    // Check for duplicate entries in the db
    $sql = "SELECT itemID FROM watchlist WHERE userID = ? AND stock_ticker = ?";
    // Set the prepared statement
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $userID, $symbol);
    mysqli_stmt_execute($stmt);
    // Get the result of the query
    $result = mysqli_stmt_get_result($stmt);
    // If there are no duplicate entries
    if (mysqli_num_rows($result) == 0) {
        // Insert into the database
        $sql = "INSERT INTO watchlist (userID, stock_ticker, stock_name) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iss", $userID, $symbol, $description);
        // If the statement executes successfuly, set true
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true]);
        } 
        // If there is an error setting the db, error
        else {
            http_response_code(500);
            echo json_encode(['error' => 'Error adding to watchlist']);
        }
    } 
    // Set to success if the entry is already added
    else {
        echo json_encode(['success' => true]); // Already exists, treat as success
    }
    // Close the statement
    mysqli_stmt_close($stmt);
}
// If trying to remove stock entry from db
elseif ($action === 'remove') {
    // Parse the post data
    $input = json_decode(file_get_contents('php://input'), true);
    // Grab the session id and set the stock symbol
    $userID = $_SESSION['user_id'];
    $symbol = mysqli_real_escape_string($conn, $input['symbol']);
    // Prepare the delete query for the db
    $sql = "DELETE FROM watchlist WHERE userID = ? AND stock_ticker = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $userID, $symbol);
    // If the statement is successful, return true
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } 
    // If there is an issue, return error
    else {
        http_response_code(500);
        echo json_encode(['error' => 'Error removing from watchlist']);
    }
    // Close the prepared statement
    mysqli_stmt_close($stmt);
} 
// If the action is not defined, error
else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
}
// Close the connection
mysqli_close($conn);
?>