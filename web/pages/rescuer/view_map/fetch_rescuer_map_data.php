<?php
include '../../../includes/db_connect.php';
include '../../../includes/rescuer_access.php';

$user_id = $_SESSION['user_id'];

// Fetch vehicle associated with the rescuer
$vehicle_query = "
    SELECT * FROM vehicles
    WHERE rescuer_id = :rescuer_id
    LIMIT 1";
$vehicle_stmt = $conn->prepare($vehicle_query);
$vehicle_stmt->execute(['rescuer_id' => $user_id]);
$vehicle = $vehicle_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch pending or active requests
$requests_query = "SELECT * FROM requests WHERE status IN ('pending', 'active')";
$requests_stmt = $conn->query($requests_query);
$requests = $requests_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch pending or active offers
$offers_query = "SELECT * FROM offers WHERE status IN ('pending', 'active')";
$offers_stmt = $conn->query($offers_query);
$offers = $offers_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch tasks assigned to this vehicle
$tasks = [];
if ($vehicle) {
    $tasks_query = "
        SELECT id, 'request' AS type FROM requests WHERE vehicle_id = :vehicle_id
        UNION ALL
        SELECT id, 'offer' AS type FROM offers WHERE vehicle_id = :vehicle_id";
    $tasks_stmt = $conn->prepare($tasks_query);
    $tasks_stmt->execute(['vehicle_id' => $vehicle['id']]);
    $tasks = $tasks_stmt->fetchAll(PDO::FETCH_ASSOC);
}

$data = [
    'vehicle' => $vehicle,
    'requests' => $requests,
    'offers' => $offers,
    'tasks' => $tasks
];

header('Content-Type: application/json');
echo json_encode($data);
?>
