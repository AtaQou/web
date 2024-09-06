<?php
include '../../../includes/db_connect.php';
include '../../../includes/rescuer_access.php';

$input = json_decode(file_get_contents('php://input'), true);
$task_id = $input['task_id'];
$task_type = $input['task_type'];

$user_id = $_SESSION['user_id'];

// Get rescuer's vehicle
$vehicle_query = "SELECT id, latitude AS vehicle_lat, longitude AS vehicle_lng FROM vehicles WHERE rescuer_id = :rescuer_id LIMIT 1";
$vehicle_stmt = $conn->prepare($vehicle_query);
$vehicle_stmt->execute(['rescuer_id' => $user_id]);
$vehicle = $vehicle_stmt->fetch(PDO::FETCH_ASSOC);

if ($vehicle) {
    // Get the item and quantity from the task
    if ($task_type === 'request') {
        $task_query = "SELECT item_id, quantity, latitude, longitude FROM requests WHERE id = :task_id AND vehicle_id = :vehicle_id";
    } else if ($task_type === 'offer') {
        $task_query = "SELECT item_id, quantity, latitude, longitude FROM offers WHERE id = :task_id AND vehicle_id = :vehicle_id";
    }

    $task_stmt = $conn->prepare($task_query);
    $task_stmt->execute([
        'task_id' => $task_id,
        'vehicle_id' => $vehicle['id']
    ]);
    $task = $task_stmt->fetch(PDO::FETCH_ASSOC);

    if ($task) {
        $item_id = $task['item_id'];
        $quantity = $task['quantity'];
        $task_lat = $task['latitude'];
        $task_lng = $task['longitude'];
        $vehicle_lat = $vehicle['vehicle_lat'];
        $vehicle_lng = $vehicle['vehicle_lng'];

        // Function to calculate distance between two lat/lng points
        function calculateDistance($lat1, $lng1, $lat2, $lng2) {
            $earth_radius = 6371000; // Earth radius in meters

            $lat1_rad = deg2rad($lat1);
            $lng1_rad = deg2rad($lng1);
            $lat2_rad = deg2rad($lat2);
            $lng2_rad = deg2rad($lng2);

            $dlat = $lat2_rad - $lat1_rad;
            $dlng = $lng2_rad - $lng1_rad;

            $a = sin($dlat / 2) ** 2 + cos($lat1_rad) * cos($lat2_rad) * sin($dlng / 2) ** 2;
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

            return $earth_radius * $c;
        }

        // Check if the vehicle is within 50 meters of the task
        if (calculateDistance($vehicle_lat, $vehicle_lng, $task_lat, $task_lng) <= 50) {
            // Proceed with the task completion logic
            if ($task_type === 'request') {
                // Check current cargo quantity
                $cargo_query = "SELECT quantity FROM vehicle_loads WHERE vehicle_id = :vehicle_id AND item_id = :item_id";
                $cargo_stmt = $conn->prepare($cargo_query);
                $cargo_stmt->execute([
                    'vehicle_id' => $vehicle['id'],
                    'item_id' => $item_id
                ]);
                $cargo = $cargo_stmt->fetch(PDO::FETCH_ASSOC);

                if ($cargo && $cargo['quantity'] >= $quantity) {
                    // Update task status to 'completed'
                    $update_query = "UPDATE requests SET status = 'completed' WHERE id = :task_id AND vehicle_id = :vehicle_id";
                    $update_stmt = $conn->prepare($update_query);
                    $update_stmt->execute([
                        'task_id' => $task_id,
                        'vehicle_id' => $vehicle['id']
                    ]);

                    // Update vehicle load
                    $load_update_query = "UPDATE vehicle_loads SET quantity = quantity - :quantity WHERE vehicle_id = :vehicle_id AND item_id = :item_id";
                    $load_update_stmt = $conn->prepare($load_update_query);
                    $load_update_stmt->execute([
                        'quantity' => $quantity,
                        'vehicle_id' => $vehicle['id'],
                        'item_id' => $item_id
                    ]);

                    echo "Task completed.";
                } else {
                    echo "Item/quantity of item not available.";
                }
            } else if ($task_type === 'offer') {
                // Check if the item already exists in the vehicle loads
                $cargo_query = "SELECT quantity FROM vehicle_loads WHERE vehicle_id = :vehicle_id AND item_id = :item_id";
                $cargo_stmt = $conn->prepare($cargo_query);
                $cargo_stmt->execute([
                    'vehicle_id' => $vehicle['id'],
                    'item_id' => $item_id
                ]);
                $cargo = $cargo_stmt->fetch(PDO::FETCH_ASSOC);

                if ($cargo) {
                    // If the item exists, update the quantity
                    $load_update_query = "UPDATE vehicle_loads SET quantity = quantity + :quantity WHERE vehicle_id = :vehicle_id AND item_id = :item_id";
                    $load_update_stmt = $conn->prepare($load_update_query);
                    $load_update_stmt->execute([
                        'quantity' => $quantity,
                        'vehicle_id' => $vehicle['id'],
                        'item_id' => $item_id
                    ]);
                } else {
                    // If the item does not exist, insert it with the provided quantity
                    $load_insert_query = "INSERT INTO vehicle_loads (vehicle_id, item_id, quantity) VALUES (:vehicle_id, :item_id, :quantity)";
                    $load_insert_stmt = $conn->prepare($load_insert_query);
                    $load_insert_stmt->execute([
                        'vehicle_id' => $vehicle['id'],
                        'item_id' => $item_id,
                        'quantity' => $quantity
                    ]);
                }

                // Update the offer status to 'completed'
                $update_query = "UPDATE offers SET status = 'completed' WHERE id = :task_id AND vehicle_id = :vehicle_id";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->execute([
                    'task_id' => $task_id,
                    'vehicle_id' => $vehicle['id']
                ]);

                echo "Task completed.";
            }
        } else {
            echo "Vehicle is too far from the task location.";
        }
    } else {
        echo "Task not found.";
    }
} else {
    echo "No vehicle found.";
}
?>
