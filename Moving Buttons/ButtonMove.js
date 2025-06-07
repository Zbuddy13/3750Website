document.addEventListener('DOMContentLoaded', () => {
    // Create varibales linked to dom elements
    const viewingArea = document.getElementById('viewingArea'); // Black area for buttons
    const makeButton = document.getElementById('makeButton'); // Button to make buttons
    const colorSelect = document.getElementById('colorSelect'); // Menu for color selection
    const totalSumSpan = document.getElementById('totalSum'); // Counter to keep track of score
    const moveButton = document.getElementById('moveButton'); // Button to move elements

    // Variables to set default states initially
    let totalSum = 0;
    let isMoving = false;
    let animationFrameId = null;

    // Function used to create new button
    function createButton() {
        // Create the new button element with the created button class
        const button = document.createElement('button');
        button.className = 'created-button';

        // Set initial color to selected color
        const selectedColor = colorSelect.value;
        button.style.backgroundColor = selectedColor;
        
        // Calculate random number between 1 and 50 and assign it to new button
        const randomNumber = Math.floor(Math.random() * 50) + 1;
        button.textContent = randomNumber;
        button.dataset.value = randomNumber; // Store value for scoring later

        // Append to viewing area and get the position of the button and viewing area
        viewingArea.appendChild(button);
        const newButtonRect = button.getBoundingClientRect();
        const viewAreaRect = viewingArea.getBoundingClientRect();

        // Calculate range button can be places
        const widthRange = viewAreaRect.width - newButtonRect.width
        const heightRange = viewAreaRect.height - newButtonRect.height

        // Calculate random position that is also in the bounds of the view area
        const newButtonX = Math.random() * widthRange;
        const newButtonY = Math.random() * heightRange;

        // Set the location of the button by using px
        button.style.left = `${newButtonX}px`;
        button.style.top = `${newButtonY}px`;

        // Assign random speed for movement
        // .5 used for direction as it can be -.5 or .5
        // Then multiply by 5 to give a speed of -2.5 or 2.5
        // * had issues with stale button, had to narrow range 1->2.5
        let dx = 0;
        let dy = 0;
        while (Math.abs(dx) < 1) {dx = (Math.random() - 0.5) * 5;}
        while (Math.abs(dy) < 1) {dy = (Math.random() - 0.5) * 5;}
        button.dx = dx; // Horizontal
        button.dy = dy; // Vertical
        
        // Add click listener so when clicked the score can be added
        button.addEventListener('click', handleButtonClick);
    }

    // Used to handle a button being clicked
    function handleButtonClick(event) {
        // Assign the element/button that is clicked
        const clickedButton = event.target;
        
        // Change color to the currently selected color based on menu value
        clickedButton.style.backgroundColor = colorSelect.value;
        
        // Update the total score by adding the buttons value
        const value = parseInt(clickedButton.dataset.value, 10); // set to base 10 mode
        totalSum += value;
        totalSumSpan.textContent = totalSum;
    }

    // Function used to move the buttons on screen
    function animateButtons() {
        // Assign buttons to all buttons in the viewing area and create area var
        const buttons = viewingArea.querySelectorAll('.created-button');
        const areaRect = viewingArea.getBoundingClientRect();

        // Function to apply for each of the buttons in the area
        buttons.forEach(button => {
            // Get button size
            const buttonRect = button.getBoundingClientRect();
            
            // Get current position of button
            let nextX = button.offsetLeft + button.dx;
            let nextY = button.offsetTop + button.dy;

            // If the next value is at the left or right of the view area change direction
            if (nextX + buttonRect.width > areaRect.width || nextX < 0) {
                button.dx *= -1; // Reverse direction
                nextX = button.offsetLeft + button.dx; // Recalculate so it does not stick
            }
            // If the next value is at the top or bottom of the view area change direction
            if (nextY + buttonRect.height > areaRect.height || nextY < 0) {
                button.dy *= -1; // Reverse direction
                nextY = button.offsetTop + button.dy; // Recalculate
            }

            // Apply new position to the button by setting the position
            button.style.left = `${nextX}px`;
            button.style.top = `${nextY}px`;
        });

        // Continue the loop if the button is not off
        if (isMoving) {
            // Set the frame id variable so it can stop the animation
            animationFrameId = requestAnimationFrame(animateButtons);
        }
    }

    // Used to toggle the movement of the buttons on screen
    function triggerMoveButtons() {
        // Set the ismoving var on or off based on previous state
        isMoving = !isMoving;
        // If they are set to move
        if (isMoving) {
            // Change button text
            moveButton.textContent = 'Stop Moving';
            // Add button formatting 
            moveButton.classList.add('moving');
            // Method used for smooth animations in the browser and calling move buttons
            animationFrameId = requestAnimationFrame(animateButtons);
        } else {
            // Change button text
            moveButton.textContent = 'Move';
            // Remove button formatting 
            moveButton.classList.remove('moving');
            // Cancel the animation and set the frame id to null to stop the loop
            cancelAnimationFrame(animationFrameId);
            animationFrameId = null;
        }
    }

    // Listen and call create button or toggle movement when buttons are clicked
    makeButton.addEventListener('click', createButton);
    moveButton.addEventListener('click', triggerMoveButtons);
});