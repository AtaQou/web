<?php
// Include the database connection
include '../../includes/db_connect.php';

// Start the session
session_start();

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute SQL query
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Check if the password matches
        if ($password === $user['password_hash']) {
            // Successful login
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role']; // Save the user's role in the session
            $_SESSION['user_id'] = $user['id']; // Save user_id in the session
            
            // Redirect based on user role
            if ($user['role'] === 'administrator') {
                header("Location: ../admin/adminhome.php");
            } elseif ($user['role'] === 'rescuer') {
                header("Location: ../rescuer/rescuerhome.php");
            } else {
                echo "Invalid user role.";
                exit;
            }
            exit;
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "Username not found.";
    }
}

$conn = null; // Close the database connection
?>
