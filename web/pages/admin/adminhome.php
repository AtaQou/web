<?php
session_start();
include '../../includes/admin_access.php';
include '../../includes/db_connect.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'administrator') {
    header("Location: ../login.php"); // Redirect to login if not authorized
    exit;
}

// Fetch the admin's name for a personalized greeting
$username = $_SESSION['username'];
$sql = "SELECT name FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$username]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$conn = null; // Close the database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($admin['name']); ?>!</h1>
    <nav>
        <ul>
            <li><a href="map_view/map_view.html">View map</a></li>
            <li><a href="item_management/manage.html">Manage Categories</a></li>
            <li><a href="../dashboard.php">Dashboard</a></li>
            <li><a href="stats/stats.php">View Stats</a></li>
            <li><a href="create_rescuer/create_rescuer.php">Add A New Rescuer</a></li>
            <li><a href="create_announcement/create_announcement.html">Add New Announcement</a></li>
            <!-- Add other admin pages here -->
        </ul>
    </nav>
    <a href="../logout.php">Logout</a>
</body>
</html>
