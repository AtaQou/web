<?php
// Start session at the top
session_start();

// Database credentials
$host = '127.0.0.1'; // or 'localhost'
$dbname = 'web';
$username = 'root';
$password = 'root'; // The password you set in the Docker command

// Get the decoded data from the session
if (!isset($_SESSION['decoded_data'])) {
    die('Error: No data to insert.');
}
$data = $_SESSION['decoded_data'];

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;port=3310;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    foreach ($data['items'] as $item) {
        $name = $item['name'];
        $category_id = $item['category'];
        $details = json_encode($item['details']);

        $stmt = $pdo->prepare("INSERT INTO inventory (name, category_id, description) VALUES (:name, :category_id, :details)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':details', $details);
        $stmt->execute();
    }

    echo 'Data inserted successfully.';
} catch (PDOException $e) {
    echo 'Database error: ' . $e->getMessage();
}
?>
