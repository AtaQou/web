<?php
// Start session at the top
session_start();

// Define the URL of the JSON data
$url = 'http://usidas.ceid.upatras.gr/web/2023/export.php';

// Fetch the JSON data
$json_data = file_get_contents($url);

// Check if the data was fetched successfully
if ($json_data === false) {
    die('Error: Unable to fetch data from the URL.');
}

// Decode the JSON data into a PHP array
$data = json_decode($json_data, true);

// Check if JSON decoding was successful
if ($data === null) {
    die('Error: Unable to decode JSON data.');
}

// Save the decoded data in a PHP session
$_SESSION['decoded_data'] = $data;

// Redirect to the next script for inserting data
header('Location: insertdataindb.php');
exit;
?>

