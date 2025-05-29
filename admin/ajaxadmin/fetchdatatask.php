<?php
session_start();

include '../../settings/connect.php';

$adminId = 0;
if (isset($_COOKIE['useradmin'])) {
    $adminId = $_COOKIE['useradmin'];
} elseif (isset($_SESSION['useradmin'])) {
    $adminId = $_SESSION['useradmin'];
}


$sql = "SELECT a.taskID, a.done, p.priority_name, a.Task_subject, a.Datend, a.Discription
        FROM tbltaskadmin a
        JOIN tbltaskpriority p ON a.priorityID = p.priority_id
        WHERE a.adminID=?
        ORDER BY a.done, a.Datend";

// Prepare the SQL statement
$stmt = $con->prepare($sql);

// Execute the query
if ($stmt->execute(array($adminId))) {
    // Fetch the rows as an associative array
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the data as JSON
    header('Content-Type: application/json');
    echo json_encode($data);
} else {
    // Handle the error if the query fails
    echo json_encode(array('error' => 'Error fetching tasks.'));
}
?>
