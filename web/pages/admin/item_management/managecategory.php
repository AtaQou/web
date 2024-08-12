<?php
session_start();
include '../../../includes/db_connect.php';
include '../../../includes/admin_access.php';

if (!isset($_SESSION['decoded_data'])) {
    die('Error: No data to insert.');
}

$data = $_SESSION['decoded_data'];
$selected_categories = $_POST['selected_categories'] ?? [];
$selected_items = $_POST['selected_items'] ?? [];

try {
    // Insert selected categories into the database
    foreach ($data['categories'] as $category) {
        if (in_array($category['id'], $selected_categories)) {
            $stmt = $conn->prepare("INSERT INTO categories (id, name, description) VALUES (:id, :name, :description)");
            $stmt->bindParam(':id', $category['id']);
            $stmt->bindParam(':name', $category['category_name']);
            $stmt->bindParam(':description', $category['description']); // Use correct description
            $stmt->execute();
        }
    }

    // Insert selected items into the database
    foreach ($data['items'] as $item) {
        if (in_array($item['id'], $selected_items)) {
            $quantity = $_POST['quantity_' . $item['id']] ?? 1; // Get quantity or default to 1
            $stmt = $conn->prepare("INSERT INTO inventory (id, category_id, name, description, quantity) VALUES (:id, :category_id, :name, :description, :quantity)");
            $stmt->bindParam(':id', $item['id']);
            $stmt->bindParam(':category_id', $item['category']);
            $stmt->bindParam(':name', $item['name']);
            $stmt->bindParam(':description', $item['description']); // Use correct description
            $stmt->bindParam(':quantity', $quantity);
            $stmt->execute();
        }
    }

    $message = 'Selected data inserted successfully.';
} catch (PDOException $e) {
    $message = 'Database error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Insertion Result</title>
</head>
<body>
    <p><?php echo htmlspecialchars($message); ?></p>
    <button onclick="window.location.href='manage.html'">Back to Manage Page</button>
</body>
</html>
