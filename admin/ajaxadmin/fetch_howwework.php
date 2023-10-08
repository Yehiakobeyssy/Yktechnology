<?php
// Include your database connection file here
include '../../settings/connect.php';

try {
    $stmt = $con->prepare("SELECT No, title, discription FROM  tblhowwework");
    $stmt->execute();
    $HowweWorkData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($HowweWorkData);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>
