<?php
include '../../settings/connect.php';

$sql = "SELECT 
            staffID,
            CONCAT(Fname, ' ', MidelName, ' ', LName) AS fullname,
            Staff_Phone,
            Staff_email,
            Region,
            Staff_address,
            Possition_Name,
            expected_sallary,
            DatewillBegin,
            block,
            accepted,
            CASE 
                WHEN block = 1 THEN 'blocked'
                WHEN block = 0 AND accepted = 1 THEN 'accepted'
                WHEN block = 0 AND accepted = 0 THEN 'on Study'
                ELSE 'unknown'
            END AS status
        FROM tblstaff
        INNER JOIN tblpossition_request 
        ON Possition_ID = Posstion
        ORDER BY 
            CASE 
                WHEN block = 0 AND accepted = 0 THEN 0  -- on Study first
                WHEN block = 0 AND accepted = 1 THEN 1  -- accepted second
                WHEN block = 1 THEN 2                   -- blocked last
                ELSE 3
            END";
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
