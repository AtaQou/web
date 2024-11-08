<?php
include '../../../includes/db_connect.php';
include '../../../includes/admin_access.php';

// Λήψη του vehicle_id από το αίτημα
$vehicle_id = isset($_GET['vehicle_id']) ? (int)$_GET['vehicle_id'] : 0;

// Έξοδος για αποσφαλμάτωση
var_dump($_GET); // Εμφάνιση του πλήρους πίνακα $_GET για να δείτε όλες τις παραμέτρους

// Ελέγξτε αν το vehicle_id είναι έγκυρο
if ($vehicle_id <= 0) {
    echo json_encode(['error' => 'Invalid vehicle ID']);
    exit;
}

// Έξοδος για αποσφαλμάτωση
var_dump($_GET['vehicle_id']); // Εμφάνιση του vehicle_id

// Ελέγξτε αν το vehicle_id είναι έγκυρο
if ($vehicle_id <= 0) {
    echo json_encode(['error' => 'Invalid vehicle ID']);
    exit;
}

// Fetch only active requests for the specified vehicle_id
$requests_query = "
    SELECT 
        r.citizen_username AS username, 
        i.name AS item_name, 
        r.quantity 
    FROM requests r
    LEFT JOIN inventory i ON r.item_id = i.id
    WHERE r.status = 'active' AND r.vehicle_id = :vehicle_id";
$requests_stmt = $conn->prepare($requests_query);
$requests_stmt->execute(['vehicle_id' => $vehicle_id]);
$requests = $requests_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch only active offers for the specified vehicle_id
$offers_query = "
    SELECT 
        o.citizen_username AS username, 
        i.name AS item_name, 
        o.quantity 
    FROM offers o
    LEFT JOIN inventory i ON o.item_id = i.id
    WHERE o.status = 'active' AND o.vehicle_id = :vehicle_id";
$offers_stmt = $conn->prepare($offers_query);
$offers_stmt->execute(['vehicle_id' => $vehicle_id]);
$offers = $offers_stmt->fetchAll(PDO::FETCH_ASSOC);

// Επιστροφή μόνο των απαραίτητων δεδομένων
$data = [
    'requests' => $requests,
    'offers' => $offers,
];

header('Content-Type: application/json');
echo json_encode($data);
?>
