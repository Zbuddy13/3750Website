<?php
// Set the main header to indicate it will return json
header('Content-Type: application/json');
// Function to count vowels in a word
function countVowels($word) {
    // Use pattern match to count the number of vowels in a word
    return preg_match_all('/[aeiou]/i', $word);
}
// Read words from file and process
function processWords() {
    // Set the file name and word arry
    $file = 'words.txt';
    $words = [];
    // If the file is found
    if (file_exists($file)) {
        // Read the file and convert it to line while skipping new lines and empty lines
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        // For each of the words found
        foreach ($lines as $word) {
            // Trim any whitespace
            $word = trim($word);
            // Count the vowels
            $vowelCount = countVowels($word);
            // Add the word and count to the array
            $words[$vowelCount][] = $word;
        }
    }
    return $words;
}
// Parse the url passed to php
$action = isset($_GET['action']) ? $_GET['action'] : '';
// If the html request to get vowel counts method
if ($action === 'get_vowel_counts') {
    // Call the process words function
    $words = processWords();
    // Set the array keys which hold the vowel counts to a variable
    $vowelCounts = array_keys($words);
    // Sort through the array of vowel counts
    sort($vowelCounts);
    // Return the proper list of counts (1-infinity) in order
    echo json_encode(['vowelCounts' => $vowelCounts]);
} 
// If the html request to return a list of words matching a vowel count
elseif ($action === 'get_words' && isset($_GET['vowel_count'])) {
    // Set the vowel count to the count sent from the html
    $vowelCount = (int)$_GET['vowel_count'];
    // Call process words
    $words = processWords();
    // Set wordList to the words that match the vowel count
    $wordList = isset($words[$vowelCount]) ? $words[$vowelCount] : [];
    // A functionality: Sort words by length (shortest to longest)
    usort($wordList, function($a, $b) {
        return strlen($a) - strlen($b);
    });
    echo json_encode(['words' => $wordList]);
} 
// If the request is invalid
else {
    echo json_encode(['error' => 'Invalid action']);
}
?>