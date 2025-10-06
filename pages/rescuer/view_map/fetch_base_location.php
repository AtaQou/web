<?php
include '../../../includes/db_connect.php';

// Query to get base location
$query = "SELECT latitude, longitude FROM base_location LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->execute();
$base_location = $stmt->fetch(PDO::FETCH_ASSOC);

if ($base_location) {
    echo json_encode($base_location);
} else {
    echo json_encode(['latitude' => null, 'longitude' => null]);
}
?>
