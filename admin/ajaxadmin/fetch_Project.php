<?php
include '../../settings/connect.php';

$sql = "SELECT 
            p.project_Name,
            CONCAT(c.Client_FName, ' ', c.Client_LName) AS ClientName,
            c.Client_email,
            CONCAT(a.admin_FName, ' ', a.admin_LName) AS ProjectManager,
            COUNT(DISTINCT sp.ServiceProjectID) AS Services,
            SUM(cs.Price) AS Budget,
            COUNT(DISTINCT dp.Dev_Pro_ID) AS Developers,
            p.StartTime,
            p.ExpectedDate,
            p.EndDate,
            ps.Status AS Status
        FROM 
            tblprojects p
        JOIN tblclients c ON p.ClientID = c.ClientID
        JOIN tbladmin a ON p.Project_Manager = a.admin_ID
        JOIN tblproject_status ps ON p.Status = ps.Status_ID
        LEFT JOIN tblserviceproject sp ON p.ProjectID = sp.ProjectID
        LEFT JOIN tblclientservices cs ON sp.ServiceID = cs.ServicesID
        LEFT JOIN tbldevelopers_project dp ON p.ProjectID = dp.projectID
        GROUP BY 
            p.ProjectID;
        ";

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