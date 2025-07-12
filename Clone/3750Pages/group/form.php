<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Form Submission Result</title>
    <style>
        /* Body style for form submission */
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: white;
            margin: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            max-width: 650px;
            margin-left: auto;
            margin-right: auto;
            background-color: #fff;
        }
        /* Main heading format with line underneath */
        h2 {
            color: #333333;
            border-bottom: 3px solid #522D80;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        /* Normal text format */
        p {
            margin-bottom: 10px;
        }
        /* Format for each submission header */
        strong {
            color: #F56600;
        }
        /* Format to display file information */
        .file-info {
            background-color: #522D80;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        /* Change the text color */
        .file-info p {
            margin-bottom: 10px;
            color: white;
        }
        /* button format */
        button {
            padding: 10px 20px;
            background-color: #F56600;
            color: white;
            cursor: pointer;
            font-size: 15px;
            border: none;
            border-radius: 5px;
        }
        button:hover {
            background-color: #522D80;
        }
    </style>
</head>
<body>

    <h2>Form Submission Result</h2>

    <!-- Start of php code -->
    <?php

        // Echo the Text Input
        if (isset($_POST['username'])) {
            echo "<p><strong>Text Input (Name):</strong> " . htmlspecialchars($_POST['username']) . "</p>";
        }

        // Echo the Text Area Input
        if (isset($_POST['message'])) {
            echo "<p><strong>Text Area (Message):</strong> " . nl2br(htmlspecialchars($_POST['message'])) . "</p>";
        }

        // Echo the Hidden Data
        if (isset($_POST['hidden'])) {
            echo "<p><strong>Hidden Text:</strong> " . htmlspecialchars($_POST['hidden']) . "</p>";
        }

        // Echo the password entered
        if (isset($_POST['password'])) {
            echo "<p><strong>Password Input:</strong> " . htmlspecialchars($_POST['password']) . "</p>";
        }

        // Array of Checkboxes
        // If the interest are valid
        if (isset($_POST['interests']) && is_array($_POST['interests'])) {
            echo "<p><strong>Array of Checkboxes (Interests):</strong> ";
            // Join each of the elements with a , between them
            echo implode(", ", array_map('htmlspecialchars', $_POST['interests']));
            echo "</p>";
        } else {
            // If the user did not select any interest, display that
            echo "<p><strong>Array of Checkboxes (Interests):</strong> No interests selected.</p>";
        }

        // Radio Buttons
        if (isset($_POST['contact_method'])) {
            // Attach the user preference
            echo "<p><strong>Radio Buttons (Preferred Contact Method):</strong> " . htmlspecialchars($_POST['contact_method']) . "</p>";
        } else {
            // If they did not have a preference display not selected
            echo "<p><strong>Radio Buttons (Preferred Contact Method):</strong> Not selected.</p>";
        }

        // Selection List
        // Ensure the list of countries is valid
        if (isset($_POST['country']) && !empty($_POST['country'])) {
            // Display the selected country
            echo "<p><strong>Selection List (Country):</strong> " . htmlspecialchars($_POST['country']) . "</p>";
        } else {
            // If not selected, display not selected
            echo "<p><strong>Selection List (Country):</strong> Not selected.</p>";
        }

        // File Upload
        echo "<p><strong>File Input (Upload Profile Picture):</strong></p>";
        // Check to ensure file was uploaded
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
            echo "<div class='file-info'>";
            // Display the name of the file
            echo "<p><strong>File Name:</strong> " . htmlspecialchars($_FILES['profile_pic']['name']) . "</p>";
            // Display the file type
            echo "<p><strong>File Type:</strong> " . htmlspecialchars($_FILES['profile_pic']['type']) . "</p>";
            // Display the file size
            echo "<p><strong>File Size:</strong> " . number_format($_FILES['profile_pic']['size'] / 1024, 2) . " KB</p>";
            echo "</div>";
        // Case if no file is uploaded
        } else {
            echo "<p>No file uploaded.</p>";
        }

        // URL Input
        if (isset($_POST['user_url'])) {
            // Display the url entered
            echo "<p><strong>URL:</strong> " . htmlspecialchars($_POST['user_url']) . "</p>";
        }
    ?>
    <br><button onclick="document.location='/group/form.html'">Go back to form</button>
</body>
</html>