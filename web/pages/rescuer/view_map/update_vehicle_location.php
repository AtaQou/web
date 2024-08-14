<?php
include '../../../includes/db_connect.php';
include '../../../includes/rescuer_access.php';

$user_id = $_SESSION['user_id'];

// Retrieve the rescuer's vehicle
$vehicle_query = "SELECT * FROM vehicles WHERE rescuer_id = :rescuer_id LIMIT 1";
$vehicle_stmt = $conn->prepare($vehicle_query);
$vehicle_stmt->execute(['rescuer_id' => $user_id]);
$vehicle = $vehicle_stmt->fetch(PDO::FETCH_ASSOC);

if ($vehicle) {
    $input = json_decode(file_get_contents('php://input'), true);
    $latitude = $input['latitude'];
    $longitude = $input['longitude'];

    // Update vehicle's location
    $update_query = "UPDATE vehicles SET latitude = :latitude, longitude = :longitude WHERE id = :id";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->execute([
        'latitude' => $latitude,
        'longitude' => $longitude,
        'id' => $vehicle['id']
    ]);

    echo "Vehicle location updated.";
} else {
    echo "No vehicle found.";
}
?>
