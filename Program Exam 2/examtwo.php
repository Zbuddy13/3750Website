<?php
header('Content-Type: application/json');

// Function to count vowels in a word
function countVowels($word) {
    return preg_match_all('/[aeiou]/i', $word);
}

// Read words from file and process
function processWords() {
    $file = 'words.txt';
    $words = [];
    
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $word) {
            $word = trim($word);
            $vowelCount = countVowels($word);
            $words[$vowelCount][] = $word;
        }
    }
    
    return $words;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'get_vowel_counts') {
    $words = processWords(); // Add semicolon here
    $vowelCounts = array_keys($words);
    sort($vowelCounts);
    echo json_encode(['vowelCounts' => $vowelCounts]);
} elseif ($action === 'get_words' && isset($_GET['vowel_count'])) {
    $vowelCount = (int)$_GET['vowel_count'];
    $words = processWords();
    $wordList = isset($words[$vowelCount]) ? $words[$vowelCount] : [];
    // Sort words by length (shortest to longest)
    usort($wordList, function($a, $b) {
        return strlen($a) - strlen($b);
    });
    echo json_encode(['words' => $wordList]);
} else {
    echo json_encode(['error' => 'Invalid action']);
}
?>