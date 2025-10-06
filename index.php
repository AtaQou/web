<?php
// index.php

// Include the database connection
include '/web/includes/db_connect.php';

// Example query to fetch categories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disaster Relief</title>
    <link rel="stylesheet" href="assets/style.css"> <!-- Example CSS link -->
</head>
<body>
    <h1>Welcome to Disaster Relief</h1>
    <h2>Categories</h2>
    <ul>
        <?php foreach ($categories as $category): ?>
            <li><?php echo htmlspecialchars($category['name']); ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
