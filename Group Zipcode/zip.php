<?php
// Get request to deal with live search
// Check to see if the request is get and has the zipcode
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    // Clean the input query 
    $query = filter_input(INPUT_GET, 'query');
    // Create variable to hold csv data and results
    $csvFile = 'uszips.csv';
    $results = [];
    // Code to read the input csv from simplemaps.com
    // Make sure the file exist
    if (file_exists($csvFile)) {
        // Pull the file into a lines array while ignoring new and empty lines
        $lines = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        // Grab the headers from each column of the csv
        $headers = str_getcsv(array_shift($lines));
        // For each of the lines of the csv
        foreach ($lines as $line) {
            // Parse the line using csv function
            $row = str_getcsv($line);
            // Create a data array with key value pairs (header as key, row data as values)
            $data = array_combine($headers, $row);
            // Look for the requested zipcode in the csv via the query
            if (stripos($data['zip'], $query) === 0) {
                //Return the results
                $results[] = [
                    'zip' => $data['zip'],
                    'city' => $data['city'],
                    'state' => $data['state_name']
                ];
                // Limit to 5 suggestions
                if (count($results) >= 5) break;
            }
        }
    }
    // Return the results as json
    header('Content-Type: application/json');
    echo json_encode($results);
    exit;
}

// Post request with zip codes to be processed
// Checks to see if it is a post request and if the zip 1 and 2 variables are in the string
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zip1'], $_POST['zip2'])) {
    // Filter out the zip 1 and 2 from the input
    $zip1 = filter_input(INPUT_POST, 'zip1');
    $zip2 = filter_input(INPUT_POST, 'zip2');
    // Set variable error to empty and distance to null (will be set later)
    $error = '';
    $distance = null;
    // Variables with csv file and array for all the zip data
    $csvFile = 'uszips.csv';
    $zipData = [];

    // Code to read the input csv from simplemaps.com
    // Make sure the file exist
    if (file_exists($csvFile)) {
        // Pull the file into a lines array while ignoring new and empty lines
        $lines = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        // Grab the headers from each column of the csv
        $headers = str_getcsv(array_shift($lines));
        // For each of the lines of the csv
        foreach ($lines as $line) {
            // Parse the line using csv function
            $row = str_getcsv($line);
            // Create a data array with key value pairs (header as key, row data as values)
            $data = array_combine($headers, $row);
            // Place that data into the main zip data array
            $zipData[$data['zip']] = [
                'lat' => floatval($data['lat']),
                'lng' => floatval($data['lng']),
                'city' => $data['city'],
                'state' => $data['state_name']
            ];
        }
    } 
    // If the csv file is not found, error
    else {
        $error = "CSV file not found.";
    }

    // Using the Haversine formula in order to calcualte the zip distance in miles
    // Input the lat and long for both to return distance in miles
    function zipdistance($lat1, $lon1, $lat2, $lon2) {
        // Radius of earth in miles
        $earthRadius = 3963.1;
        // Convert all of the long and lats to radians from degrees
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);
        // Calculate the difference in long and lat
        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;
        // Haversine formula
        // Calculate the square of half the chord length between the points on a unit sphere
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos($lat1) * cos($lat2) * sin($dLon / 2) * sin($dLon / 2);
        // Calcualte the angular distance in radians
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        // Convert to miles
        return $earthRadius * $c;
    }

    // If the user does not enter both zip codes return error
    if (empty($zip1) || empty($zip2)) {
        $error = "Please enter both zip codes";
    } 
    // If the zip codes entered are not valid return error
    elseif (!isset($zipData[$zip1]) || !isset($zipData[$zip2])) {
        $error = "One or both zip codes not found in the database";
    } 
    // If passes conditions, pass through to zip distance function so distance can be returned
    else {
        $distance = zipdistance(
            $zipData[$zip1]['lat'],
            $zipData[$zip1]['lng'],
            $zipData[$zip2]['lat'],
            $zipData[$zip2]['lng']
        );
    }
}
?>
<!-- HTTP Code section -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Zip Code Distance Calculator</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        /* Container for zip code calculator */
        .zip-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
        }
        /* Format for the group */
        .form-group {
            margin-bottom: 15px;
            position: relative;
        }
        /* Zip Label format */
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        /* Input text box format */
        input[type="text"] {
            width: 96%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 4px;
        }
        /* Button format */
        button {
            background-color: #F56600;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 14px;
            font-weight: bold;
        }
        /* Hover format */
        button:hover {
            background-color: #522D80;
        }
        /* Result and error box format */
        .result, .error {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
        }
        .result {
            background-color: #e7f3e7;
        }
        /* Error format */
        .error {
            background-color: #f8d7da;
            color: #ff0019ff;
        }
        /* Autocomplete zipcode class, element, and hover format */
        .autocomplete-suggestions {
            position: absolute;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            max-height: 150px;
            overflow-y: auto;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .autocomplete-suggestion {
            padding: 8px;
            cursor: pointer;
        }
        .autocomplete-suggestion:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <!-- Add nav bar -->
    <div id="navbar"></div>
    <!-- Create container to hold zip calculator -->
    <div class="zip-container">
        <h1 style="text-align: center;">Zip Code Distance Calculator</h1>
        <!-- Create a form with post method -->
        <form method="post">
            <!-- Form group for first zipcode -->
            <div class="form-group">
                <label for="zip1">First Zip Code:</label>
                <!-- Input field which will prefill old zip value after submission based on set variable -->
                <input type="text" id="zip1" name="zip1" value="<?php echo isset($zip1) ? htmlspecialchars($zip1) : ''; ?>" autocomplete="off">
                <!-- Div element to contain live search results -->
                <div id="zip1-suggestions" class="autocomplete-suggestions" style="display: none;"></div>
            </div>
            <!-- Form group for second zipcode -->
            <div class="form-group">
                <label for="zip2">Second Zip Code:</label>
                <!-- Input field which will prefill old zip value after submission based on set variable -->
                <input type="text" id="zip2" name="zip2" value="<?php echo isset($zip2) ? htmlspecialchars($zip2) : ''; ?>" autocomplete="off">
                <!-- Div element to contain live search results -->
                <div id="zip2-suggestions" class="autocomplete-suggestions" style="display: none;"></div>
            </div>
            <!-- Add submit button for post request -->
            <button type="submit">Calculate Distance</button>
        </form>
        <!-- If error variable is set, display error -->
        <?php if (isset($error) && $error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <!-- If the distance variable is set -->
        <?php elseif (isset($distance)): ?>
            <!-- Create result div class -->
            <div class="result">
                <!-- Set the string to display the distance between the two zipcodes -->
                Distance between <?php echo htmlspecialchars($zip1); ?> (<?php echo htmlspecialchars($zipData[$zip1]['city'] . ', ' . $zipData[$zip1]['state']); ?>) 
                and <?php echo htmlspecialchars($zip2); ?> (<?php echo htmlspecialchars($zipData[$zip2]['city'] . ', ' . $zipData[$zip2]['state']); ?>): 
                <strong><?php echo number_format($distance, 2); ?> miles</strong>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Function to display search results and allow autofill of zip code
        function autofillZip(inputId, suggestionsId) {
            // Set variable to dom input zip1 or 2
            const input = document.getElementById(inputId);
            // Set suggestions to zip1 or 2 suggestions div
            const suggestions = document.getElementById(suggestionsId);
            // Add input listener to the field that will execute the fetch every time the user updates the zip input field in async mode
            input.addEventListener('input', async () => {
                // Remove whitespace from the zip input
                const query = input.value.trim();
                // Return nothing if there are not more than 2 digits
                if (query.length < 2) {
                    suggestions.style.display = 'none';
                    return;
                }
                // Try to fetch from php
                try {
                    // Send the fetch request to the php code with the zip
                    const response = await fetch(`?query=${encodeURIComponent(query)}`);
                    const results = await response.json();
                    // Clear out the response div
                    suggestions.innerHTML = '';
                    // If there are any results
                    if (results.length > 0) {
                        // Iterate over the results array
                        results.forEach(result => {
                            // Create a div element for each response
                            const div = document.createElement('div');
                            // Set the class
                            div.className = 'autocomplete-suggestion';
                            // Set the text results
                            div.textContent = `${result.zip} (${result.city}, ${result.state})`;
                            // Add a listener for if the value is clicked, set this zip to the input field
                            div.addEventListener('click', () => {
                                input.value = result.zip;
                                suggestions.style.display = 'none';
                            });
                            // Add the div to the main div element
                            suggestions.appendChild(div);
                        });
                        // Set the display to block so it is visible
                        suggestions.style.display = 'block';
                    } else {
                        // If fails, set the style to none
                        suggestions.style.display = 'none';
                    }
                } 
                // If there is an error
                catch (error) {
                    // Display the error
                    console.error('Error fetching suggestions:', error);
                    suggestions.style.display = 'none';
                }
            });
            // Hide suggestions when clicking outside the input
            document.addEventListener('click', (event) => {
                if (!input.contains(event.target) && !suggestions.contains(event.target)) {
                    suggestions.style.display = 'none';
                }
            });

            // Added navbar code
            document.addEventListener('DOMContentLoaded', () => {
                fetch('../navbar.html')
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('navbar').innerHTML = data;
                    });
            });
        }
        // Set the function to each input and element
        autofillZip('zip1', 'zip1-suggestions');
        autofillZip('zip2', 'zip2-suggestions');
    </script>
</body>
</html>