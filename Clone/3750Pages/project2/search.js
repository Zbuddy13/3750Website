// Estabilish variables to be used
const apiKey = 'd1trt19r01qt0evcso50d1trt19r01qt0evcso5g'; // Token for finnhub to provide search and partial search
const searchInput = document.getElementById('searchInput'); // Used to hold search term
const resultsContainer = document.getElementById('results'); // Assign the search results container element
const watchlistContainer = document.getElementById('watchlist'); // Assign the watchlist container element
const watchlistEmptyMessage = document.getElementById('watchlistEmptyMessage'); // Assign the watchlist empty element
// --- Stock search fetching ---
// Function to fetch stock data
async function fetchStocks(query) {
    // If there is not a query, set the result container to empty
    if (!query) {
        resultsContainer.innerHTML = '';
        return;
    }
    // Attempt to search using the funnhub api
    try {
        // Request search of query from finnhub
        const response = await fetch(`https://finnhub.io/api/v1/search?q=${encodeURIComponent(query)}&token=${apiKey}`);
        const data = await response.json();
        // If no stock was found, display no results
        if (data.count === 0) {
            resultsContainer.innerHTML = '<div class="no-results">No results found</div>';
            return;
        }
        // Clear the container before setting the list
        resultsContainer.innerHTML = '';
        // For each of the stock returned by finnhub
        data.result.forEach(stock => {
            // Create the element for the stock name
            const div = document.createElement('div');
            // Set the class
            div.className = 'result-item';
            // Set the tooltip title
            div.title = `Click to view ${stock.symbol}'s chart`;

            // Set the html code to the display the ticker and name of the stock
            div.innerHTML = `
                <div><strong>${stock.symbol}</strong> - ${stock.description}</div>
                <button data-symbol="${stock.symbol}" data-description="${stock.description}">Add to Watchlist</button>
            `;
            // Add a button to the stock to add it to the watchlist
            div.querySelector('button').addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent the div's click event from firing
                addStockToWatchlist(stock.symbol, stock.description); // Call the add to watchlist function
            });
            // Click to send to chart.html to display stats
            div.addEventListener('click', () => {
                // Set the window to the chart.html and send the stock symbol to process
                window.location.href = `chart.html?symbol=${encodeURIComponent(stock.symbol)}`;
            });

            // Append tto the results container
            resultsContainer.appendChild(div);
        });
    }
    catch (error) {
        // If the api has an error, set the html to error and display in the console
        resultsContainer.innerHTML = '<div class="error">Error fetching data. Please try again.</div>';
        console.error('Error fetching stock data:', error);
    }
}
// --- Watchlist Functions ---
// Load watchlist from local storage and parse the data
function getWatchlist() {
    const watchlist = localStorage.getItem('stockWatchlist');
    return watchlist ? JSON.parse(watchlist) : [];
}
// Save watchlist to local storage
function saveWatchlist(watchlist) {
    localStorage.setItem('stockWatchlist', JSON.stringify(watchlist));
}
// Add stock to watchlist
function addStockToWatchlist(symbol, description) {
    let watchlist = getWatchlist();
    // Check if stock is already in watchlist to avoid duplicates
    if (!watchlist.some(stock => stock.symbol === symbol)) {
        watchlist.push({ symbol, description });
        saveWatchlist(watchlist);
        renderWatchlist();
    }
}
// Remove stock from watchlist
function removeStockFromWatchlist(symbol) {
    let watchlist = getWatchlist();
    watchlist = watchlist.filter(stock => stock.symbol !== symbol);
    saveWatchlist(watchlist);
    renderWatchlist();
}
// Render watchlist to the UI
function renderWatchlist() {
    // Pull the watchlist from storage
    const watchlist = getWatchlist();
    // Clear current list
    watchlistContainer.innerHTML = '';
    // If there are no elements in the current watch list
    if (watchlist.length === 0) {
        watchlistContainer.innerHTML = '<div class="watchlist-empty">No stocks in your watchlist yet.</div>';
    }
    // For each of the stock found in storage, add them to the list on page
    else {
        watchlist.forEach(stock => {
            // Create the div and set the class
            const div = document.createElement('div');
            div.className = 'watchlist-item';
            // Set the contents to the symbol, description, and add the remove and view chart buttons
            div.innerHTML = `
                <div><strong>${stock.symbol}</strong> - ${stock.description}</div>
                <div class="watchlist-actions">
                    <button class="view-chart-btn" data-symbol="${stock.symbol}">View Chart</button>
                    <button class="remove-btn" data-symbol="${stock.symbol}">Remove</button>
                </div>
            `;
            // Add the listener to the remove button so when clicked it removes the stock from the watchlist
            div.querySelector('.remove-btn').addEventListener('click', (e) => {
                // Prevent send to chart.html
                e.stopPropagation();
                removeStockFromWatchlist(stock.symbol);
            });
            // Add the listener to the view chart button
            div.querySelector('.view-chart-btn').addEventListener('click', (e) => {
                e.stopPropagation();
                window.location.href = `chart.html?symbol=${encodeURIComponent(stock.symbol)}`;
            });
            // Appends the stock to the parent list
            watchlistContainer.appendChild(div);
        });
    }
}
// Debounce function to limit API calls
// This is important as the free tier has a limit in the amount of calls made
function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}
// Setup the debounced search with the stock fetch and a delay of 500 ms
// This will ensure when typing it will wait properly
const debouncedSearch = debounce(fetchStocks, 500);
// Event listener for input field
searchInput.addEventListener('input', (e) => {
    debouncedSearch(e.target.value.trim());
});
// Initial render of the watchlist when the page loads
document.addEventListener('DOMContentLoaded', renderWatchlist);
// Navbar code
document.addEventListener('DOMContentLoaded', () => {
    fetch('../navbar.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('navbar').innerHTML = data;
        });
});