<?php
// Include your database connection code here, e.g., connect.php
include '../../settings/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the task ID and "done" status from the AJAX request
    $taskID = $_POST['taskID'];
    $isDone = $_POST['isDone'];

    // Prepare and execute an SQL query to update the "done" status in your database
    $sql = "UPDATE tbltaskadmin SET done = :isDone WHERE taskID = :taskID";

    try {
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':taskID', $taskID, PDO::PARAM_INT);
        $stmt->bindParam(':isDone', $isDone, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Update successful
            echo json_encode(array('success' => true));
        } else {
            // Update failed
            echo json_encode(array('error' => 'Failed to update task status.'));
        }
    } catch (PDOException $e) {
        // Handle database errors
        echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
    }
} else {
    // Handle invalid requests
    echo json_encode(array('error' => 'Invalid request.'));
}
?>
