<?php
include '../db_connection.php'; // Adjust path as needed

// Get the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data received']);
    exit();
}

$product = $data['product'];
$quantity = $data['quantity'];
$category = $data['category'];

$sql = "INSERT INTO tasks (product_id, quantity, category) VALUES ('$product', '$quantity', '$category')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Task created successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error creating task: ' . $conn->error]);
}

$conn->close();
?>
