<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../../includes/db_connect.php';
include '../../../includes/admin_access.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_SESSION['decoded_data'])) {
            throw new Exception('Error: No data to insert.');
        }

        $data = $_SESSION['decoded_data'];
        $selected_categories = $_POST['selected_categories'] ?? [];
        $selected_items = $_POST['selected_items'] ?? [];

        // Insert selected categories into the database
        if (isset($data['categories']) && is_array($data['categories'])) {
            foreach ($data['categories'] as $category) {
                if (in_array($category['id'], $selected_categories)) {
                    // Check if category already exists to prevent duplicates
                    $stmt_check = $conn->prepare("SELECT id FROM categories WHERE id = :id");
                    $stmt_check->bindParam(':id', $category['id']);
                    $stmt_check->execute();

                    if ($stmt_check->rowCount() === 0) { // Only insert if category doesn't exist
                        $stmt = $conn->prepare("INSERT INTO categories (id, name) VALUES (:id, :name)");
                        $stmt->bindParam(':id', $category['id']);
                        $stmt->bindParam(':name', $category['category_name']);
                        $stmt->execute();
                    }
                }
            }
        }

        // Insert selected items into the database
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                if (in_array($item['id'], $selected_items)) {
                    $quantity = $_POST['quantity_' . $item['id']] ?? 1; // Get quantity or default to 1

                    // Ensure the category exists before inserting the item
                    $stmt_check_category = $conn->prepare("SELECT id FROM categories WHERE id = :category_id");
                    $stmt_check_category->bindParam(':category_id', $item['category']);
                    $stmt_check_category->execute();

                    if ($stmt_check_category->rowCount() > 0) { // Insert only if the category exists
                        // Build description from the 'details' array
                        $description = '';
                        if (isset($item['details']) && is_array($item['details'])) {
                            foreach ($item['details'] as $detail) {
                                if (isset($detail['detail_name']) && isset($detail['detail_value'])) {
                                    $description .= $detail['detail_name'] . ': ' . $detail['detail_value'] . ', ';
                                }
                            }
                            $description = rtrim($description, ', ');
                        }

                        // Check if item already exists to prevent duplicate entries
                        $stmt_check = $conn->prepare("SELECT id FROM inventory WHERE id = :id");
                        $stmt_check->bindParam(':id', $item['id']);
                        $stmt_check->execute();

                        if ($stmt_check->rowCount() === 0) { // Only insert if item doesn't exist
                            $stmt = $conn->prepare("INSERT INTO inventory (id, category_id, name, description, quantity) VALUES (:id, :category_id, :name, :description, :quantity)");
                            $stmt->bindParam(':id', $item['id']);
                            $stmt->bindParam(':category_id', $item['category']);
                            $stmt->bindParam(':name', $item['name']);
                            $stmt->bindParam(':description', $description);
                            $stmt->bindParam(':quantity', $quantity);
                            $stmt->execute();
                        }
                    } else {
                        throw new Exception("Error: Category ID " . htmlspecialchars($item['category']) . " does not exist.");
                    }
                }
            }
        }

        $response['status'] = 'success';
        $response['message'] = 'Selected data inserted successfully.';
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
exit;