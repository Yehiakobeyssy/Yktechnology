<?php

    include '../../settings/connect.php';
    try {
        $stmt = $con->prepare("SELECT 
            p.paymentID,
            p.Payment_Date,
            CONCAT(c.Client_FName, ' ', c.Client_LName) AS ClientName,
            p.invoiceID,
            pm.methot,
            p.NoofDocument,
            CONCAT('$', FORMAT(p.Payment_Amount, 2)) AS FormattedPaymentAmount
        FROM tblpayments AS p
        INNER JOIN tblclients AS c ON p.ClientID = c.ClientID
        INNER JOIN tblpayment_method AS pm ON p.paymentMethod = pm.paymentmethodD
        ORDER BY p.paymentID DESC");

        $stmt->execute();
        $paymentsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($paymentsData);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
?>
