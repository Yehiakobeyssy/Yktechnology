<?php
// Include your database connection code here
include '../../settings/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $HowweworkNo = $_POST['No'];

    // Delete the country from the database
    $sql = $con->prepare("DELETE FROM tblhowwework WHERE No = ?");
    if ($sql->execute([$HowweworkNo])) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>



