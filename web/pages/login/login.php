<?php
// Include the database connection
include '../../includes/db_connect.php';

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
            session_start();
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role']; // Save the user's role in the session
            echo "Login successful. Welcome, " . htmlspecialchars($user['name']) . "!";
            // Redirect to dashboard or another page
            header("Location: ../admin/adminhome.php");
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
