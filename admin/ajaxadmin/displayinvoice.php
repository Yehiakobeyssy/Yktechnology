<?php
session_start();
include '../../settings/connect.php';
include '../../common/function.php';

$searchtext = (isset($_GET['search'])) ? $_GET['search'] : '';
$search = str_replace('_', ' ', $searchtext);


$searchParam = "%" . $search . "%";

$sql = $con->prepare("SELECT
                        tblinvoice.InvoiceID,
                        CONCAT(tblclients.Client_FName, ' ', tblclients.Client_LName) AS 'Client Name',
                        tblinvoice.InvoiceDate,
                        (tblinvoice.TotalAmount + tblinvoice.TotalTax) AS 'Total Amount',
                        COALESCE(
                            (SELECT SUM(tblpayments.Payment_Amount) 
                            FROM tblpayments 
                            WHERE tblpayments.invoiceID = tblinvoice.InvoiceID),
                            0
                        ) AS 'Total Payment',
                        tblstatusinvoice.StatusInvoice
                        FROM
                            tblinvoice
                        JOIN
                            tblclients ON tblinvoice.ClientID = tblclients.ClientID
                        JOIN
                            tblstatusinvoice ON tblinvoice.Invoice_Status = tblstatusinvoice.StatusInvoiceID
                        WHERE
                            tblinvoice.InvoiceID LIKE ? OR 
                            CONCAT(tblclients.Client_FName, ' ', tblclients.Client_LName) LIKE ? OR
                            tblstatusinvoice.StatusInvoice LIKE ?
                        ORDER BY
                            tblinvoice.Invoice_Status ;
                    ");

$sql->execute(array($searchParam,$searchParam,$searchParam));

$rows = $sql->fetchAll();
$check= $sql->rowCount();

foreach ($rows as $row) {
    echo '
    <tr>
        <td>' . $row['InvoiceID'] . '</td>
        <td>' . $row['Client Name'] . '</td>
        <td>' . $row['InvoiceDate'] . '</td>
        <td>' . number_format($row['Total Amount'], 2, '.', '') . ' $</td>
        <td>' . number_format($row['Total Payment'], 2, '.', '') . ' $</td>
        <td>' . number_format($row['Total Amount']-$row['Total Payment'], 2, '.', '') . ' $</td>
        <td>' . $row['StatusInvoice'] . '</td>
        <td class="icon-cell">
            <i class="fa-solid fa-ellipsis-vertical"></i>
            <div class="hover-content">
                <a href="ManageInvoices.php?do=detail&id='.$row['InvoiceID'].'">Detail</a>
                <a href="ManageInvoices.php?do=payment&id='.$row['InvoiceID'].'">New Payment</a>
                <a href="ManageInvoices.php?do=cancel&id='.$row['InvoiceID'].'">Cancel</a>
            </div>
        </td>
    </tr>
    ';

}


?>
