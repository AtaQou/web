<?php
include '../../../includes/db_connect.php';
session_start();

$citizen_username = $_SESSION['username'];
$offer_id = $_POST['offer_id'];

try {
    // Διαγραφή προσφοράς
    $delete_stmt = $conn->prepare("
        DELETE FROM offers 
        WHERE id = :offer_id AND citizen_username = :citizen_username
    ");
    $delete_stmt->execute(['offer_id' => $offer_id, 'citizen_username' => $citizen_username]);

    // Αποστολή της σωστής απάντησης JSON
    echo json_encode(['message' => 'Offer canceled successfully.']);
} catch (PDOException $e) {
    echo json_encode(['message' => 'Error: ' . $e->getMessage()]);
}
?>
