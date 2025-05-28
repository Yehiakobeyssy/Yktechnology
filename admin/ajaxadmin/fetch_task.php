<?php
include '../../settings/connect.php';

$sql = "SELECT
            -- Project name or fallback
            CASE 
                WHEN t.ProjectID = 0 THEN 'Not Related Project' 
                ELSE p.project_Name 
            END AS project_Name,

            -- Client name if related, otherwise null
            CASE 
                WHEN t.ProjectID = 0 THEN '' 
                ELSE CONCAT(c.Client_FName, ' ', c.Client_LName) 
            END AS client_name,

            -- Admin (assigned from)
            CONCAT(a.admin_FName, ' ', a.admin_LName) AS assignFrom,

            -- Staff (assigned to)
            CONCAT(s.Fname, ' ', s.LName) AS assignTo,

            -- Task details
            t.taskTitle,
            t.StartDate,
            t.DueDate,
            t.communicationChannel,

            -- Status
            st.Status_name AS status

        FROM tbltask t
        LEFT JOIN tbladmin a ON a.admin_ID = t.assignFrom
        LEFT JOIN tblstaff s ON s.staffID = t.Assign_to
        LEFT JOIN tblprojects p ON p.ProjectID = t.ProjectID
        LEFT JOIN tblclients c ON c.ClientID = p.ClientID
        LEFT JOIN tblstatus_task_freelancer st ON st.StatusID = t.Status

        ORDER BY t.DueDate DESC, t.Status ASC;
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