<?php
include '../../settings/connect.php';

$sql = "SELECT 
            p.ProjectID,
            p.project_Name,
            CONCAT(c.Client_FName, ' ', c.Client_LName) AS ClientName,
            c.Client_email,
            CONCAT(a.admin_FName, ' ', a.admin_LName) AS ProjectManager,
            COALESCE(srv.ServiceCount, 0) AS Services,
            COALESCE(srv.TotalBudget, 0) AS Budget,
            COALESCE(dev.DevCount, 0) AS Developers,
            p.StartTime,
            p.ExpectedDate,
            p.EndDate,
            ps.Status AS Status,
            
            -- Tasks count
            COALESCE(task.TotalTasks, 0) AS TotalTasks,
            COALESCE(task.FinishedTasks, 0) AS FinishedTasks

        FROM 
            tblprojects p
        JOIN tblclients c ON p.ClientID = c.ClientID
        JOIN tbladmin a ON p.Project_Manager = a.admin_ID
        JOIN tblproject_status ps ON p.Status = ps.Status_ID

        -- Services and budget
        LEFT JOIN (
            SELECT 
                sp.ProjectID,
                COUNT(sp.ServiceProjectID) AS ServiceCount,
                SUM(cs.Price) AS TotalBudget
            FROM tblserviceproject sp
            LEFT JOIN tblclientservices cs ON sp.ServiceID = cs.ServicesID
            GROUP BY sp.ProjectID
        ) AS srv ON p.ProjectID = srv.ProjectID

        -- Developers count
        LEFT JOIN (
            SELECT 
                projectID,
                COUNT(DISTINCT Dev_Pro_ID) AS DevCount
            FROM tbldevelopers_project
            GROUP BY projectID
        ) AS dev ON p.ProjectID = dev.projectID

        -- Tasks count
        LEFT JOIN (
            SELECT 
                ProjectID,
                COUNT(CASE WHEN Status < 5 THEN 1 END) AS TotalTasks,
                COUNT(CASE WHEN Status = 4 THEN 1 END) AS FinishedTasks
            FROM tbltask
            GROUP BY ProjectID
        ) AS task ON p.ProjectID = task.ProjectID

        GROUP BY 
            p.ProjectID;"

        ;

// Prepare the SQL statement
$stmt = $con->prepare($sql);

// Execute the query
if ($stmt->execute()) {
    // Fetch the rows as an associative array
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the data as JSON
    header('Content-Type: application/json');
    echo json_encode($data);
} else {
    // Handle the error if the query fails
    echo json_encode(array('error' => 'Error fetching staff data.'));
}
?>