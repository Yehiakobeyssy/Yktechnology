<?php
    session_start();
    include '../../settings/connect.php';

    $taskID = ($_GET['id'])?$_GET['id']:0;

    $sql= "SELECT notes FROM  tbltask WHERE taskID = ?";
    $stmt = $con->prepare($sql);
    if ($stmt->execute([$taskID])) {
        $data = $stmt->fetch(PDO::FETCH_ASSOC); // بدل fetchAll
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        echo json_encode(array('error' => 'Error fetching task data.'));
    }
?>