<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Include your database connection code here
include '../../settings/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $howweworkno = $_POST['No'];
    $howweworktitle = $_POST['title'];
    $howweworkdiscription = $_POST['discription'];

    // Update data in the database
    $sql = $con->prepare("UPDATE  tblhowwework SET title = ?, discription = ? WHERE HowID  = ?");
    if ($sql->execute([$howweworktitle, $howweworkdiscription, $howweworkno])) {
        echo 'success';
    } else {
        echo 'error';
    }
}
if ($sql->execute([$howweworktitle, $howweworkdiscription, $howweworkno])) {
    echo 'success';
} else {
    error_log('Database update error: ' . implode(', ', $sql->errorInfo()));
    echo 'error';
}
?>
