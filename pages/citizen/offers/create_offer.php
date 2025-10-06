<?php
include '../../../includes/db_connect.php';
session_start();

$citizen_username = $_SESSION['username'];

// Λήψη δεδομένων από τη φόρμα
$announcement_id = $_POST['announcement_id'];
$item_id = $_POST['item_id'];
$quantity = $_POST['quantity'];

try {
    // Βρίσκουμε τις συντεταγμένες του χρήστη από τον πίνακα users
    $stmt = $conn->prepare("SELECT latitude, longitude FROM users WHERE username = ?");
    $stmt->execute([$citizen_username]);
    $user_location = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user_location) {
        // Προσθήκη της προσφοράς στη βάση δεδομένων
        $stmt = $conn->prepare("
            INSERT INTO offers (citizen_username, announcement_id, item_id, quantity, latitude, longitude, offer_date) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $citizen_username, 
            $announcement_id, 
            $item_id, 
            $quantity, 
            $user_location['latitude'], 
            $user_location['longitude']
        ]);

        echo 'Offer created successfully';
    } else {
        echo 'User location not found';
    }

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
