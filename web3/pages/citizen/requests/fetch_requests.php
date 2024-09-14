<?php
include '../../../includes/db_connect.php';
session_start();

$citizen_username = $_SESSION['username'];  // Παίρνουμε το username από το session

$stmt = $conn->prepare("SELECT requests.id, inventory.name AS item_name, requests.status, requests.quantity, requests.request_date FROM requests JOIN inventory ON requests.item_id = inventory.id WHERE requests.citizen_username = ? ORDER BY requests.request_date DESC");
$stmt->execute([$citizen_username]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($requests);
?>
