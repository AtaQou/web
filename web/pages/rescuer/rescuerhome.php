<?php
// Ελέγχουμε αν ο χρήστης έχει δικαίωμα πρόσβασης
include '../../includes/rescuer_access.php';
include '../../includes/db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rescuer Home</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
    <nav>
        <ul>
            <li><a href="rescuer_load_management/manage_cargo.html">Manage Cargo</a></li>
                <!-- Add other admin pages here -->
        </ul>
    </nav>
    <!-- Logout Button -->
    <form action="../logout.php" method="post">
        <input type="submit" value="Logout">
    </form>

    <!-- Add more content here for the rescuer's home page -->

</body>
</html>
