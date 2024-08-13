<?php
session_start();
include '../../../includes/db_connect.php';
include '../../../includes/admin_access.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_category'])) {
        $category_id = $_POST['category_id'];
        try {
            $stmt = $conn->prepare("DELETE FROM categories WHERE id = :category_id");
            $stmt->bindParam(':category_id', $category_id);
            $stmt->execute();
            $_SESSION['message'] = 'Category deleted successfully.';
            header('Location: viewdb.php');
            exit;
        } catch (PDOException $e) {
            echo 'Error deleting category: ' . $e->getMessage();
        }
    }

    if (isset($_POST['delete_item'])) {
        $item_id = $_POST['item_id'];
        try {
            $stmt = $conn->prepare("DELETE FROM inventory WHERE id = :item_id");
            $stmt->bindParam(':item_id', $item_id);
            $stmt->execute();
            $_SESSION['message'] = 'Item deleted successfully.';
            header('Location: viewdb.php');
            exit;
        } catch (PDOException $e) {
            echo 'Error deleting item: ' . $e->getMessage();
        }
    }

    if (isset($_POST['update_quantity'])) {
        $item_id = $_POST['item_id'];
        $new_quantity = $_POST['new_quantity'];
        try {
            $stmt = $conn->prepare("UPDATE inventory SET quantity = :quantity WHERE id = :item_id");
            $stmt->bindParam(':quantity', $new_quantity);
            $stmt->bindParam(':item_id', $item_id);
            $stmt->execute();
            $_SESSION['message'] = 'Quantity updated successfully.';
            header('Location: viewdb.php');
            exit;
        } catch (PDOException $e) {
            echo 'Error updating quantity: ' . $e->getMessage();
        }
    }
}

$selected_categories = isset($_POST['categories']) ? $_POST['categories'] : [];

// Fetch categories from the database
try {
    $stmt = $conn->query("SELECT * FROM categories ORDER BY id ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Database error: ' . $e->getMessage();
    exit;
}

// Fetch items based on selected categories
try {
    $category_filter = '';
    if (!empty($selected_categories)) {
        $category_ids = implode(',', array_map('intval', $selected_categories));
        $category_filter = "WHERE category_id IN ($category_ids)";
    }
    
    $stmt = $conn->query("SELECT * FROM inventory $category_filter ORDER BY category_id ASC, id ASC");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Database error: ' . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View and Manage Database</title>
    <style>
        .back-button {
            margin-bottom: 20px;
        }
        .filter-form {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Warehouse Inventory Status</h1>

    <button class="back-button" onclick="window.location.href='manage.html'">Back to Manage Page</button>

    <h2>Filter Items by Category</h2>
    <form action="viewdb.php" method="post" class="filter-form">
        <fieldset>
            <legend>Select Categories:</legend>
            <?php foreach ($categories as $category): ?>
                <label>
                    <input type="checkbox" name="categories[]" value="<?= $category['id'] ?>" 
                        <?= in_array($category['id'], $selected_categories) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($category['name']) ?>
                </label><br>
            <?php endforeach; ?>
        </fieldset>
        <input type="submit" value="Apply Filters">
    </form>

    <h2>Categories</h2>
    <?php if (count($categories) > 0): ?>
        <ul>
            <?php foreach ($categories as $category): ?>
                <li>
                    <?= htmlspecialchars($category['name']) ?>: <?= htmlspecialchars($category['description']) ?>
                    <form action="viewdb.php" method="post" style="display:inline;">
                        <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                        <input type="submit" name="delete_category" value="Delete">
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No categories in the database.</p>
    <?php endif; ?>

    <h2>Items</h2>
    <?php if (count($items) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category ID</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= $item['category_id'] ?></td>
                        <td><?= htmlspecialchars($item['description']) ?></td>
                        <td>
                            <form action="viewdb.php" method="post" style="display:inline;">
                                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                <input type="number" name="new_quantity" min="0" value="<?= $item['quantity'] ?>">
                                <input type="submit" name="update_quantity" value="Update Quantity">
                            </form>
                            <?= $item['quantity'] ?>
                        </td>
                        <td>
                            <form action="viewdb.php" method="post" style="display:inline;">
                                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                <input type="submit" name="delete_item" value="Delete">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No items in the database.</p>
    <?php endif; ?>
</body>
</html>

