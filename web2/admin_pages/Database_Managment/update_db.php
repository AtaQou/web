<?php
include 'D:\Program Files\xampp\htdocs\web\db_connection.php';

// Get the raw POST data
$rawData = file_get_contents('php://input');

// Log the raw JSON data to a file for inspection
file_put_contents('log.txt', "Raw Data: " . $rawData . PHP_EOL, FILE_APPEND);

$data = json_decode($rawData, true);

// Check for JSON errors
if (json_last_error() !== JSON_ERROR_NONE) {
    file_put_contents('log.txt', "JSON Error: " . json_last_error_msg() . PHP_EOL, FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data received']);
    exit();
}

// Assuming your table structures are:
// items(id, name, category)
// item_details(id, item_id, detail_name, detail_value)

foreach ($data['items'] as $item) {
    $itemId = $conn->real_escape_string($item['id']);
    $itemName = $conn->real_escape_string($item['name']);
    $itemCategory = $conn->real_escape_string($item['category']);

    // Insert the item into the items table
    $sql = "INSERT INTO items (id, name, category)
            VALUES ('$itemId', '$itemName', '$itemCategory')
            ON DUPLICATE KEY UPDATE
            name = VALUES(name), category = VALUES(category)";
    
    if ($conn->query($sql) === TRUE) {
        // Successfully inserted/updated the item
    } else {
        echo json_encode(['success' => false, 'message' => 'Error inserting item: ' . $conn->error]);
        exit();
    }

    // Now, insert the item details
    foreach ($item['details'] as $detail) {
        $detailName = $conn->real_escape_string($detail['detail_name']);
        $detailValue = $conn->real_escape_string($detail['detail_value']);

        $detailSql = "INSERT INTO itemdetails (id, detail_name, detail_value)
                      VALUES ('$itemId', '$detailName', '$detailValue')";
        
        if ($conn->query($detailSql) !== TRUE) {
            echo json_encode(['success' => false, 'message' => 'Error inserting item detail: ' . $conn->error]);
            exit();
        }
    }
}

echo json_encode(['success' => true, 'message' => 'Database updated successfully']);
$conn->close();
?>
