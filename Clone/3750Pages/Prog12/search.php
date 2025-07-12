<?php
   header("Content-type: text/xml");
   // List of states and capitals
   $states = array (
      "Alabama" => "Montgomery",
      "Alaska" => "Juneau",
      "Arizona" => "Phoenix",
      "Arkansas" => "Little Rock",
      "California" => "Sacramento",
      "Colorado" => "Denver",
      "Connecticut" => "Hartford",
      "Delaware" => "Dover",
      "Florida" => "Tallahassee",
      "Georgia" => "Atlanta",
      "Hawaii" => "Honolulu",
      "Idaho" => "Boise",
      "Illinois" => "Springfield",
      "Indiana" => "Indianapolis",
      "Iowa" => "Des Moines",
      "Kansas" => "Topeka",
      "Kentucky" => "Frankfort",
      "Louisiana" => "Baton Rouge",
      "Maine" => "Augusta",
      "Maryland" => "Annapolis",
      "Massachusetts" => "Boston",
      "Michigan" => "Lansing",
      "Minnesota" => "Saint Paul",
      "Mississippi" => "Jackson",
      "Missouri" => "Jefferson City",
      "Montana" => "Helena",
      "Nebraska" => "Lincoln",
      "Nevada" => "Carson City",
      "New Hampshire" => "Concord",
      "New Jersey" => "Trenton",
      "New Mexico" => "Santa Fe",
      "New York" => "Albany",
      "North Carolina" => "Raleigh",
      "North Dakota" => "Bismarck",
      "Ohio" => "Columbus",
      "Oklahoma" => "Oklahoma City",
      "Oregon" => "Salem",
      "Pennsylvania" => "Harrisburg",
      "Rhode Island" => "Providence",
      "South Carolina" => "Columbia",
      "South Dakota" => "Pierre",
      "Tennessee" => "Nashville",
      "Texas" => "Austin",
      "Utah" => "Salt Lake City",
      "Vermont" => "Montpelier",
      "Virginia" => "Richmond",
      "Washington" => "Olympia",
      "West Virginia" => "Charleston",
      "Wisconsin" => "Madison",
      "Wyoming" => "Cheyenne"
   );
   // Echo the version and each of the elements in a list
   echo "<?xml version=\"1.0\" ?>\n";
   echo "<states>\n";
   // For each state
   foreach ($states as $state => $capital) {
      // If the string matches in the query from ajax, return it
      if (stristr($state, $_GET['query'])) {
         echo "<state><name>$state</name><capital>$capital</capital></state>\n";
      }
   }
   echo "</states>\n";
?>
