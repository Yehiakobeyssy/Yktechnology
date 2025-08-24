<?php
include '../../settings/connect.php';

$sql = "SELECT 
            s.staffID,
            CONCAT(s.Fname, ' ', s.MidelName, ' ', s.LName) AS fullname,
            s.Staff_Phone,
            s.Staff_email,
            s.Region,
            s.Staff_address,
            p.Possition_Name,
            s.expected_sallary,
            s.DatewillBegin,
            s.block,
            s.accepted,
            CASE 
                WHEN s.block = 1 THEN 'blocked'
                WHEN s.block = 0 AND s.accepted = 1 THEN 'accepted'
                WHEN s.block = 0 AND s.accepted = 0 THEN 'on Study'
                ELSE 'unknown'
            END AS status,
            COALESCE(SUM(a.depit - a.criedit), 0) AS balance
        FROM tblstaff s
        INNER JOIN tblpossition_request p
            ON p.Possition_ID = s.Posstion
        LEFT JOIN tblaccountstatment_staff a
            ON a.staffID = s.staffID
        GROUP BY 
            s.staffID, s.Fname, s.MidelName, s.LName,
            s.Staff_Phone, s.Staff_email, s.Region, s.Staff_address,
            p.Possition_Name, s.expected_sallary, s.DatewillBegin,
            s.block, s.accepted
        ORDER BY 
            CASE 
                WHEN s.block = 0 AND s.accepted = 0 THEN 0  -- on Study first
                WHEN s.block = 0 AND s.accepted = 1 THEN 1  -- accepted second
                WHEN s.block = 1 THEN 2                     -- blocked last
                ELSE 3
            END;
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
