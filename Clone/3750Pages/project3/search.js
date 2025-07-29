// Establish variables to be used
const apiKey = 'd1trt19r01qt0evcso50d1trt19r01qt0evcso5g'; // Token for finnhub to provide search and partial search
const marketDataApiKey = 'bV9VTlFoUjJLeUVSUUVHeE9Nb1NhdFFkcGE4bGZsR2xjd1FnTF9lVm9Yaz0'; // Token for Market Data API
const searchInput = document.getElementById('searchInput'); // Used to hold search term
const resultsContainer = document.getElementById('results'); // Assign the search results container element
const watchlistContainer = document.getElementById('watchlist'); // Assign the watchlist container element
const watchlistEmptyMessage = document.getElementById('watchlistEmptyMessage'); // Assign the watchlist empty element
// Fetch the stocks in the search
async function fetchStocks(query) {
    // If there is an empty query, then set container to empty
    if (!query) {
        resultsContainer.innerHTML = '';
        return;
    }
    try {
        // Try to search finnhub for stock
        const response = await fetch(`https://finnhub.io/api/v1/search?q=${encodeURIComponent(query)}&token=${apiKey}`);
        const data = await response.json();
        // If no results are found (data.count is 0), display a "No results found" message
        if (data.count === 0) {
            resultsContainer.innerHTML = '<div class="no-results">No results found</div>';
            return;
        }
        // Empty the container
        resultsContainer.innerHTML = '';
        // For each stock found in search
        data.result.forEach(stock => {
            // Create the div element
            const div = document.createElement('div');
            div.className = 'result-item';
            div.title = `Click to view ${stock.symbol}'s chart`;
            // Set the inner html to the symbol, description, and a add to watchlist button
            div.innerHTML = `
                <div><strong>${stock.symbol}</strong> - ${stock.description}</div>
                <button data-symbol="${stock.symbol}" data-description="${stock.description}">Add to Watchlist</button>
            `;
            // Add the eventlistener to the button so it can add the stock to the db watchlist
            div.querySelector('button').addEventListener('click', (e) => {
                e.stopPropagation();
                addStockToWatchlist(stock.symbol, stock.description);
            });
            // Add the event listener to send te user to the chart page when clicked
            div.addEventListener('click', () => {
                window.location.href = `chart.php?symbol=${encodeURIComponent(stock.symbol)}`;
            });
            resultsContainer.appendChild(div);
        });
    } 
    // If there is an error fetching
    catch (error) {
        resultsContainer.innerHTML = '<div class="error">Error fetching data. Please try again.</div>';
        console.error('Error fetching stock data:', error);
    }
}
// Fetch stock details for dropdown in watchlist
async function fetchStockDetails(symbol) {
    try {
        // Fetch last price from Market Data API
        const priceResponse = await fetch(`https://api.marketdata.app/v1/stocks/quotes/${encodeURIComponent(symbol)}?token=${marketDataApiKey}`);
        const priceData = await priceResponse.json();
        const lastPrice = priceData.last || 'N/A';
        // Fetch website link from Finnhub API
        const profileResponse = await fetch(`https://finnhub.io/api/v1/stock/profile2?symbol=${encodeURIComponent(symbol)}&token=${apiKey}`);
        const profileData = await profileResponse.json();
        const website = profileData.weburl ? `<a href="${profileData.weburl}" target="_blank">${profileData.weburl}</a>` : 'N/A';
        // Return json array with price and website
        return { lastPrice, website };
    } 
    // If there is an error
    catch (error) {
        console.error(`Error fetching details for ${symbol}:`, error);
        return { lastPrice: 'N/A', website: 'N/A' };
    }
}
// Watchlist Functions 
async function getWatchlist() {
    // Attempt to get the watchlist from the database
    try {
        const response = await fetch('watchlist.php?action=get');
        const watchlist = await response.json();
        return watchlist;
    } 
    // If there is an error getting the watchlist
    catch (error) {
        console.error('Error fetching watchlist:', error);
        return [];
    }
}
// Function to add a stock to the watchlist
async function addStockToWatchlist(symbol, description) {
    // Try to add the stock to the database
    try {
        const response = await fetch('watchlist.php?action=add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ userID, symbol, description })
        });
        // If the response was successful, render the watchlist
        if (response.ok) {
            renderWatchlist();
        } 
        // If error in response
        else {
            console.error('Error adding to watchlist');
        }
    } 
    // If cannot connect to watchlist php, error
    catch (error) {
        console.error('Error adding to watchlist:', error);
    }
}
// Function to remove a stock from the watchlist
async function removeStockFromWatchlist(symbol) {
    // Attempt to remove from the database
    try {
        const response = await fetch('watchlist.php?action=remove', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ userID, symbol })
        });
        // If success render watchlist
        if (response.ok) {
            renderWatchlist();
        } 
        // If error in removing from db
        else {
            console.error('Error removing from watchlist');
        }
    } 
    // If there is an error with the watchlist.php
    catch (error) {
        console.error('Error removing from watchlist:', error);
    }
}
// Function to generate and display the watchlist
async function renderWatchlist() {
    // Fetch the current watchlist data
    const watchlist = await getWatchlist();
    // Clear any existing content in the watchlist container
    watchlistContainer.innerHTML = '';
    // If the db watchlist is empty, display a message
    if (watchlist.length === 0) {
        watchlistContainer.innerHTML = '<div class="watchlist-empty">No stocks in your watchlist yet.</div>';
    } 
    else {
        // Iterate over each stock in the watchlist.
        for (const stock of watchlist) {
            // Create a new div element for each watchlist item with class
            const div = document.createElement('div');
            div.className = 'watchlist-item';
            // Set the inner HTML of the watchlist item, including header, details placeholder, and action buttons
            div.innerHTML = `
                <div class="watchlist-header">
                    <strong>${stock.stock_ticker}</strong> - ${stock.stock_name}
                    <button class="dropdown-toggle" data-symbol="${stock.stock_ticker}">▼</button>
                </div>
                <div class="watchlist-details" style="display: none;">
                    <div>Loading details...</div>
                </div>
                <div class="watchlist-actions">
                    <button class="view-chart-btn" data-symbol="${stock.stock_ticker}">View Chart</button>
                    <button class="remove-btn" data-symbol="${stock.stock_ticker}">Remove</button>
                </div>
            `;
            // Append the created watchlist item div to the watchlist container
            watchlistContainer.appendChild(div);
            // Fetch and populate dropdown details for the current stock
            const detailsDiv = div.querySelector('.watchlist-details');
            const details = await fetchStockDetails(stock.stock_ticker);
            // Update the details div with the fetched last price and website link
            detailsDiv.innerHTML = `
                <div>Last Price: ${details.lastPrice}</div>
                <div>Website: ${details.website}</div>
            `;
            // Add dropdown toggle event listener for the arrow button
            const toggleBtn = div.querySelector('.dropdown-toggle');
            toggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                // Check if the details div is currently visible
                const isVisible = detailsDiv.style.display === 'block';
                // Toggle the display style of the details div (show/hide)
                detailsDiv.style.display = isVisible ? 'none' : 'block';
                // Change the toggle button text based on visibility
                toggleBtn.textContent = isVisible ? '▼' : '▲';
            });
            // Add remove button event listener for each watchlist item
            div.querySelector('.remove-btn').addEventListener('click', (e) => {
                e.stopPropagation();
                // Call the function to remove the stock from the watchlist
                removeStockFromWatchlist(stock.stock_ticker);
            });
            // Add view chart button event listener for each watchlist item
            div.querySelector('.view-chart-btn').addEventListener('click', (e) => {
                e.stopPropagation();
                // Redirect to the chart page for the specific stock
                window.location.href = `chart.php?symbol=${encodeURIComponent(stock.stock_ticker)}`;
            });
        }
    }
}
// Debounce function to limit Finviz calls
function debounce(func, delay) {
    let timeoutId;
    // Return a new function that will be called
    return function (...args) {
        // Clear any existing timeout to reset the delay
        clearTimeout(timeoutId);
        // Set a new timeout in milliseconds
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}
// Create a delayed version of fetchStocks with a 500ms delay
const debouncedSearch = debounce(fetchStocks, 500);
// Add listener for search
searchInput.addEventListener('input', (e) => {
    // Call the debounced search function when there is input in the search
    debouncedSearch(e.target.value.trim());
});
// Add event listener to render the watchlist when the page finishes loading
document.addEventListener('DOMContentLoaded', renderWatchlist);
// Add navbar
document.addEventListener('DOMContentLoaded', () => {
    fetch('../navbar.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('navbar').innerHTML = data;
        });
});