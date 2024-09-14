<?php
session_start();
include '../../../includes/db_connect.php';
include '../../../includes/admin_access.php';

// Αύξηση του ορίου μνήμης για να διαχειριστεί μεγάλα JSON αρχεία
ini_set('memory_limit', '512M'); // Αυξήστε το όριο αν χρειαστεί

// Initialize data variable
$data = null;

// Function to remove BOM and trim whitespace
function removeBOM($string) {
    if (substr($string, 0, 3) === "\xEF\xBB\xBF") {
        $string = substr($string, 3);
    }
    return trim($string);
}

// Function to diagnose JSON issues
function diagnoseJSONError($json) {
    echo "JSON Data: <pre>" . htmlspecialchars($json) . "</pre>"; // Display the JSON string
    echo "JSON Error: " . json_last_error_msg(); // Show the last error message from json_decode
    exit; // Stop execution to view the output
}

// URL of the JSON file
$json_url = "http://usidas.ceid.upatras.gr/web/2023/export.php";

try {
    // Διαβάζει το JSON από το URL
    $json_data = file_get_contents($json_url);

    if ($json_data === false) {
        throw new Exception('Error: Unable to fetch data from the URL.');
    }

    // Εμφανίζει το περιεχόμενο για debugging
    echo "<h3>Raw JSON Data:</h3><pre>" . htmlspecialchars($json_data) . "</pre>";

    // Αφαιρεί το BOM και trim
    $json_data = removeBOM($json_data);

    // Ελέγχει την κωδικοποίηση του JSON
    if (!mb_detect_encoding($json_data, 'UTF-8', true)) {
        throw new Exception('Error: JSON data is not UTF-8 encoded.');
    }

    // Decode JSON με error handling
    $data = json_decode($json_data, true);

    // Ελέγχει αν υπήρξε σφάλμα κατά την αποκωδικοποίηση
    if ($data === null) {
        diagnoseJSONError($json_data);
    }

    // Εμφανίζει τα decoded δεδομένα για debugging
    echo "<h3>Decoded JSON Data:</h3><pre>" . print_r($data, true) . "</pre>";

    // Αποθηκεύει τα δεδομένα στη συνεδρία
    $_SESSION['decoded_data'] = $data;

    // Ανακατεύθυνση στη σελίδα επιλογής
    header('Location: loadjson.php');
    exit;

} catch (Exception $e) {
    die($e->getMessage());
}
?>
