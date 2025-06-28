// Array to store cards and toggle visibilty
let cards = [];
let isCardsVisible = false;

// Initialize with sample data
cards.push(new Card("Sue Suthers", "sue@suthers.com", "123 Elm Street, Yourtown ST 99999", "555-555-9876", "1957-06-06"));
cards.push(new Card("Fred Fanboy", "fred@fanboy.com", "233 Oak Lane, Sometown ST 99399", "555-555-4444", "1980-03-15"));
cards.push(new Card("Jimbo Jones", "jimbo@jones.com", "233 Walnut Circle, Anotherville ST 88999", "555-555-1344", "1990-11-22"));

// Define the card constructor
function Card(name, email, address, phone, birthdate) {
    this.name = name;
    this.email = email;
    this.address = address;
    this.phone = phone;
    this.birthdate = birthdate;
    this.printCard = printCard;
}

// Define function to print out each card
function printCard() {
    return `
        <strong>Name: </strong>${this.name}<br>
        <strong>Email: </strong>${this.email}<br>
        <strong>Address: </strong>${this.address}<br>
        <strong>Phone: </strong>${this.phone}<br>
        <strong>Birthdate: </strong>${this.birthdate}<br>
        <hr>
    `;
}

// Function to add a new card
function addNewCard() {
    // Get all the elements from the document
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const address = document.getElementById('address').value;
    const phone = document.getElementById('phone').value;
    const birthdate = document.getElementById('birthdate').value;
    // If all the elements are filled in
    if (name && email && address && phone && birthdate) {
        // Create the card object
        const newCard = new Card(name, email, address, phone, birthdate);
        // Push the new card into the array
        cards.push(newCard);
        // Reset the data entry fields
        document.getElementById('cardForm').reset();
        // Alert the user that the cards were added
        alert('Card added successfully!');
        // If the cards are visible, then refresh
        if (isCardsVisible) {
            displayAllCards(); // Refresh table if toggled
        }
    } else {
        // If the user does not enter data alert
        alert('Please fill in all fields.');
    }
}

// Function to display all cards in a table
function displayAllCards() {
    // Set the output to the heading
    let output = '<h2>All Business Cards</h2>';
    // Add the table format html
    output += `
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Birthdate</th>
            </tr>
    `;
    // For each of the cards in the array, add them to the table to be displayed
    cards.forEach(card => {
        output += `
            <tr>
                <td>${card.name}</td>
                <td>${card.email}</td>
                <td>${card.address}</td>
                <td>${card.phone}</td>
                <td>${card.birthdate}</td>
            </tr>
        `;
    });
    // Add table close tag
    output += '</table>';
    // Output to the dom
    document.getElementById('cardOutput').innerHTML = output;
}

// Function to toggle business card table
function toggleCards() {
    // Create variables for the card output table and the toggle button
    const outputDiv = document.getElementById('cardOutput');
    const toggleButton = document.getElementById('toggleButton');
    // If the table is visible when button is pressed
    if (isCardsVisible) {
        outputDiv.style.display = 'none';
        toggleButton.textContent = 'Show All Cards';
        isCardsVisible = false;
    } 
    // If the table is not visible when button is pressed
    else {
        displayAllCards();
        outputDiv.style.display = 'block';
        toggleButton.textContent = 'Hide All Cards';
        isCardsVisible = true;
    }
}