<?php
include '../../../includes/db_connect.php';
include '../../../includes/rescuer_access.php';

$user_id = $_SESSION['user_id'];

// Fetch vehicle associated with the rescuer
$vehicle_query = "
    SELECT id FROM vehicles
    WHERE rescuer_id = :rescuer_id
    LIMIT 1";
$vehicle_stmt = $conn->prepare($vehicle_query);
$vehicle_stmt->execute(['rescuer_id' => $user_id]);
$vehicle = $vehicle_stmt->fetch(PDO::FETCH_ASSOC);

$tasks = [];
if ($vehicle) {
    $tasks_query = "
        SELECT id AS request_id, 'request' AS type, citizen_name, citizen_phone, request_date, item_id, quantity, status AS request_status 
        FROM requests 
        WHERE vehicle_id = :vehicle_id AND status = 'active'
        UNION ALL
        SELECT id AS offer_id, 'offer' AS type, NULL AS citizen_name, NULL AS citizen_phone, offer_date AS request_date, item_id AS item_id, quantity AS quantity, status AS offer_status
        FROM offers 
        WHERE vehicle_id = :vehicle_id AND status = 'active'";
    $tasks_stmt = $conn->prepare($tasks_query);
    $tasks_stmt->execute(['vehicle_id' => $vehicle['id']]);
    $tasks = $tasks_stmt->fetchAll(PDO::FETCH_ASSOC);
}

header('Content-Type: application/json');
echo json_encode($tasks);
?>
