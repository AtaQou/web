<?php

include '../../../includes/db_connect.php';
include '../../../includes/admin_access.php';

$response = ['status' => 'error', 'message' => 'Error loading data'];

if (isset($_SESSION['decoded_data'])) {
    $data = $_SESSION['decoded_data'];

    try {
        // Fetch existing categories from the database
        $existing_categories_stmt = $conn->query("SELECT id FROM categories");
        $existing_categories = $existing_categories_stmt->fetchAll(PDO::FETCH_COLUMN);

        $categories = [];
        $items = [];

        // Prepare categories data
        if (isset($data['categories']) && is_array($data['categories'])) {
            foreach ($data['categories'] as $category) {
                $categories[] = [
                    'id' => htmlspecialchars($category['id']),
                    'name' => htmlspecialchars($category['category_name']),
                    'selected' => in_array($category['id'], $existing_categories)
                ];
            }
        }

        // Get selected categories from query string
        $selected_categories = $_GET['categories'] ?? [];
        if (!is_array($selected_categories)) {
            $selected_categories = [$selected_categories];
        }

        // Prepare items data based on selected categories
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                if (empty($selected_categories) || in_array($item['category'], $selected_categories)) {
                    $description = '';
                    if (isset($item['details']) && is_array($item['details'])) {
                        foreach ($item['details'] as $detail) {
                            if (isset($detail['detail_name']) && isset($detail['detail_value'])) {
                                $description .= htmlspecialchars($detail['detail_name']) . ': ' . htmlspecialchars($detail['detail_value']) . ', ';
                            }
                        }
                        $description = rtrim($description, ', ');
                    }

                    $items[] = [
                        'id' => htmlspecialchars($item['id']),
                        'name' => htmlspecialchars($item['name']),
                        'category' => htmlspecialchars($item['category']),
                        'description' => $description ?: 'No description available'
                    ];
                }
            }
        }

        $response['status'] = 'success';
        $response['categories'] = $categories;
        $response['items'] = $items;
    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>