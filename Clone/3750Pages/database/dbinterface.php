<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integrate with DB</title>
    <!-- Import stylesheets -->
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="createdb.css">
</head>
<body>
    <!-- Add nav bar -->
    <div id="navbar"></div>
    <!-- Formatting for page -->
    <div class="createdb">
        <h1>Create DB</h1>
        <!-- Form to add a new person to the db -->
        <div class="form-container">
            <h2>Add New Person</h2>
            <!-- Post request to the dbaction php -->
            <form action="dbaction.php" method="POST">
                <!-- Hidden value to be read by dbaction -->
                <input type="hidden" name="action" value="add">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" required>
                <button class="db-button" type="submit">Add Person</button>
            </form>
        </div>
        <!-- Form to retrieve all records from the database -->
        <div class="form-container">
            <h2>View All Persons</h2>
            <!-- Post request to the dbaction php -->
            <form action="dbaction.php" method="POST">
                <!-- Hidden value to be read by dbaction -->
                <input type="hidden" name="action" value="view_all">
                <button class="db-button" type="submit">Show All Persons</button>
            </form>
        </div>
        <!-- Form to search by last name in the database -->
        <div class="form-container search-form">
            <h2>Search by Last Name</h2>
            <!-- Post request to the dbaction php -->
            <form action="dbaction.php" method="POST">
                <!-- Hidden value to be read by dbaction -->
                <input type="hidden" name="action" value="search">
                <label for="search_last_name">Last Name:</label>
                <input type="text" id="search_last_name" name="search_last_name" required>
                <button class="db-button" type="submit">Search</button>
            </form>
        </div>
        <!-- Results div to display the results retrieved from the database -->
        <?php
        $results = [];
        // If the results are set and not empty
        if (isset($_GET['results']) && !empty($_GET['results'])) {
            // Decode the results
            $decodedResults = json_decode(urldecode($_GET['results']), true);
            // If they are not empty, set in the results var
            if (!empty($decodedResults)) {
                $results = $decodedResults;
            }
        }
        // Only display the results div if there are actual results
        if (!empty($results)) {
        ?>
        <div class="results">
            <?php
            // Create the results table for the elements to be displayed
            echo '<table>';
            echo '<tr><th>First Name</th><th>Last Name</th><th>Email</th></tr>';
            // For each of the result put them into the table as elements
            foreach ($results as $row) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['first_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['last_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            ?>
        </div>
        <?php
        }
        ?>
    </div>
    <script>
        // Added navbar code
        document.addEventListener('DOMContentLoaded', () => {
            fetch('../navbar.html')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('navbar').innerHTML = data;
                });
        });
    </script>
</body>
</html>