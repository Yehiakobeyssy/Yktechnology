<?php
// Include your database connection file here
include '../../settings/connect.php';

try {
    $stmt = $con->prepare("SELECT CountryID, CountryName, CountryTVA FROM tblcountrys");
    $stmt->execute();
    $countriesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($countriesData);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
