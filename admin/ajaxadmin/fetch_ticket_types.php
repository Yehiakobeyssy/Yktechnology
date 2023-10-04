<?php
// Include your database connection file here
include '../../settings/connect.php';

try {
    $stmt = $con->prepare("SELECT TypeTicketID, TypeTicket FROM tbltypeoftickets");
    $stmt->execute();
    $ticketTypesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($ticketTypesData);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
