<?php
include '../../../includes/db_connect.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->query("SELECT id, name FROM inventory ORDER BY name ASC");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($items);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
