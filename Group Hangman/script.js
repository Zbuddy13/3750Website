// Create global variables for each game
let wrongGuesses = 0;
const maxGuesses = 6;
let currentWord = '';
let guessedLetters = new Set();

// Called when user clicks start game
function startGame() {
    // Initialize the game
    wrongGuesses = 0;
    guessedLetters.clear();
    drawHangman();
    
    // Fetch a new word from the server
    // Created a larger list of words (B version)
    fetch('getWord.php')
        .then(response => response.json())
        .then(data => {
            if (data.word) {
                // If cheat mode is enabled show word as alert (D version)
                currentWord = data.word.toUpperCase();
                if (document.getElementById('cheatMode').checked) {
                    alert('Word: ' + currentWord);
                }
                // Call setup game and pass the current word
                setupGame(currentWord);
            } else {
                console.error('Error fetching word:', data.error);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Used to setup the game
function setupGame(word) {
    // Pull the word to guess dom element
    const wordToGuess = document.getElementById('wordToGuess');
    // Format with underlines and spaces
    wordToGuess.innerHTML = word.split('')
        .map(letter => guessedLetters.has(letter) ? letter : '_')
        .join(' ');
    // Generate the letter buttons for the game
    generateLetterButtons();
    // Draw the hangman
    drawHangman();
}

// Used to generate the letter buttons in a grid
function generateLetterButtons() {
    const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const lettersDiv = document.getElementById('letters');
    // Clear previous buttons used
    lettersDiv.innerHTML = '';
    // For each letter
    letters.split('').forEach(letter => {
        // Create button element
        const button = document.createElement('button');
        // Set the text to the letter
        button.textContent = letter;
        // Disable the button if it has been used
        button.disabled = guessedLetters.has(letter);
        // When button is clicked, add to guessed letter
        button.onclick = () => guessLetter(letter);
        lettersDiv.appendChild(button);
    });
}
// Called when the player gueses a letter
function guessLetter(letter) {
    // Add the letter ot the set of guessed letters
    guessedLetters.add(letter);
    // Disable the letter that was clicked
    const button = event.target;
    button.disabled = true;
    // If the guess is incorrect, increment the variable and call draw hangman
    if (!currentWord.includes(letter)) {
        wrongGuesses++;
        drawHangman();
    }
    // Update the game display
    setupGame(currentWord);
    // Check if player has lost game
    if (wrongGuesses >= maxGuesses) {
        // Added delay so canvas updates with leg
        setTimeout(() => {
            alert('Game Over! The word was: ' + currentWord);
            startGame();
        }, 100);
    // Check if player won game
    } else if (currentWord.split('').every(letter => guessedLetters.has(letter))) {
        // Added delay so word dispaly updates
        setTimeout(() => {
            alert('Congratulations! You won!');
            startGame();
        }, 100);
    }
}
// Updates the hangman drawing
function drawHangman() {
    // Grab the canvas element for it to modify
    const canvas = document.getElementById('hangmanCanvas');
    // Grab the 2d rendering context
    const ctx = canvas.getContext('2d');
    // Clear canvas and set drawing line width
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.lineWidth = 2;

    // Draw the gallows
    ctx.beginPath();
    ctx.moveTo(20, 180);
    ctx.lineTo(180, 180);
    ctx.moveTo(50, 180);
    ctx.lineTo(50, 20);
    ctx.lineTo(120, 20);
    ctx.lineTo(120, 40);
    ctx.stroke();

    // Draw hangman parts based on wrong guesses
    // Head
    if (wrongGuesses > 0) {
        ctx.beginPath();
        ctx.arc(120, 60, 20, 0, Math.PI * 2);
        ctx.stroke();
    }
    // Body
    if (wrongGuesses > 1) {
        ctx.beginPath();
        ctx.moveTo(120, 80);
        ctx.lineTo(120, 120);
        ctx.stroke();
    }
    // Left arm
    if (wrongGuesses > 2) {
        ctx.beginPath();
        ctx.moveTo(120, 90);
        ctx.lineTo(100, 110);
        ctx.stroke();
    }
    // Right arm
    if (wrongGuesses > 3) {
        ctx.beginPath();
        ctx.moveTo(120, 90);
        ctx.lineTo(140, 110);
        ctx.stroke();
    }
    // Left leg
    if (wrongGuesses > 4) {
        ctx.beginPath();
        ctx.moveTo(120, 120);
        ctx.lineTo(100, 150);
        ctx.stroke();
    }
    // Right leg
    if (wrongGuesses > 5) {
        ctx.beginPath();
        ctx.moveTo(120, 120);
        ctx.lineTo(140, 150);
        ctx.stroke();
    }
}

// Trigger the start game
startGame();