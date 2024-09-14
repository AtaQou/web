<?php
include '../../../includes/db_connect.php';

$category_id = $_GET['category_id'];

$stmt = $conn->prepare("SELECT id, name FROM inventory WHERE category_id = ?");
$stmt->execute([$category_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($items);
?>
