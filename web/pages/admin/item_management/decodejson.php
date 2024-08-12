<?php
session_start();
include '../../../includes/db_connect.php';
include '../../../includes/admin_access.php';

// Initialize data variable
$data = null;

// Check if JSON URL is provided
if (isset($_POST['json_url']) && !empty($_POST['json_url'])) {
    $json_url = $_POST['json_url'];
    $json_data = file_get_contents($json_url);

    if ($json_data === false) {
        die('Error: Unable to fetch data from the URL.');
    }
    $data = json_decode($json_data, true);

} elseif (isset($_FILES['json_file']) && $_FILES['json_file']['error'] == UPLOAD_ERR_OK) {
    $json_data = file_get_contents($_FILES['json_file']['tmp_name']);
    $data = json_decode($json_data, true);
}

// Check if JSON decoding was successful
if ($data === null) {
    die('Error: Unable to decode JSON data.');
}

// Save the decoded data in a PHP session
$_SESSION['decoded_data'] = $data;

// Redirect to the selection page
header('Location: loadjson.php');
exit;
?>
