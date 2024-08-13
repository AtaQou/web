<?php
session_start();
include '../../../includes/db_connect.php';
include '../../../includes/admin_access.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $items = $_POST['items'] ?? [];

    // Simple validation
    if (empty($title) || empty($description)) {
        $error_message = 'Title and description are required.';
    } else {
        try {
            // Insert new announcement
            $stmt = $conn->prepare("
                INSERT INTO announcements (title, description)
                VALUES (:title, :description)
            ");
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->execute();
            
            $announcement_id = $conn->lastInsertId();

            // Insert related items
            if (!empty($items)) {
                $stmt = $conn->prepare("
                    INSERT INTO announcement_items (announcement_id, item_id)
                    VALUES (:announcement_id, :item_id)
                ");
                foreach ($items as $item_id) {
                    $stmt->bindParam(':announcement_id', $announcement_id);
                    $stmt->bindParam(':item_id', $item_id);
                    $stmt->execute();
                }
            }

            $_SESSION['message'] = 'Announcement created successfully.';
            header('Location: ../adminhome.php');  // Redirect to admin home
            exit;
        } catch (PDOException $e) {
            $error_message = 'Database error: ' . $e->getMessage();
        }
    }
}

// Fetch items for the dropdown
try {
    $stmt = $conn->query("SELECT id, name FROM inventory ORDER BY name ASC");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = 'Database error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Announcement</title>
    <style>
        .container {
            width: 80%;
            margin: auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .error-message {
            color: red;
        }
        .success-message {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Announcement</h1>

        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <?php if (!empty($_SESSION['message'])): ?>
            <p class="success-message"><?= htmlspecialchars($_SESSION['message']) ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <form action="create_announcement.php" method="post">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="items">Items:</label>
                <select id="items" name="items[]" multiple>
                    <?php foreach ($items as $item): ?>
                        <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit">Create Announcement</button>
        </form>

        <button class="back-button" onclick="window.location.href='../adminhome.php'">Back to Admin Home</button>
    </div>
</body>
</html>
