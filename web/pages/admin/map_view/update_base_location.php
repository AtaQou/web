<?php
include '../../../includes/db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['latitude']) && isset($data['longitude'])) {
    $latitude = $data['latitude'];
    $longitude = $data['longitude'];

    try {
        // Insert new base location into the database
        $stmt = $conn->prepare("INSERT INTO base_location (latitude, longitude) VALUES (:latitude, :longitude)");
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':longitude', $longitude);
        $stmt->execute();

        echo 'Base location updated successfully.';
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
} else {
    echo 'Invalid data.';
}
?>
