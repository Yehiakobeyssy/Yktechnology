<?php
session_start();
include '../../settings/connect.php';

$data = array(); // Initialize as empty array

if (isset($_SESSION['ServiceProject'])) {
    $totalbudget=0;
    foreach ($_SESSION['ServiceProject'] as $index => $item) {
        $itemid = $item['id'];
        $note = $item['note'];

        $sql = $con->prepare('SELECT ServiceID, ServiceTitle, Price FROM tblclientservices WHERE ServicesID = ?');
        $sql->execute(array($itemid));
        $statement = $sql->fetch();

        if ($statement) {
            $data[] = array(
                'serviceID'    => $statement['ServiceID'],
                'ServiceTitle' => $statement['ServiceTitle'],
                'Budget'       => $statement['Price'],
                'note'         => $note
            );
            $totalbudget+=$statement['Price'];
        }
    }
}

header('Content-Type: application/json');
echo json_encode([
    'services'     => $data,
    'totalbudget'  => $totalbudget
]);
?>
