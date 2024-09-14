<?php
include '../../../includes/db_connect.php';

try {
    $stmt = $conn->prepare("SELECT id, name FROM inventory");
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['items' => $items]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
