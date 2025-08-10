<?php
    session_start();
    include '../../settings/connect.php';
    $staff_Id= (isset($_COOKIE['staff']))?$_COOKIE['staff']:$_SESSION['staff'];


    $sql="SELECT 
            taskID,
            admin_FName,
            admin_LName,
            CASE 
                WHEN tbltask.ProjectID = 0 THEN 'Not Related Project' 
                ELSE tblprojects.project_Name 
            END AS project_Name,
            taskTitle,
            StartDate,
            DueDate,
            FinishDate,
            Status_name
            FROM tbltask
            INNER JOIN  tbladmin ON  tbladmin.admin_ID =  tbltask.assignFrom
            LEFT JOIN tblprojects  ON tblprojects.ProjectID = tbltask.ProjectID
            INNER JOIN tblstatus_task_freelancer ON tblstatus_task_freelancer.StatusID = tbltask.Status
            WHERE Assign_to = ?
            ORDER BY tbltask.Status";
    
    $stmt = $con->prepare($sql);
    if ($stmt->execute([$staff_Id])) {
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        echo json_encode(array('error' => 'Error fetching staff data.'));
    }
?>