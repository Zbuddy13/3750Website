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
    <title>P3: Stock Chart</title>
    <!-- Use the CDN for JS chart  -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <!-- Link style sheets -->
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="chart.css">
</head>
<body>
    <!-- Nav bar code -->
    <div id="navbar"></div>
    <!-- Main container -->
    <div class="chart">
        <h1 id="chartTitle">Stock Chart</h1>
        <!-- Usability (add button to go back to watchlist page) -->
        <a href="search.php">
            <button class="see-more-button" style="margin-bottom: 20px;">Go back to My Watchlist</button>
        </a>
        <!-- Container for the chart -->
        <div class="chart-container">
            <!-- Set a canvas for the stock price chart -->
            <canvas id="stockChart"></canvas>
            <!-- Default error state -->
            <div id="errorMessage" class="error" style="display: none;"></div>
        </div>
        <!-- Container to display profile -->
        <div class="company-profile-container">
            <h2>Company Profile</h2>
            <!-- Display profile contents -->
            <div id="companyProfileContent">
                <div class="company-profile-item"><strong id="profileNameLabel">Company Name:</strong> <span id="profileName">Loading...</span></div>
                <div class="company-profile-item"><strong id="profileExchangeLabel">Exchange:</strong> <span id="profileExchange">Loading...</span></div>
                <div class="company-profile-item"><strong id="profileIndustryLabel">Industry:</strong> <span id="profileIndustry">Loading...</span></div>
                <div class="company-profile-item"><strong id="profileWebURLLabel">Website:</strong> <span id="profileWebURL">Loading...</span></div>
                <div class="company-profile-item"><strong id="profileIPODateLabel">IPO Date:</strong> <span id="profileIPODate">Loading...</span></div>
                <div class="company-profile-item"><strong id="profilePhoneLabel">Phone:</strong> <span id="profilePhone">Loading...</span></div>
                <!-- Default error state -->
                <div id="profileErrorMessage" class="error" style="display: none;"></div>
            </div>
        </div>
        <!-- Main container to display news for the stock -->
        <div class="company-news-container">
            <h2>Latest Company News</h2>
            <!-- Element for company news -->
            <div id="companyNewsContent">
                <!-- Loading message -->
                <div id="newsLoadingMessage" class="no-data-message">Loading news...</div>
                <div id="newsErrorMessage" class="error" style="display: none;"></div>
                <div id="noNewsMessage" class="no-data-message" style="display: none;">No recent news found for this company.</div>
            </div>
            <!-- Container to hold the see more button -->
            <div class="see-more-button-container">
                <!-- Set the see more news button container -->
                <button id="seeMoreNewsBtn" class="see-more-button" style="display: none;">See More News</button>
            </div>
        </div>
    </div>
    <!-- Import the search js -->
    <script type="text/javascript" src="chart.js"></script>
</body>
</html>