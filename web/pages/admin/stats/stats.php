<?php
session_start();
include '../../../includes/db_connect.php';
include '../../../includes/admin_access.php';

$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d');

try {
    // Fetch counts of requests and offers based on the selected period
    $stmt = $conn->prepare("
        SELECT 
            SUM(CASE WHEN type = 'request' AND status = 'pending' THEN 1 ELSE 0 END) AS new_requests,
            SUM(CASE WHEN type = 'offer' AND status = 'pending' THEN 1 ELSE 0 END) AS new_offers,
            SUM(CASE WHEN type = 'request' AND status = 'completed' THEN 1 ELSE 0 END) AS completed_requests,
            SUM(CASE WHEN type = 'offer' AND status = 'completed' THEN 1 ELSE 0 END) AS completed_offers
        FROM (
            SELECT 'request' AS type, status, request_date AS date FROM requests
            UNION ALL
            SELECT 'offer' AS type, status, offer_date AS date FROM offers
        ) AS combined
        WHERE date BETWEEN :start_date AND :end_date
    ");
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Database error: ' . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .container {
            width: 80%;
            margin: auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .back-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Statistics</h1>

        <form action="stats.php" method="post">
            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" required>
            </div>
            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" required>
            </div>
            <button type="submit">Generate Report</button>
        </form>

        <h2>Statistics Graph</h2>
        <canvas id="statsChart"></canvas>

        <button class="back-button" onclick="window.location.href='../adminhome.php'">Back to Admin Home</button>

        <script>
            const ctx = document.getElementById('statsChart').getContext('2d');
            const statsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['New Requests', 'New Offers', 'Completed Requests', 'Completed Offers'],
                    datasets: [{
                        label: 'Count',
                        data: [
                            <?= $data['new_requests'] ?? 0 ?>,
                            <?= $data['new_offers'] ?? 0 ?>,
                            <?= $data['completed_requests'] ?? 0 ?>,
                            <?= $data['completed_offers'] ?? 0 ?>
                        ],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255, 99, 132, 0.2)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    </div>
</body>
</html>
