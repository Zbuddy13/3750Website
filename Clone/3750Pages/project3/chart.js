// API key for Market Data to enable historical prices
const marketDataApiKey = 'bV9VTlFoUjJLeUVSUUVHeE9Nb1NhdFFkcGE4bGZsR2xjd1FnTF9lVm9Yaz0';
// API key for Finnhub do display news and profile
const finnhubApiKey = 'd1trt19r01qt0evcso50d1trt19r01qt0evcso5g';

// Get stock symbol from the URL
const urlParams = new URLSearchParams(window.location.search);
let symbol = urlParams.get('symbol') || 'AAPL'; // Default case for apple if nothing is sent
// Edge case if the ticker has a . in it, replace to - (brk.b)
symbol = symbol.replace('.', '-');
// Set the chart title to the stock ticker
document.getElementById('chartTitle').textContent = `${symbol} Stock Chart`;

// Calculate date range for 3 months for chart (from today minus 3 months)
const today = new Date();
const toDateChart = today.toISOString().split('T')[0];
const fromDateChart = new Date(today.setMonth(today.getMonth() - 3)).toISOString().split('T')[0];

// Set global variables for stock news
let allCompanyNews = [];
let newsItemsDisplayed = 0;
const NEWS_CHUNK_SIZE = 5;
// Calculate date range for last 30 days for news
const newsEndDate = new Date();
const newsStartDate = new Date();
newsStartDate.setDate(newsStartDate.getDate() - 30);
// Set the stock news search dates
const toDateNews = newsEndDate.toISOString().split('T')[0];
const fromDateNews = newsStartDate.toISOString().split('T')[0];

// Set other global variables
const companyNewsContent = document.getElementById('companyNewsContent');
const newsLoadingMessage = document.getElementById('newsLoadingMessage');
const newsErrorMessage = document.getElementById('newsErrorMessage');
const noNewsMessage = document.getElementById('noNewsMessage');
const seeMoreNewsBtn = document.getElementById('seeMoreNewsBtn');


// Function to fetch stock candles
async function fetchStockCandles() {
    // Construct the marketdata url to fetch stock candles
    const url = `https://api.marketdata.app/v1/stocks/candles/D/${symbol}?from=${fromDateChart}&to=${toDateChart}&token=${marketDataApiKey}`;
    console.log('Fetching candle data from:', url);

    try {
        // Attempt to fetch from the api and log status
        const response = await fetch(url);
        console.log('Candle data response status:', response.status);
        // Set the response and display in the log
        const data = await response.json();
        console.log('Candle API response:', data);
        // If there was no data, set the error messages
        if (data.s === 'no_data') {
            // Hides the canvas element where the chart would normally be displayed
            document.getElementById('stockChart').style.display = 'none';
            // Makes the error message div visible
            document.getElementById('errorMessage').style.display = 'block';
            // Sets the text content of the error message div to inform the user about the missing data.
            document.getElementById('errorMessage').textContent = `No historical data available for symbol ${symbol}.`;
            return;
        }
        // If the reponse is not okay
        if (data.s !== 'ok') {
            // Throw an error
            throw new Error(`API error fetching candle data: ${data.error || 'Unknown error'}`);
        }
        // Prepare data for the chart with only closing data in an array
        const chartData = data.t.map((timestamp, index) => ({
            // Create an xy array where x: DateObject, y: Price
            // For each timestamp, it creates a new Date object in miliseconds
            x: new Date(timestamp * 1000),
            // Set the closing price at the index
            y: data.c[index]
        }));

        // Initialize chart by referencing the canvas
        const ctx = document.getElementById('stockChart').getContext('2d');
        // Create a new chart instance 
        new Chart(ctx, {
            type: 'line', // Line chart
            data: {
                datasets: [{
                    label: `${symbol} Closing Price Daily`,
                    data: chartData,
                    borderColor: '#F56600',
                    backgroundColor: '#522d8063',
                    fill: true,
                    tension: 0.1
                }]
            }, // Set the data for the chart with chart data
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'day'
                        },
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Closing Price ($USD)'
                        },
                        beginAtZero: false
                    }
                } // Set the x y headings
            }
        });
    }
    // If there is an error with the graph
    catch (error) {
        // Error to the console 
        console.error('Error details fetching candle data:', error);
        // Hides the chart canvas
        document.getElementById('stockChart').style.display = 'none';
        // Makes the error message div visible
        document.getElementById('errorMessage').style.display = 'block';
        // Set error message on the page, including the error's message property
        document.getElementById('errorMessage').textContent = `Error fetching chart data: ${error.message}. Please check the console for details.`;
    }
}

// Fetch company profile
async function fetchCompanyProfile() {
    // Setup url to fetch profile
    const url = `https://finnhub.io/api/v1/stock/profile2?symbol=${encodeURIComponent(symbol)}&token=${finnhubApiKey}`;
    console.log('Fetching company profile from:', url);
    try {
        // Attempt to fetch from finnhub and display status in console
        const response = await fetch(url);
        console.log('Company profile response status:', response.status);
        const data = await response.json();
        console.log('Company profile API response:', data);
        // Gather dom elements for profile error and content
        const profileErrorMessage = document.getElementById('profileErrorMessage');
        const companyProfileContent = document.getElementById('companyProfileContent');
        // Check to see if the profile is empty or errors
        if (Object.keys(data).length === 0 || data.error) {
            // Set the error message 
            profileErrorMessage.style.display = 'block';
            profileErrorMessage.textContent = `No company profile data available for ${symbol}.`;
            companyProfileContent.style.display = 'none';
            return;
        }
        // Display company profile data by setting dom elements
        document.getElementById('profileName').textContent = data.name || 'N/A';
        document.getElementById('profileExchange').textContent = data.exchange || 'N/A';
        document.getElementById('profileIndustry').textContent = data.industry || 'N/A';
        // Set the web url for company
        const websiteLink = document.getElementById('profileWebURL');
        if (data.weburl) {
            // Set proper href if one exist
            websiteLink.innerHTML = `<a href="${data.weburl}" target="_blank">${data.weburl}</a>`;
        } 
        else {
            // Set to na if not given
            websiteLink.textContent = 'N/A';
        }
        // Set the ipo date and phone
        document.getElementById('profileIPODate').textContent = data.ipo || 'N/A';
        document.getElementById('profilePhone').textContent = data.phone || 'N/A';
        // Hide the error message
        profileErrorMessage.style.display = 'none';
        companyProfileContent.style.display = 'block';
    } 
    // Set the error messages and elements if found
    catch (error) {
        console.error('Error details fetching company profile:', error);
        document.getElementById('companyProfileContent').style.display = 'none';
        document.getElementById('profileErrorMessage').style.display = 'block';
        document.getElementById('profileErrorMessage').textContent = `Error fetching company profile: ${error.message}. Please check the console for details.`;
    }
}

// Function to render a chunk of news with an index input
// This allows for the see more articles functionality to increment the elements displayed
function renderNewsChunk(startIndex) {
    // Set the end index
    const endIndex = Math.min(startIndex + NEWS_CHUNK_SIZE, allCompanyNews.length);
    // Set the news to render from the news array
    const newsToRender = allCompanyNews.slice(startIndex, endIndex);
    // Iterate over each news object in the render array
    newsToRender.forEach(news => {
        // Create a div and set the class
        const newsItem = document.createElement('div');
        newsItem.className = 'news-item';
        // Convert the news date
        const newsDate = new Date(news.datetime * 1000).toLocaleDateString();
        // Set the html of the new news element
        newsItem.innerHTML = `
            <h3><a href="${news.url}" target="_blank">${news.headline}</a></h3>
            <p>${news.summary}</p>
            <div class="news-source-date">${news.source} - ${newsDate}</div>
        `;
        // Append this news element to the parent
        companyNewsContent.appendChild(newsItem);
    });
    // Update the count of displayed items
    newsItemsDisplayed = endIndex;
    // Show/hide and enable/disable see more button
    if (newsItemsDisplayed < allCompanyNews.length) {
        seeMoreNewsBtn.style.display = 'block';
        seeMoreNewsBtn.disabled = false;
    } 
    else {
        // Hide if all news is shown
        seeMoreNewsBtn.style.display = 'none';
    }
}

// Fetch company news
async function fetchCompanyNews() {
    // Construct the url and display in the log
    const url = `https://finnhub.io/api/v1/company-news?symbol=${encodeURIComponent(symbol)}&from=${fromDateNews}&to=${toDateNews}&token=${finnhubApiKey}`;
    console.log('Fetching company news from:', url);
    // Set the messages and content
    newsLoadingMessage.style.display = 'block';
    newsErrorMessage.style.display = 'none';
    noNewsMessage.style.display = 'none';
    // Clear previous news
    companyNewsContent.innerHTML = '';
    // Hide button initially
    seeMoreNewsBtn.style.display = 'none';

    try {
        // Attempt to fetch the news, log the response
        const response = await fetch(url);
        console.log('Company news response status:', response.status);
        const data = await response.json();
        console.log('Company news API response:', data);
        // Hide the loading news element
        newsLoadingMessage.style.display = 'none';
        // If the recieved data is empty
        if (!data || data.length === 0) {
            noNewsMessage.style.display = 'block';
            return;
        }
        // Sort news by datetime (newest to oldest)
        allCompanyNews = data.sort((a, b) => new Date(b.datetime) - new Date(a.datetime));
        // Reset counter
        newsItemsDisplayed = 0;
        // Render the first chunk of news
        renderNewsChunk(0);

    } 
    // Catch and display any errors from fetch
    catch (error) {
        console.error('Error details fetching company news:', error);
        newsLoadingMessage.style.display = 'none';
        newsErrorMessage.style.display = 'block';
        newsErrorMessage.textContent = `Error fetching company news: ${error.message}. Please check the console for details.`;
    }
}

// Event listener for "See More News" button to render more news items
seeMoreNewsBtn.addEventListener('click', () => {
    renderNewsChunk(newsItemsDisplayed);
});

// Fetch data on page load by calling functions
document.addEventListener('DOMContentLoaded', () => {
    fetchStockCandles();
    fetchCompanyProfile();
    fetchCompanyNews();
});

// Navbar code
document.addEventListener('DOMContentLoaded', () => {
    fetch('../navbar.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('navbar').innerHTML = data;
        });
});