<?php
include 'D:\Program Files\xampp\htdocs\web\db_connection.php'; // Adjust path as needed

$sql = "SELECT i.id, i.name, i.category, i.quantity, d.detail_name, d.detail_value
        FROM items i
        LEFT JOIN item_details d ON i.id = d.item_id";

$result = $conn->query($sql);

$products = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $productId = $row['id'];

        // If the product is not already in the array, add it
        if (!isset($products[$productId])) {
            $products[$productId] = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'category' => $row['category'],
                'quantity' => $row['quantity'],
                'details' => array()
            );
        }

        // Add the detail to the product's details array
        $products[$productId]['details'][] = array(
            'detail_name' => $row['detail_name'],
            'detail_value' => $row['detail_value']
        );
    }
}

$conn->close();

echo json_encode(array_values($products)); // Convert associative array to indexed array for JSON
?>
