// James Shaw
// 6/14/25
// CPSC 3750
// program exam #1 
// A level

// Clemson brand colors
const colors = ['#005EB8', '#B94700', '#EFDBB2', '#CBC4BC', '#F1C400'];
// Index to find color in list to use
let colorIndex = 0;
// Function to determine if a number is prime
function isNumPrime(num) {
    if (num <= 1) return false;
    for (let i = 2; i <= Math.sqrt(num); i++) {
        if (num % i === 0) return false;
    }
    return true;
}
// Function to generate the two list
function generateLists() {
    // Grab input value
    const input = parseInt(document.getElementById('userInput').value);
    // Ensure the input is greater than 1 and exist
    if (isNaN(input) || input < 1) {
        alert('Please enter a valid number greater than 0');
        return;
    }
    // Create arrays for prime and non prime numbers
    let primeNumbers = [];
    let nonPrimeNumbers = [];
    // For every numebr in the input, check if it is prime and push to list
    for (let i = 1; i <= input; i++) {
        if (isNumPrime(i)) {
            primeNumbers.push(i);
        } else {
            nonPrimeNumbers.push(i);
        }
    }
    // Grab the list dom
    const primeList = document.getElementById('primeNumbers');
    const nonPrimeList = document.getElementById('nonPrimeNumbers');
    // Set the html empty for the prime and non prime lsit
    primeList.innerHTML = '';
    nonPrimeList.innerHTML = '';
    // For each number in the list, create an li element with the number
    primeNumbers.forEach(num => {
        const li = document.createElement('li');
        li.textContent = num;
        primeList.appendChild(li);
    });
    nonPrimeNumbers.forEach(num => {
        const li = document.createElement('li');
        li.textContent = num;
        nonPrimeList.appendChild(li);
    });
    // Set the sums to empty
    document.getElementById('primeSum').textContent = '';
    document.getElementById('nonPrimeSum').textContent = '';
}
// Calculate the sum of the numbers for prime and not prime
function showSum(listId, sumId) {
    // Go into the array on the dom, access each number and put in numbers array in js
    const numbers = Array.from(document.getElementById(listId).children).map(li => parseInt(li.textContent));
    // Add together all the numbers in the list
    const sum = numbers.reduce((acc, num) => acc + num, 0);
    // Set the element in html to the sum of the numbers
    document.getElementById(sumId).textContent = `Sum: ${sum}`;
}     
// Intereval used to change the color every 5 seconds or 5000 miliseconds
setInterval(() => {
    // Grab the list objects from dom
    const primeList = document.getElementById('primeList');
    const nonPrimeList = document.getElementById('nonPrimeList');
    // Generate random indexes for colors
    let primeIndex = Math.floor(Math.random() * colors.length);
    // Find an index that is not the same as the first one generated
    let nonPrimeIndex;
    do {
        nonPrimeIndex = Math.floor(Math.random() * colors.length);
    } while (nonPrimeIndex === primeIndex);
    // Set the background of the list to a color in colors array
    primeList.style.backgroundColor = colors[primeIndex];
    nonPrimeList.style.backgroundColor = colors[nonPrimeIndex];
}, 5000);