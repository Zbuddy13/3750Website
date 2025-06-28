// initialize the names array to hold names
var names = [];

function SortNames() {
   // Get the input field and its value from the html newname input
   const newNameInput = document.theform.newname;
   // Set the value in the box to thename and remove whitespace with trim
   let thename = newNameInput.value.trim();

   // Extra: Case where the name is empty alert user
   if (thename === "") {
      alert("Name cannot be blank!");
      // Return focus to the input field after alert
      newNameInput.focus();
      // Exit the function
      return;
   }

   // Use the toUpperCase to convert the input to all uppercase
   thename = thename.toUpperCase();

   // Push the uppercase name to the array of names
   names.push(thename);

   // Sort the array with sort method
   names.sort();

   // Append the index number to the name
   const numberedNames = names.map((name, index) => 
   // Map the existing names by taking the index of the name in the list and 
   // adding a . and space then join this with a new line to the numbered Names list
   // EX James Shaw is changed to 1. James Shaw /n
   {return (index + 1) + ". " + name;}).join("\n");

   // Print the sorted and numbered names in the textarea 
   document.theform.sorted.value = numberedNames;

   // Clear the input field and set focus back to the input area
   newNameInput.value = "";
   newNameInput.focus();
}

// This should be run after the HTML document is fully loaded.
window.onload = function() {
   // Assign newNameInputElement 
   const newNameInput = document.theform.newname;
   // If there is something in newNameInput
   if (newNameInput) {
      // Check if the key pressed was 'Enter'
      newNameInput.addEventListener('keypress', function(event) {
         // event.key is looking for the enter
         if (event.key === 'Enter') {
               // Prevent the default Enter key action
               event.preventDefault();
               // Call the SortNames function after
               SortNames();
         }
      });
   }
};

