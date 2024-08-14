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
    if ($task_type === 'request') {
        $assign_query = "UPDATE requests SET status = 'active', vehicle_id = :vehicle_id WHERE id = :task_id AND status = 'pending'";
    } else if ($task_type === 'offer') {
        $assign_query = "UPDATE offers SET status = 'active', vehicle_id = :vehicle_id WHERE id = :task_id AND status = 'pending'";
    }
    
    $assign_stmt = $conn->prepare($assign_query);
    $assign_stmt->execute([
        'vehicle_id' => $vehicle['id'],
        'task_id' => $task_id
    ]);

    echo "Task assigned.";
} else {
    echo "No vehicle found.";
}
?>
