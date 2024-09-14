<?php
include '../../../includes/db_connect.php';
include '../../../includes/rescuer_access.php';

$input = json_decode(file_get_contents('php://input'), true);
$task_id = $input['task_id'];
$task_type = $input['task_type'];

$user_id = $_SESSION['user_id'];

// Get rescuer's vehicle
$vehicle_query = "SELECT id FROM vehicles WHERE rescuer_id = :rescuer_id LIMIT 1";
$vehicle_stmt = $conn->prepare($vehicle_query);
$vehicle_stmt->execute(['rescuer_id' => $user_id]);
$vehicle = $vehicle_stmt->fetch(PDO::FETCH_ASSOC);

if ($vehicle) {
    // Update task status to 'pending' and remove vehicle assignment
    if ($task_type === 'request') {
        $update_query = "UPDATE requests SET status = 'pending', vehicle_id = NULL WHERE id = :task_id AND vehicle_id = :vehicle_id";
    } else if ($task_type === 'offer') {
        $update_query = "UPDATE offers SET status = 'pending', vehicle_id = NULL WHERE id = :task_id AND vehicle_id = :vehicle_id";
    }
    
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->execute([
        'task_id' => $task_id,
        'vehicle_id' => $vehicle['id']
    ]);

    echo "Task cancelled.";
} else {
    echo "No vehicle found.";
}
?>
