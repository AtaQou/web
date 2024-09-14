<?php
// Include the database connection
include '../../includes/db_connect.php';

// Start the session
session_start();

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Check if the username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->fetch()) {
        echo "Username already exists.";
        exit;
    }

    // Prepare and execute SQL query to insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role, name, phone, latitude, longitude) 
                            VALUES (?, ?, 'citizen', ?, ?, ?, ?)");
    
    $success = $stmt->execute([$username, $password, $name, $phone, $latitude, $longitude]);

    if ($success) {
        // Registration successful, redirect to login page
        header("Location: ../login/login.html");
        exit;
    } else {
        echo "Registration failed. Please try again.";
    }
}

$conn = null; // Close the database connection
?>
