<?php

include '../../../includes/db_connect.php';
include '../../../includes/admin_access.php';

// Get start and end dates, with default values
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d');

try {
    // Fetch counts of requests based on the selected period
    $stmt = $conn->prepare("
        SELECT 
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS new_requests,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_requests
        FROM requests
        WHERE request_date BETWEEN :start_date AND :end_date
    ");
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->execute();
    $requestsData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch counts of offers based on the selected period
    $stmt = $conn->prepare("
        SELECT 
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS new_offers,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_offers
        FROM offers
        WHERE offer_date BETWEEN :start_date AND :end_date
    ");
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->execute();
    $offersData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Merge request and offer data into one array
    $data = array_merge($requestsData, $offersData);

    // Ensure valid JSON response, even if empty
    header('Content-Type: application/json');
    echo json_encode($data);

} catch (PDOException $e) {
    // Always return valid JSON, even on error
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit;
}
?>