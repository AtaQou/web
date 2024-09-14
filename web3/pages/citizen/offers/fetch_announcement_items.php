<?php
include '../../../includes/db_connect.php';
include '../../../includes/citizen_access.php';
session_start();

$announcement_id = $_GET['announcement_id'];

try {
    $stmt = $conn->prepare("
        SELECT inventory.id, inventory.name 
        FROM announcement_items 
        JOIN inventory ON announcement_items.item_id = inventory.id 
        WHERE announcement_items.announcement_id = ?
    ");
    $stmt->execute([$announcement_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($items);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
