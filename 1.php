<?php

// Get the search query from the URL parameter, default to empty if not provided
$searchQuery = isset($_GET['search']) ? urlencode($_GET['search']) : '';

// Define the base API URL
$baseApiUrl = "https://www.googleapis.com/blogger/v3/blogs/1188115979390920205/posts";

// Build the API URL based on the presence of a search query
if ($searchQuery) {
    // Search by labels if the search query is provided
    $apiUrl = "{$baseApiUrl}/search?q=label:{$searchQuery}&key=AIzaSyCdJl4_H0fnN6d0zGO1Ek9R3g2qDkj162I";
} else {
    // Retrieve all posts if no search query is provided
    $apiUrl = "{$baseApiUrl}?key=AIzaSyCdJl4_H0fnN6d0zGO1Ek9R3g2qDkj162I";
}

// Make the API request
$response = file_get_contents($apiUrl);

// Decode the JSON response
$data = json_decode($response, true);

// Prepare an array to store the simplified data
$simplifiedData = [
    'items_indicator' => [
        'search_indicator' => ''
    ],
    'items_search' => []
];

// Check if items are available
if (!empty($data['items'])) {
    // Loop through each item and extract the required fields
    foreach ($data['items'] as $item) {
        // Initialize the label variables with default values
        $label_1 = '';
        $label_2 = '';
        $label_3 = '';
        $label_4 = '';
        $img = '';

        // Assign labels if they exist
        if (isset($item['labels'])) {
            if (isset($item['labels'][0])) {
                $label_1 = $item['labels'][0];
            }
            if (isset($item['labels'][1])) {
                $label_2 = $item['labels'][1];
            }
            if (isset($item['labels'][2])) {
                $label_3 = $item['labels'][2];
            }
            if (isset($item['labels'][3])) {
                $label_4 = $item['labels'][3];
            }
            
            // Determine the IMG value based on Label_3
            if ($label_3 === 'mp4') {
                $img = 'http://www.mysite.com';
            } elseif ($label_3 === 'pdf') {
                $img = 'http://www.ok.com';
            }
        }

        // Extract the required fields
        $simplifiedItem = [
            'title' => $item['title'],
            'content' => $item['content'],
            'Label_1' => $label_1,
            'Label_2' => $label_2,
            'Label_3' => $label_3,
            'Label_4' => $label_4,
            'IMG' => $img
        ];

        // Add the simplified item to the simplified data array
        $simplifiedData['items_search'][] = $simplifiedItem;
    }
} else {
    // Set the search_indicator if no items are found or no search query was provided
    $simplifiedData['items_indicator']['search_indicator'] = 'No Result';
}

// Set the header to output JSON content
header('Content-Type: application/json');

// Output the simplified data in JSON format without escaping slashes
echo json_encode($simplifiedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

?>
