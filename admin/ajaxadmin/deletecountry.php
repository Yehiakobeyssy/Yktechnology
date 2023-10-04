<?php
// Include your database connection code here
include '../../settings/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $countryID = $_POST['id'];

    // Delete the country from the database
    $sql = $con->prepare("DELETE FROM tblcountrys WHERE CountryID = ?");
    if ($sql->execute([$countryID])) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>



