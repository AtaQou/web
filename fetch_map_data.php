<?php
include '../../../includes/db_connect.php';
include '../../../includes/admin_access.php';

// Fetch vehicles with their associated requests, offers, and cargo
$vehicles_query = "
    SELECT 
        v.id AS vehicle_id, 
        v.username, 
        v.status, 
        v.latitude, 
        v.longitude, 
        u.username AS rescuer_username,   -- Changed to include rescuer's username
        u.phone AS rescuer_phone,
        GROUP_CONCAT(DISTINCT CONCAT(i.name, ' ', vl.quantity) SEPARATOR ', ') AS cargo,
        GROUP_CONCAT(DISTINCT subquery.task_description ORDER BY subquery.task_type, subquery.task_date SEPARATOR '<br>') AS tasks
    FROM vehicles v
    LEFT JOIN users u ON v.rescuer_id = u.id   -- Use rescuer_id instead of username
    LEFT JOIN vehicle_loads vl ON v.id = vl.vehicle_id
    LEFT JOIN inventory i ON vl.item_id = i.id
    LEFT JOIN (
        SELECT 
            r.vehicle_id,
            'Request' AS task_type,
            CONCAT('Request: ', r.citizen_username, ' - ', r.item_id, ' (', r.quantity, ') on ', r.request_date) AS task_description,
            r.request_date AS task_date
        FROM requests r
        WHERE r.status IN ('pending', 'active')
        
        UNION ALL
        
        SELECT 
            o.vehicle_id,
            'Offer' AS task_type,
            CONCAT('Offer: ', o.citizen_username, ' - ', o.item_id, ' (', o.quantity, ') on ', o.offer_date) AS task_description,
            o.offer_date AS task_date
        FROM offers o
        WHERE o.status IN ('pending', 'active')
    ) AS subquery ON v.id = subquery.vehicle_id
    GROUP BY v.id
";
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
