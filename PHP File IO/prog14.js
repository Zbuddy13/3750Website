// Add event listener for "Check these numbers" button press
document.getElementById('numberForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    // Set the numbers inputted to the number variable
    const numbers = document.getElementById('numbers').value;
    // Fetch call to php route check
    const response = await fetch('prog14.php?action=check', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=check&numbers=${encodeURIComponent(numbers)}`
    });
    // Set the results of fetch to result variable
    const result = await response.text();
    // Display the results in the result div
    document.getElementById('result').innerHTML = result;
});

// Helper function to display numbers in proper format
function displayNumbers(numbers, title, functype) {
    // Grab the result div from dom
    const resultDiv = document.getElementById('result');
    // If there are no numbers in the response, display no numbers found
    if (numbers.length === 0) {
        resultDiv.innerHTML = `<h3>${title}</h3><p>No numbers found.</p>`;
        return;
    }
    // If there are numbers, span each number in a list
    const numberElements = numbers.map(num => `<span class="number">${num}</span>`).join('');
    // Add the title and the numberelement list
    resultDiv.innerHTML = `<h3>${title}</h3><div class="number-list">${numberElements}</div>`;
}

// Add event listener for "Armstrong" button press
document.getElementById('armstrong').addEventListener('click', async () => {
    // Fetch from php for armstrong.txt numbers
    const response = await fetch('prog14.php?action=armstrong');
    // Set the response to var numbers
    const numbers = await response.json();
    // Call display numbers helper function
    displayNumbers(numbers, 'Armstrong Numbers');
});

// Add event listener for "Fibonacci" button press
document.getElementById('fibonacci').addEventListener('click', async () => {
    // Fetch from php for fibonacci.txt numbers
    const response = await fetch('prog14.php?action=fibonacci');
    // Set the response to numbers
    const numbers = await response.json();
    // Call display numbers helper function
    displayNumbers(numbers, 'Fibonacci Numbers');
});

// Add event listener for "Prime" button press
document.getElementById('prime').addEventListener('click', async () => {
    // Fetch from php for prime.txt numbers
    const response = await fetch('prog14.php?action=prime');
    // Set the response to numbers var
    const numbers = await response.json();
    // Call display numbers helper function
    displayNumbers(numbers, 'Prime Numbers');
});

// Add event listener for "None" button press
document.getElementById('none').addEventListener('click', async () => {
    // Fetch from php for none.txt numbers
    const response = await fetch('prog14.php?action=none');
    // Set the response to numbers var
    const numbers = await response.json();
    // Call display numbers helper function
    displayNumbers(numbers, 'Numbers that had no Category');
});

// Add event listener for "Reset" button press
document.getElementById('reset').addEventListener('click', async () => {
    // Fetch from php to reset the cookie and delete files
    const response = await fetch('prog14.php?action=reset');
    // Set the response to result
    const result = await response.text();
    // Display the result text
    document.getElementById('result').innerHTML = result;
    // Reset the number entry text box
    document.getElementById('numbers').value = '';
});
