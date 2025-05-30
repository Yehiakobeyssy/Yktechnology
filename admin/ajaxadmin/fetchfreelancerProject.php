<?php
session_start();
include '../../settings/connect.php';

$data = array(); 

if (isset($_SESSION['freelancerProject'])) {
    foreach ($_SESSION['freelancerProject'] as $index => $item) {
        $freelancerId = $item['id'];
        $Service      = $item['Service'];
        $share        = $item['share'];
        $note         = $item['note'];

        $sql = $con->prepare('SELECT Fname, LName FROM tblstaff WHERE staffID = ?');
        $sql->execute([$freelancerId]);
        $statement = $sql->fetch(); // ✅ use the same variable name

        if ($statement) {
            $data[] = array(
                'Name'    => $statement['Fname'] . ' ' . $statement['LName'],
                'Service' => $Service,
                'Share'   => $share,
                'Note'    => $note
            );
        }
    }
}

header('Content-Type: application/json');
echo json_encode(['freelancers' => $data]); // ✅ wrap result in a key

?>
