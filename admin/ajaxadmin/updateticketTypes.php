<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include your database connection code here
include '../../settings/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $typeTicketID = $_POST['TypeTicketID'];
    $newTypeTicketName = $_POST['TypeTicketName'];

    // Update data in the database
    $sql = $con->prepare("UPDATE tbltypeoftickets SET TypeTicket = ? WHERE TypeTicketID = ?");
    
    if ($sql->execute([$newTypeTicketName, $typeTicketID])) {
        echo 'success';
    } else {
        error_log('Database update error: ' . implode(', ', $sql->errorInfo()));
        echo 'error';
    }
}
?>
