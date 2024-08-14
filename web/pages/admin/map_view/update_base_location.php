<?php
include '../../../includes/db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['latitude']) && isset($data['longitude'])) {
    $latitude = $data['latitude'];
    $longitude = $data['longitude'];

    try {
        // Check if a base location already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM base_location");
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            // Update the existing base location
            $stmt = $conn->prepare("UPDATE base_location SET latitude = :latitude, longitude = :longitude");
        } else {
            // Insert a new base location if none exists
            $stmt = $conn->prepare("INSERT INTO base_location (latitude, longitude) VALUES (:latitude, :longitude)");
        }

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
