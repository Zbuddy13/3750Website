<?php
// PHP code for the login functionality
session_start();
// Load environment variables from .env file
// This file is expected to be in a secure_data directory outside of public web access
$env = parse_ini_file('/app/secure_data/.env');
// Define the database name for persons
$DB_NAME = "project_three";
// Create the mysqli connection
$conn = new mysqli($env["DB_HOST"], $env["DB_USER"], $env["DB_PASS"], $DB_NAME, $env["DB_PORT"]);
// Check to see if the connection was successful, if not error
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Check if user is logged in and is an admin (disabled)
// if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
//     header("Location: login_system.php");
//     exit();
// }
// Fetch all users
$sql_users = "SELECT id, name, email, created_at, last_login, login_count, failed_attempts, is_locked FROM users";
$result_users = mysqli_query($conn, $sql_users);
$users = [];
// If there are users returned by the query
if ($result_users) {
    // Loops through each row of the result set and adds it to the $users array
    while ($row = mysqli_fetch_assoc($result_users)) {
        $users[] = $row;
    }
}
// Fetch global watchlist with usernames of each user
$sql_watchlist = "SELECT w.itemID, w.userID, w.stock_ticker, w.stock_name, w.added_at, u.name AS username 
                 FROM watchlist w 
                 JOIN users u ON w.userID = u.id 
                 ORDER BY w.added_at DESC";
$result_watchlist = mysqli_query($conn, $sql_watchlist);
$watchlist = [];
// Checks if the query for watchlist was successful
if ($result_watchlist) {
    // Loops through each row of the result set and adds it to the $watchlist array as an associative array
    while ($row = mysqli_fetch_assoc($result_watchlist)) {
        $watchlist[] = $row;
    }
}
// Close the connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>P3: Admin Dashboard</title>
    <!-- Import necessary files -->
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="login.css">
    <script type="text/javascript" src="navbar.js"></script>
</head>
<body>
    <!-- Add nav bar -->
    <div id="navbar"></div>
    <!-- Create the admin body and container -->
    <div class="admin">
        <div class="admin-container">
            <h1 style="text-align: center;">Admin Dashboard</h1>
            <!-- First section to display users -->
            <div class="admin-section">
                <h2>Users</h2>
                <!-- If there are no users, display none found -->
                <?php if (empty($users)) { ?>
                    <p class="no-data">No users found</p>
                <?php } 
                // If there are users found display them in table format
                else { ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Created At</th>
                                <th>Last Login</th>
                                <th>Login Count</th>
                                <th>Failed Attempts</th>
                                <th>Locked</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- For each of the users in the users table, display each record -->
                            <?php foreach ($users as $user) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                    <td><?php echo htmlspecialchars($user['last_login'] ?? 'Never'); ?></td>
                                    <td><?php echo htmlspecialchars($user['login_count']); ?></td>
                                    <td><?php echo htmlspecialchars($user['failed_attempts']); ?></td>
                                    <td><?php echo $user['is_locked'] ? 'Yes' : 'No'; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
            <!-- Second section to display watchlist/favorites -->
            <div class="admin-section">
                <h2>Stock Watchlist</h2>
                <!-- If no records are found display none found -->
                <?php if (empty($watchlist)) { ?>
                    <p class="no-data">No stocks in any watchlist</p>
                <?php } 
                // If there are watchlist records, display them
                else { ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Item ID</th>
                                <th>Username</th>
                                <th>Stock Ticker</th>
                                <th>Stock Name</th>
                                <th>Added At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- For each of the watchlist records, put them in the table -->
                            <?php foreach ($watchlist as $item) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['itemID']); ?></td>
                                    <td><?php echo htmlspecialchars($item['username']); ?></td>
                                    <td><?php echo htmlspecialchars($item['stock_ticker']); ?></td>
                                    <td><?php echo htmlspecialchars($item['stock_name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['added_at']); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>