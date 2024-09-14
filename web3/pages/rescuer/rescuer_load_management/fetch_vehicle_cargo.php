<?php
include '../../../includes/db_connect.php';
session_start();

header('Content-Type: application/json');

// Get the user ID from session
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['error' => 'User ID not set in session.']);
    exit;
}

try {
    // Fetch the vehicle assigned to the current rescuer
    $stmt = $conn->prepare("
        SELECT v.id AS vehicle_id, v.username AS vehicle_name
        FROM vehicles v
        WHERE v.rescuer_id = :user_id
    ");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vehicle) {
        echo json_encode(['error' => 'No vehicle found for this rescuer.']);
        exit;
    }

    // Fetch the cargo of the vehicle
    $stmt = $conn->prepare("
        SELECT i.id, i.name, vl.quantity
        FROM vehicle_loads vl
        INNER JOIN inventory i ON vl.item_id = i.id
        WHERE vl.vehicle_id = :vehicle_id
    ");
    $stmt->bindParam(':vehicle_id', $vehicle['vehicle_id']);
    $stmt->execute();
    $cargo = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'user_id' => $user_id,
        'vehicle_name' => $vehicle['vehicle_name'],
        'items' => $cargo
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
