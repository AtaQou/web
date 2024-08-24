<?php
include '../../../includes/db_connect.php';
session_start();

$citizen_username = $_SESSION['username'];

try {
    $stmt = $conn->prepare("
        SELECT offers.id, inventory.name AS item_name, offers.status, offers.quantity, offers.offer_date 
        FROM offers 
        JOIN inventory ON offers.item_id = inventory.id 
        WHERE offers.citizen_username = ? 
        ORDER BY offers.offer_date DESC
    ");
    $stmt->execute([$citizen_username]);
    $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($offers);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
