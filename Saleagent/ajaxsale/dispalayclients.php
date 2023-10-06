<?php
session_start();
include '../../settings/connect.php';
include '../../common/function.php';

$agentId= (isset($_COOKIE['AgentID']))?$_COOKIE['AgentID']:$_SESSION['AgentID'];
$sql=$con->prepare('SELECT PromoCode FROM tblsalesperson WHERE SalePersonID =?');
$sql->execute(array($agentId));
$result=$sql->fetch();
$promocode= $result['PromoCode'];

$searchtext = (isset($_GET['search'])) ? $_GET['search'] : '';
$search = str_replace('_', ' ', $searchtext);

// Use placeholders for the search criteria
$searchParam = "%" . $search . "%";

$sql = $con->prepare("SELECT
                            c.ClientID,
                            CONCAT(c.Client_FName, ' ', c.Client_LName) AS Full_Name,
                            c.Client_Phonenumber,
                            c.Client_email,
                            c.client_active,
                            COALESCE(TI.Total_Invoice_Amount, 0) AS Total_Invoice_Amount,
                            COALESCE(TP.Total_Payment_Amount, 0) AS Total_Payment_Amount,
                            COALESCE(SUM(CASE WHEN cs.serviceStatus IN (1, 2) THEN 1 ELSE 0 END), 0) AS Service_Count
                        FROM
                            tblclients AS c
                        LEFT JOIN
                            tblclientservices AS cs ON c.ClientID = cs.ClientID
                        LEFT JOIN
                            (SELECT
                                i.ClientID,
                                SUM(CASE WHEN i.Invoice_Status IN (1, 2) THEN i.TotalAmount + i.TotalTax ELSE 0 END) AS Total_Invoice_Amount
                            FROM
                                tblinvoice AS i
                            GROUP BY
                                i.ClientID) AS TI ON c.ClientID = TI.ClientID
                        LEFT JOIN
                            (SELECT
                                p.ClientID,
                                SUM(p.Payment_Amount) AS Total_Payment_Amount
                            FROM
                                tblpayments AS p
                            JOIN
                                tblinvoice AS i ON p.invoiceID = i.InvoiceID
                            WHERE
                                i.Invoice_Status IN (1, 2)
                            GROUP BY
                                p.ClientID) AS TP ON c.ClientID = TP.ClientID
                        WHERE
                            c.promo_Code=? AND 
                            (c.ClientID LIKE ? OR
                            c.Client_FName LIKE ? OR
                            c.Client_LName LIKE ? OR
                            c.Client_Phonenumber LIKE ? OR
                            c.Client_email LIKE ?)
                        GROUP BY
                            c.ClientID, c.Client_FName, c.Client_LName, c.Client_Phonenumber, c.Client_email
                        ORDER BY
                            Total_Payment_Amount DESC");

$sql->execute(array($promocode,$searchParam, $searchParam, $searchParam, $searchParam, $searchParam));
$rows = $sql->fetchAll();

foreach ($rows as $row) {
    $clientID = $row['ClientID'];
    $fullName = $row['Full_Name'];
    $phoneNumber = $row['Client_Phonenumber'];
    $email = $row['Client_email'];

    $totalInvoiceAmount = $row['Total_Invoice_Amount'];
    $totalPaymentAmount = $row['Total_Payment_Amount'];
    $serviceCount = $row['Service_Count'];

    if($row['client_active'] == 1){
            $textblock ="Block";
            $classBlock='danger';
    }else{
            $textblock ="unblock";
            $classBlock='success';
    }
    echo '
    <tr>
        <td>' . $clientID .'</td>
        <td>' . $fullName . '</td>
        <td>' . $phoneNumber . '</td>
        <td>' . $email . '</td>
        <td>'.number_format($totalInvoiceAmount,2,'.','').' $</td>
        <td>'.number_format($totalPaymentAmount ,2,'.','').' $</td>
        <td>' . $serviceCount . '</td>
        <td>
            <a href="ManageClients.php?do=deitail&id='.$clientID.'" class="btn btn-primary btnyehia"> Deitail </a>
            <a href="ManageClients.php?do=sendemail&id='.$clientID.'" class="btn btn-secondary btnyehia"> Send mail </a>
        </td>
    </tr>
    ';
}
?>
