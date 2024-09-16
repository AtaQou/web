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
    $vehicle_id = $vehicle['id'];

    // Check the number of active tasks for the rescuer
    $task_count_query = "
        SELECT COUNT(*) AS active_tasks 
        FROM (
            SELECT id FROM requests WHERE status = 'active' AND vehicle_id = :vehicle_id
            UNION ALL
            SELECT id FROM offers WHERE status = 'active' AND vehicle_id = :vehicle_id
        ) AS active_tasks";
    $task_count_stmt = $conn->prepare($task_count_query);
    $task_count_stmt->execute(['vehicle_id' => $vehicle_id]);
    $active_tasks = $task_count_stmt->fetch(PDO::FETCH_ASSOC)['active_tasks'];

    if ($active_tasks >= 4) {
        echo "Error: You already have 4 active tasks. You cannot take more.";
        exit;
    }

    // Assign the task
    if ($task_type === 'request') {
        $assign_query = "UPDATE requests SET status = 'active', vehicle_id = :vehicle_id WHERE id = :task_id AND status = 'pending'";
    } else if ($task_type === 'offer') {
        $assign_query = "UPDATE offers SET status = 'active', vehicle_id = :vehicle_id WHERE id = :task_id AND status = 'pending'";
    }

    $assign_stmt = $conn->prepare($assign_query);
    $assign_stmt->execute([
        'vehicle_id' => $vehicle_id,
        'task_id' => $task_id
    ]);

    // Update vehicle status to busy
    $update_vehicle_status_query = "UPDATE vehicles SET status = 'busy' WHERE id = :vehicle_id";
    $update_vehicle_status_stmt = $conn->prepare($update_vehicle_status_query);
    $update_vehicle_status_stmt->execute(['vehicle_id' => $vehicle_id]);

    echo "Task assigned and vehicle status updated to busy.";
} else {
    echo "No vehicle found.";
}
?>
