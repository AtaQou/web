<?php
include '../../../includes/db_connect.php';
include '../../../includes/admin_access.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];

    try {
        if ($type === 'category') {
            // Insert new category
            $name = $_POST['category_name'];
            $description = $_POST['category_description'];

            if (empty($name)) {
                echo json_encode(['status' => 'error', 'message' => 'Category name is required.']);
                exit;
            }

            $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $stmt->execute([$name, $description]);

            echo json_encode(['status' => 'success']);
        } elseif ($type === 'item') {
            // Insert new item
            $name = $_POST['item_name'];
            $category_id = $_POST['category_id'];
            $description = $_POST['item_description'];
            $quantity = $_POST['quantity'];

            if (empty($name) || empty($category_id)) {
                echo json_encode(['status' => 'error', 'message' => 'Item name and category are required.']);
                exit;
            }

            $stmt = $conn->prepare("INSERT INTO inventory (category_id, name, description, quantity) VALUES (?, ?, ?, ?)");
            $stmt->execute([$category_id, $name, $description, $quantity]);

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid form type.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
