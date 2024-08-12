<?php
include '../../../includes/db_connect.php';
include '../../../includes/admin_access.php';

header('Content-Type: application/json');

$response = [];

try {
    // Fetch vehicles
    $stmt = $conn->query("SELECT username, latitude, longitude, load, status FROM vehicles");
    $response['vehicles'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch requests
    $stmt = $conn->query("SELECT name, phone, latitude, longitude, date, item, quantity FROM requests");
    $response['requests'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch offers
    $stmt = $conn->query("SELECT name, phone, latitude, longitude, date, item, quantity FROM offers");
    $response['offers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
 