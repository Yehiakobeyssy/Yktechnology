<?php
// Include your database connection file here
include '../../settings/connect.php';

try {
    $stmt = $con->prepare("SELECT DomainTypeID , ServiceName FROM  tbldomaintype");
    $stmt->execute();
    $domainTypesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($domainTypesData);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
