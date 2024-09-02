<?php
include '../../../includes/db_connect.php';
session_start();

// Get the user ID from session
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'User ID not set in session.']);
    exit;
}

// Debugging: Print POST data to check if action and item_id are set
error_log("POST Data: " . print_r($_POST, true));

$action = $_POST['action'] ?? null;
$item_id = $_POST['item_id'] ?? null;
$quantity = $_POST['quantity'] ?? null;

if ($action) {
    try {
        // Fetch the rescuer's location
        $stmt = $conn->prepare("
            SELECT latitude, longitude
            FROM users
            WHERE id = :user_id
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['error' => 'User not found.']);
            exit;
        }

        $latitude = $user['latitude'];
        $longitude = $user['longitude'];

        // Fetch base location
        $stmt = $conn->prepare("
            SELECT latitude, longitude
            FROM base_location
            WHERE id = 1
        ");
        $stmt->execute();
        $base_location = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$base_location) {
            echo json_encode(['error' => 'Base location not found.']);
            exit;
        }

        $latitude_base = $base_location['latitude'];
        $longitude_base = $base_location['longitude'];

        // Calculate distance
        $distance = calculate_distance($latitude, $longitude, $latitude_base, $longitude_base);

        if ($distance > 100) { // 100 meters
            echo json_encode([
                'error' => 'You must be within 100 meters of the base to perform this action. Your current distance is ' . number_format($distance, 2) . ' meters.',
                'distance' => number_format($distance, 2) // Format distance to 2 decimal places
            ]);
            exit;
        }

        // Fetch the vehicle assigned to the current rescuer
        $stmt = $conn->prepare("
            SELECT v.id
            FROM vehicles v
            WHERE v.rescuer_id = :user_id
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$vehicle) {
            echo json_encode(['error' => 'No vehicle found for this rescuer.']);
            exit;
        }

        $vehicle_id = $vehicle['id'];

        if ($action === 'load' && $item_id && $quantity) {
            // Check if there is enough quantity in the inventory
            $stmt = $conn->prepare("
                SELECT quantity
                FROM inventory
                WHERE id = :item_id
            ");
            $stmt->bindParam(':item_id', $item_id);
            $stmt->execute();
            $inventory = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$inventory || $inventory['quantity'] < $quantity) {
                echo json_encode(['error' => 'Not enough items in inventory or invalid item.']);
                exit;
            }

            // Load item into vehicle and decrease the inventory quantity
            $stmt = $conn->prepare("
                INSERT INTO vehicle_loads (vehicle_id, item_id, quantity)
                VALUES (:vehicle_id, :item_id, :quantity)
                ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
            ");
            $stmt->bindParam(':vehicle_id', $vehicle_id);
            $stmt->bindParam(':item_id', $item_id);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->execute();

            // Decrease the inventory quantity
            $stmt = $conn->prepare("
                UPDATE inventory
                SET quantity = quantity - :quantity
                WHERE id = :item_id
            ");
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':item_id', $item_id);
            $stmt->execute();

            echo json_encode(['success' => 'Item loaded successfully.']);
        } elseif ($action === 'unload_all') {
            // Begin transaction
            $conn->beginTransaction();

            // Fetch all items currently loaded in the vehicle
            $stmt = $conn->prepare("
                SELECT item_id, quantity
                FROM vehicle_loads
                WHERE vehicle_id = :vehicle_id
            ");
            $stmt->bindParam(':vehicle_id', $vehicle_id);
            $stmt->execute();
            $loaded_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($loaded_items) {
                foreach ($loaded_items as $item) {
                    // Update the inventory to add back the unloaded items
                    $stmt = $conn->prepare("
                        UPDATE inventory
                        SET quantity = quantity + :quantity
                        WHERE id = :item_id
                    ");
                    $stmt->bindParam(':quantity', $item['quantity']);
                    $stmt->bindParam(':item_id', $item['item_id']);
                    $stmt->execute();
                }

                // Clear all items from the vehicle
                $stmt = $conn->prepare("
                    DELETE FROM vehicle_loads
                    WHERE vehicle_id = :vehicle_id
                ");
                $stmt->bindParam(':vehicle_id', $vehicle_id);
                $stmt->execute();

                // Commit the transaction
                $conn->commit();

                echo json_encode(['success' => 'All items unloaded successfully.']);
            } else {
                $conn->rollBack();
                echo json_encode(['error' => 'No items found in the vehicle.']);
            }
        } else {
            echo json_encode(['error' => 'Invalid action or missing data.']);
        }
    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack(); // Rollback transaction if any error occurs
        }
        error_log('Database error: ' . $e->getMessage()); // Log the database error
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Missing action or item data.']);
}

// Function to calculate distance between two coordinates (in meters)
function calculate_distance($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371000; // Earth radius in meters

    $dlat = deg2rad($lat2 - $lat1);
    $dlon = deg2rad($lon2 - $lon1);

    $a = sin($dlat / 2) * sin($dlat / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dlon / 2) * sin($dlon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earth_radius * $c;
}
?>
