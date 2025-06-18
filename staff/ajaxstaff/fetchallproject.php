<?php
    session_start();
    include '../../settings/connect.php';
    $staff_Id= (isset($_COOKIE['staff']))?$_COOKIE['staff']:$_SESSION['staff'];


    $sql="SELECT 
            p.ProjectID,
            p.project_Name AS Project_Name,
            CONCAT(c.Client_FName, ' ', c.Client_LName) AS Client_Name,
            CONCAT(a.admin_FName, ' ', a.admin_LName) AS Manager,
            dp.Posstion AS Posstion,
            COUNT(t.taskID) AS Tasks,
            ps.Status
        FROM tbldevelopers_project dp
        INNER JOIN tblprojects p ON dp.projectID = p.ProjectID
        INNER JOIN tblproject_status ps ON p.Status = ps.Status_ID
        INNER JOIN tblclients c ON p.ClientID = c.ClientID
        INNER JOIN tbladmin a ON p.Project_Manager = a.admin_ID
        LEFT JOIN tbltask t 
            ON t.ProjectID = p.ProjectID 
            AND t.Assign_to = ?
        WHERE dp.FreelancerID = ?
        AND p.Status IN (1, 2)
        GROUP BY p.ProjectID";
    
    $stmt = $con->prepare($sql);
    if ($stmt->execute([$staff_Id, $staff_Id])) {
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        echo json_encode(array('error' => 'Error fetching staff data.'));
    }
?>