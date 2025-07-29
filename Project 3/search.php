<?php
// PHP code for the search/watchlist page
session_start();
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not redirect to the login page
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>P3: Stock Search</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="search.css">
</head>
<body>
    <!-- Add nav bar -->
    <div id="navbar"></div>
    <!-- Container for all elements -->
    <div class="search">
        <!-- Container to hold stock search bar -->
        <div class="search-container">
            <!-- Create search bar -->
            <input type="text" class="search-bar" placeholder="Type to search for stocks..." id="searchInput">
            <!-- Create results div to hold search results -->
            <div class="results" id="results"></div>
        </div>
        <!-- Container to hold the watchlist of stocks stored in database -->
        <div class="watchlist-container">
            <h2>My Favorite Stocks</h2>
            <!-- Div to hold watchlist -->
            <div class="watchlist" id="watchlist">
                <!-- Default empty state for watchlist -->
                <div class="watchlist-empty" id="watchlistEmptyMessage">No stocks in your watchlist yet.</div>
            </div>
        </div>
    </div>
    <!-- Pass userID to js -->
    <script>
        const userID = <?php echo json_encode($_SESSION['user_id']); ?>;
    </script>
    <!-- Import the search js -->
    <script type="text/javascript" src="search.js"></script>
</body>
</html>