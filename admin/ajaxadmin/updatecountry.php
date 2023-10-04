<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Include your database connection code here
include '../../settings/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $countryID = $_POST['CountryID'];
    $countryName = $_POST['CountryName'];
    $countryTVA = $_POST['CountryTVA'];

    // Update data in the database
    $sql = $con->prepare("UPDATE tblcountrys SET CountryName = ?, CountryTVA = ? WHERE CountryID = ?");
    if ($sql->execute([$countryName, $countryTVA, $countryID])) {
        echo 'success';
    } else {
        echo 'error';
    }
}
if ($sql->execute([$countryName, $countryTVA, $countryID])) {
    echo 'success';
} else {
    error_log('Database update error: ' . implode(', ', $sql->errorInfo()));
    echo 'error';
}
?>
