<?php
session_start();
include '../../../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Παράμετροι φόρμας
    $category_id = $_POST['category'];
    $item_id = $_POST['item_id'];
    $number_of_people = $_POST['number_of_people'];

    // Λήψη user_id από το session
    $user_id = $_SESSION['user_id'];

    try {
        // Ανάκτηση του χρήστη για τις συντεταγμένες
        $stmt = $conn->prepare("SELECT username, latitude, longitude FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $username = $user['username'];
            $latitude = $user['latitude'];
            $longitude = $user['longitude'];

            // Δημιουργία της αίτησης
            $stmt = $conn->prepare("INSERT INTO requests (citizen_username, item_id, quantity, status, latitude, longitude) VALUES (?, ?, ?, 'pending', ?, ?)");
            $stmt->execute([$username, $item_id, $number_of_people, $latitude, $longitude]);

            echo "Request submitted successfully!";
        } else {
            echo "User not found.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

$conn = null;
?>
