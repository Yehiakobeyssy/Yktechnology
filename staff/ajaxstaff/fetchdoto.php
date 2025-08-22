<?php
    session_start();
    include '../../settings/connect.php';
    $staff_Id= (isset($_COOKIE['staff']))?$_COOKIE['staff']:$_SESSION['staff'];


    $sql="SELECT 
            dotoID,
            priority_name,
            taskSubject,
            disktiption,
            DateEnd,
            done
            FROM tbldoto
            INNER JOIN tbltaskpriority ON tbltaskpriority.priority_id = priority
            WHERE freelancer_ID = ?
            ORDER BY done";
    
    $stmt = $con->prepare($sql);
    if ($stmt->execute([$staff_Id])) {
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        echo json_encode(array('error' => 'Error fetching staff data.'));
    }
?>