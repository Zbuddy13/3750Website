<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title> 
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div id="navbar"></div>

    <h1>My first PHP page</h1>

    <?php
    echo "Hello World!";
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            fetch('navbar.html')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('navbar').innerHTML = data;
                });
        });
    </script>
</body>
</html>