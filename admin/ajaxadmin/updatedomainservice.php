<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include your database connection code here
include '../../settings/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $domainTypeID = $_POST['DomainTypeID'];
    $serviceName = $_POST['ServiceName'];

    // Update data in the database
    $sql = $con->prepare("UPDATE tbldomaintype SET ServiceName = ? WHERE DomainTypeID = ?");
    
    if ($sql->execute([$serviceName, $domainTypeID])) {
        echo 'success';
    } else {
        error_log('Database update error: ' . implode(', ', $sql->errorInfo()));
        echo 'error';
    }
}
?>
