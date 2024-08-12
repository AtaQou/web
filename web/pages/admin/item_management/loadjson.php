<?php
session_start();
include '../../../includes/db_connect.php';
include '../../../includes/admin_access.php';

if (!isset($_SESSION['decoded_data'])) {
    die('Error: No data to display.');
}

$data = $_SESSION['decoded_data'];

// Fetch existing categories from the database
$existing_categories_stmt = $conn->query("SELECT id FROM categories");
$existing_categories = $existing_categories_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Categories and Items</title>
    <style>
        .non-selectable {
            color: lightgray;
        }
    </style>
</head>
<body>
    <h1>Select Categories and Items to Insert into Database</h1>

    <?php
    // Display session message if exists
    if (isset($_SESSION['message'])) {
        echo '<p>' . htmlspecialchars($_SESSION['message']) . '</p>';
        unset($_SESSION['message']); // Clear the message after displaying it
    }
    ?>

    <form action="managecategory.php" method="post">
        <h2>Categories</h2>
        <?php if (isset($data['categories']) && is_array($data['categories'])): ?>
            <?php foreach ($data['categories'] as $category): ?>
                <input type="checkbox" name="selected_categories[]" value="<?= $category['id'] ?>"
                    <?= in_array($category['id'], $existing_categories) ? 'checked disabled' : '' ?>>
                <?= htmlspecialchars($category['category_name']) ?> (ID: <?= $category['id'] ?>)
                <?= in_array($category['id'], $existing_categories) ? '(Already in DB)' : '' ?><br>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No categories available.</p>
        <?php endif; ?>

        <h2>Items</h2>
        <?php if (isset($data['items']) && is_array($data['items'])): ?>
            <?php 
            // Separate selectable and non-selectable items
            $selectable_items = [];
            $non_selectable_items = [];

            foreach ($data['items'] as $item) {
                if (in_array($item['category'], $existing_categories)) {
                    $selectable_items[] = $item;
                } else {
                    $non_selectable_items[] = $item;
                }
            }

            // Display selectable items first
            foreach ($selectable_items as $item): ?>
                <input type="checkbox" name="selected_items[]" value="<?= $item['id'] ?>">
                <?= htmlspecialchars($item['name']) ?> (Category ID: <?= $item['category'] ?>): <?= htmlspecialchars($item['description']) ?><br>
                Quantity: <input type="number" name="quantity_<?= $item['id'] ?>" min="1" value="1"><br>
            <?php endforeach; ?>

            <!-- Display non-selectable items -->
            <?php foreach ($non_selectable_items as $item): ?>
                <span class="non-selectable">
                    <?= htmlspecialchars($item['name']) ?> (Category ID: <?= $item['category'] ?>): <?= htmlspecialchars($item['description']) ?>
                    <em>(Category not available)</em>
                </span><br>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No items available.</p>
        <?php endif; ?>

        <input type="submit" value="Insert Selected Data">
    </form>

    <form action="manage.html" method="get">
        <input type="submit" value="Back to Manage Page">
    </form>
</body>
</html>
