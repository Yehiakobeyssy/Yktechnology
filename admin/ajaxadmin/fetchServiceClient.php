<?php

include '../../settings/connect.php';

$clientID = isset($_GET['client'])?$_GET['client']:1;

$sql = "SELECT ServicesID,ServiceTitle,Service_Name FROM tblclientservices 
        INNER JOIN tblservices ON tblservices.ServiceID = tblclientservices.ServiceID
        WHERE ClientID = ?";

// Prepare the SQL statement
$stmt = $con->prepare($sql);

// Execute the query
if ($stmt->execute(array($clientID))) {
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