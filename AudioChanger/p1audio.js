// Title index and isplaying variable for future use
let currentTitleIndex = 0;
let isPlaying = false;

// List of titles created for oil video
let titles = [
    { time: 0, name: "Start" },
    { time: 40, name: "Room Temp Flow Test" },
    { time: 120, name: "Cold Flow Test" },
    { time: 127, name: "Oil Descriptions" },
    { time: 162, name: "Heat Test" },
    { time: 528, name: "Evap Results" },
    { time: 723, name: "Flow Results" }
];

// Grab the elements from the dom
const audioElement = document.getElementById('audioPlayer');
const titleListElement = document.getElementById('playlist');
const currentTimeElement = document.getElementById('currentTime');

// Function used to put the time in proper format by returning in a string
function formatTime(seconds) {
    const min = Math.floor(seconds / 60);
    const sec = Math.floor(seconds % 60);
    return `${min}:${sec.toString().padStart(2, '0')}`;
}

// Used to update the current time displayed
function updateCurrentTime() {
    // Pull the current time and update the element
    currentTimeElement.textContent = formatTime(audioElement.currentTime);
    updateCurrentTitle();
}

// Function to toggle play pause button
function togglePlayPause() {
    // If currently playing
    if (isPlaying) {
        // Pause the audio
        audioElement.pause();
        // Set global to false
        isPlaying = false;
        // Set the dom to play button
        document.querySelector('button[onclick="togglePlayPause()"]').textContent = 'Play';
    } else {
        // Play the audio element
        audioElement.play();
        // Set global to true
        isPlaying = true;
        // Set the dom to pause button
        document.querySelector('button[onclick="togglePlayPause()"]').textContent = 'Pause';
    }
}

// Function to rewind 5 seconds
function rewind() {
    // Subtract 5 seconds
    audioElement.currentTime = Math.max(0, audioElement.currentTime - 5);
    // Check for title change
    updateCurrentTitle();
}

// Function to fast forward 5 seconds
function forward() {
    audioElement.currentTime = Math.min(audioElement.duration, audioElement.currentTime + 5);
    updateCurrentTitle();
}

// Used to update which title element is close to the time mark
function updateCurrentTitle() {
    // For all the time lenths of titles
    for (let i = titles.length - 1; i >= 0; i--) {
        // If the current time is greater or = the title found
        if (audioElement.currentTime >= titles[i].time) {
            // Set the index and highlight the closest title
            currentTitleIndex = i;
            highlightCurrentTitle();
            break;
        }
    }
}

// Function  used to highlight the tile in orange that is closest to the current time
function highlightCurrentTitle() {
    // Grab all the current titles
    const domTitles = titleListElement.getElementsByClassName('titleList-item');
    // Set the background color for each element depending on current index set by update title
    for (let i = 0; i < domTitles.length; i++) {
        // Set to orange if current closest time
        domTitles[i].style.backgroundColor = i === currentTitleIndex ? '#F56600' : '#f8f9fa';
    }
}

// Used to render the title buttons
function renderTitleList() {
    // Clear the element
    titleListElement.innerHTML = '';
    // Sort the tile by time
    titles.sort((a, b) => a.time - b.time);
    // For each of the titles in the list
    titles.forEach((title, index) => {
        // Create a new div and assign the class for each title
        const div = document.createElement('div');
        div.className = 'titleList-item';
        // Create the html with the name and time stamp
        div.innerHTML = `${title.name} - (${formatTime(title.time)})`;
        // For each of the titles, assign on click
        div.onclick = () => {
            // Set the current time to the title time
            audioElement.currentTime = title.time;
            // Set the title index
            currentTitleIndex = index;
            // Highlight the current titles
            highlightCurrentTitle();
            // If it is not playing 
            if (!isPlaying) {
                // Auto play the content from that time stamp
                audioElement.play();
                // Set global variable
                isPlaying = true;
                // Set the play pause button context to pause
                document.querySelector('button[onclick="togglePlayPause()"]').textContent = 'Pause';
            }
        };
        // Append the title list
        titleListElement.appendChild(div);
    });
    // Call higlight current title after list is rendered
    highlightCurrentTitle();
}

// A function: Add title to the title list
function addTitle() {
    // Propt the user to enter title name
    const name = prompt("Enter title name:");
    if (name) {
        // Push the title to the titles list and render the list
        titles.push({ time: Math.floor(audioElement.currentTime), name });
        renderTitleList();
    }
}

// A function: Remove title from list
function removeTitle() {
    // If the title list greater than 0
    if (titles.length > 0) {
        // Remove the current title index
        titles.splice(currentTitleIndex, 1);
        // Update the index to ensure its valid
        currentTitleIndex = Math.min(currentTitleIndex, titles.length - 1);
        // Render the title list
        renderTitleList();
    }
}

// Assign the updateCurrentTime to the audio player on time update element
// This updates the current time element based on the player time
audioElement.ontimeupdate = updateCurrentTime;

// When the audio metadata is loaded, render the title list
audioElement.onloadedmetadata = () => {
    renderTitleList();
};