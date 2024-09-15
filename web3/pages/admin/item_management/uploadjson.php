<?php
session_start();
include '../../../includes/db_connect.php';
include '../../../includes/admin_access.php';

// Αυξήστε το όριο μνήμης αν χρειαστεί
ini_set('memory_limit', '512M');

$response = ['status' => 'error', 'message' => 'Error uploading file'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['json_file'])) {
    $file = $_FILES['json_file'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        // Διαβάζει το περιεχόμενο του αρχείου
        $json_data = file_get_contents($file['tmp_name']);

        if ($json_data !== false) {
            // Αφαιρεί το BOM και trim
            $json_data = trim($json_data);

            // Decode JSON με error handling
            $data = json_decode($json_data, true);

            if ($data !== null) {
                $_SESSION['decoded_data'] = $data;
                $response = ['status' => 'success', 'message' => 'Data successfully saved to debug file.'];
            } else {
                $response['message'] = 'Error decoding JSON: ' . json_last_error_msg();
            }
        } else {
            $response['message'] = 'Error reading file contents.';
        }
    } else {
        $response['message'] = 'File upload error: ' . $file['error'];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
