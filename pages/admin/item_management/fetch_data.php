<?php
include '../../../includes/db_connect.php';

// Fetch categories from the database
try {
    $stmt = $conn->query("SELECT * FROM categories ORDER BY id ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return categories as JSON
    echo json_encode($categories);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>