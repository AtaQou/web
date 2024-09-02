<?php
session_start();
include '../../../includes/db_connect.php';
include '../../../includes/admin_access.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $items = $_POST['items'] ?? [];

    if (empty($title) || empty($description)) {
        echo json_encode(['success' => false, 'message' => 'Title and description are required.']);
        exit;
    }

    try {
        // Insert new announcement
        $stmt = $conn->prepare("
            INSERT INTO announcements (title, description)
            VALUES (:title, :description)
        ");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->execute();

        $announcement_id = $conn->lastInsertId();

        // Insert related items
        if (!empty($items)) {
            $stmt = $conn->prepare("
                INSERT INTO announcement_items (announcement_id, item_id)
                VALUES (:announcement_id, :item_id)
            ");
            foreach ($items as $item_id) {
                $stmt->bindParam(':announcement_id', $announcement_id);
                $stmt->bindParam(':item_id', $item_id);
                $stmt->execute();
            }
        }

        echo json_encode(['success' => true, 'message' => 'Announcement created successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
