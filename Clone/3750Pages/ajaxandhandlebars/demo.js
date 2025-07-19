// Unchanged from source unless indicated
var pageCounter = 1;
var animalContainer = document.getElementById("animal-info");
var btn = document.getElementById("btn");
// Added variables for handlebars

// Grab the animal template definded in the html
var animalTemplate = document.getElementById("animalTemplate").innerHTML;
// Create compiled template using handlebars comile
var compiledTemplate = Handlebars.compile(animalTemplate);

btn.addEventListener("click", function() {
    var ourRequest = new XMLHttpRequest();
    ourRequest.open('GET', 'https://learnwebcode.github.io/json-example/animals-' + pageCounter + '.json');
    ourRequest.onload = function() {
        if (ourRequest.status >= 200 && ourRequest.status < 400) {
        var ourData = JSON.parse(ourRequest.responseText);
        renderHTML(ourData);
        } else {
        console.log("We connected to the server, but it returned an error.");
        }
        
    };

    ourRequest.onerror = function() {
        console.log("Connection error");
    };

    ourRequest.send();
    pageCounter++;
    if (pageCounter > 3) {
        btn.classList.add("hide-me");
    }
});

function renderHTML(data) {
    // Added elements to incorporate handlebars

    // Generate the html from the compiled template
    var generatedHTML = compiledTemplate(data);
    // Insert the html into the animal-info container
    animalContainer.insertAdjacentHTML('beforeend', generatedHTML);
}