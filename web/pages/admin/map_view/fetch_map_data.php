<?php
include '../../../includes/db_connect.php';

// Fetch vehicles
$vehicles_query = "SELECT * FROM vehicles";
$vehicles_result = $conn->query($vehicles_query);
$vehicles = [];
while ($row = $vehicles_result->fetch(PDO::FETCH_ASSOC)) {
    $vehicles[] = $row;
}

// Fetch requests
$requests_query = "SELECT * FROM requests";
$requests_result = $conn->query($requests_query);
$requests = [];
while ($row = $requests_result->fetch(PDO::FETCH_ASSOC)) {
    $requests[] = $row;
}

// Fetch offers
$offers_query = "SELECT * FROM offers";
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
