<?php
include '../../../includes/db_connect.php';
include '../../../includes/citizen_access.php';
session_start();

try {
    $stmt = $conn->prepare("
        SELECT a.id AS announcement_id, a.title, a.description, 
               i.id AS item_id, i.name AS item_name
        FROM announcements a
        LEFT JOIN announcement_items ai ON a.id = ai.announcement_id
        LEFT JOIN inventory i ON ai.item_id = i.id
        ORDER BY a.id, i.id
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $announcements = [];
    foreach ($rows as $row) {
        $announcement_id = $row['announcement_id'];

        if (!isset($announcements[$announcement_id])) {
            $announcements[$announcement_id] = [
                'title' => $row['title'],
                'description' => $row['description'],
                'items' => []
            ];
        }

        if ($row['item_name']) {
            $announcements[$announcement_id]['items'][] = [
                'id' => $row['item_id'],
                'name' => $row['item_name']
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($announcements);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
