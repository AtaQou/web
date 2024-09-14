<?php
include '../../includes/citizen_access.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen Home</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
    <h2>Welcome, Citizen!</h2>
    
    <!-- Σύνδεσμος για τη δημιουργία αιτήματος -->
    <li><a href="requests/make_request.html">Make a New Request</a></li>
    <li><a href="offers/offers.html">See Announcements And Create An Offer</a>


    <!-- Logout Button -->
    <form action="../logout.php" method="post">
        <input type="submit" value="Logout">
    </form>
</body>
</html>
