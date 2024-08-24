<?php
if (isset($_GET['url'])) {
    $url = $_GET['url'];
    
    // Use cURL to fetch the URL content
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        header('Content-Type: application/json');
        echo $response;
    } else {
        http_response_code($httpCode);
        echo json_encode(['error' => 'Failed to fetch the JSON file. HTTP Status Code: ' . $httpCode]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'No URL provided']);
}
?>
