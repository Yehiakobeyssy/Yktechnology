<?php
// Include your database connection code here
include '../../settings/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $typeTicketID = $_POST['id'];

    // Delete the ticket service from the database
    $sql = $con->prepare("DELETE FROM tbltypeoftickets WHERE TypeTicketID = ?");
    if ($sql->execute([$typeTicketID])) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
