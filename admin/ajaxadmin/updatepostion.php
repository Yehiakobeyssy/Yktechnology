<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../../settings/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Possition_ID = $_POST['Possition_ID'];
    $Possition_Name = $_POST['Possition_Name'];
    $active_postion = $_POST['active_postion'];

    $sql = $con->prepare("UPDATE tblpossition_request SET Possition_Name = ?, active_postion = ? WHERE Possition_ID = ?");
    
    if ($sql->execute([$Possition_Name, $active_postion, $Possition_ID])) {
        echo 'success';
    } else {
        error_log('Update Error: ' . implode(', ', $sql->errorInfo()));
        echo 'error';
    }
}
?>
