<?php
include '../../../includes/db_connect.php';
include '../../../includes/admin_access.php';

// Fetch vehicles with their associated requests, offers, cargo, and rescuer name
$vehicles_query = "
    SELECT 
        v.id AS vehicle_id, 
        v.username AS vehicle_name, 
        u.name AS rescuer_name,
        v.status, 
        v.latitude, 
        v.longitude, 
        GROUP_CONCAT(DISTINCT CONCAT(r.citizen_username, ' (Item: ', ir.name, ')') SEPARATOR ', ') AS requests,
        GROUP_CONCAT(DISTINCT CONCAT(o.citizen_username, ' (Item: ', io.name, ')') SEPARATOR ', ') AS offers,
        GROUP_CONCAT(DISTINCT CONCAT(i.name, ' ', vl.quantity) SEPARATOR ', ') AS cargo
    FROM vehicles v
    LEFT JOIN users u ON v.rescuer_id = u.id
    LEFT JOIN requests r ON v.id = r.vehicle_id AND r.status IN ('pending', 'active')
    LEFT JOIN offers o ON v.id = o.vehicle_id AND o.status IN ('pending', 'active')
    LEFT JOIN inventory ir ON r.item_id = ir.id
    LEFT JOIN inventory io ON o.item_id = io.id
    LEFT JOIN vehicle_loads vl ON v.id = vl.vehicle_id
    LEFT JOIN inventory i ON vl.item_id = i.id
    GROUP BY v.id";
$vehicles_result = $conn->query($vehicles_query);
$vehicles = [];
while ($row = $vehicles_result->fetch(PDO::FETCH_ASSOC)) {
    $vehicles[] = $row;
}

// Fetch only pending or active requests
$requests_query = "
    SELECT 
        r.*, 
        i.name AS item_name 
    FROM requests r
    LEFT JOIN inventory i ON r.item_id = i.id
    WHERE r.status IN ('pending', 'active')";
$requests_result = $conn->query($requests_query);
$requests = [];
while ($row = $requests_result->fetch(PDO::FETCH_ASSOC)) {
    $requests[] = $row;
}

// Fetch only pending or active offers
$offers_query = "
    SELECT 
        o.*, 
        i.name AS item_name 
    FROM offers o
    LEFT JOIN inventory i ON o.item_id = i.id
    WHERE o.status IN ('pending', 'active')";
$offers_result = $conn->query($offers_query);
$offers = [];
while ($row = $offers_result->fetch(PDO::FETCH_ASSOC)) {
    $offers[] = $row;
}

// Fetch base location
$base_query = "SELECT * FROM base_location ORDER BY id DESC LIMIT 1";
$base_result = $conn->query($base_query);
$base_location = $base_result->fetch(PDO::FETCH_ASSOC);

$data = [
    'vehicles' => $vehicles,
    'requests' => $requests,
    'offers' => $offers,
    'base_location' => $base_location
];

header('Content-Type: application/json');
echo json_encode($data);
?>
