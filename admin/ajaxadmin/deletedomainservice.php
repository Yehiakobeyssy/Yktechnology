<?php
// Include your database connection code here
include '../../settings/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $domainTypeID = $_POST['id'];

    // Delete the domain service from the database
    $sql = $con->prepare("DELETE FROM tbldomaintype WHERE DomainTypeID = ?");
    if ($sql->execute([$domainTypeID])) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
