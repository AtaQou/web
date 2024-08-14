<?php
include '../../../includes/db_connect.php';
include '../../../includes/admin_access.php';

// Fetch vehicles along with their assigned requests and offers
$vehicles_query = "
    SELECT vehicles.*, requests.id AS request_id, offers.id AS offer_id
    FROM vehicles
    LEFT JOIN requests ON vehicles.id = requests.vehicle_id
    LEFT JOIN offers ON vehicles.id = offers.vehicle_id";
$vehicles_result = $conn->query($vehicles_query);
$vehicles = [];
while ($row = $vehicles_result->fetch(PDO::FETCH_ASSOC)) {
    $vehicles[] = $row;
}

// Fetch only pending or active requests
$requests_query = "SELECT * FROM requests WHERE status IN ('pending', 'active')";
$requests_result = $conn->query($requests_query);
$requests = [];
while ($row = $requests_result->fetch(PDO::FETCH_ASSOC)) {
    $requests[] = $row;
}

// Fetch only pending or active offers
$offers_query = "SELECT * FROM offers WHERE status IN ('pending', 'active')";
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
