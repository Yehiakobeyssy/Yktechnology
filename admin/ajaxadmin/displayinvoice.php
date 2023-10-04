<?php
session_start();
include '../../settings/connect.php';
include '../../common/function.php';

$searchtext = (isset($_GET['search'])) ? $_GET['search'] : '';
$search = str_replace('_', ' ', $searchtext);


$searchParam = "%" . $search . "%";

$sql = $con->prepare("
                        SELECT
                            tblinvoice.InvoiceID,
                            CONCAT(tblclients.Client_FName, ' ', tblclients.Client_LName) AS 'Client Name',
                            tblinvoice.InvoiceDate,
                            (tblinvoice.TotalAmount + tblinvoice.TotalTax) AS 'Total Amount',
                            COALESCE(SUM(tblpayments.Payment_Amount), 0) AS 'Total Payment',
                            tblstatusinvoice.StatusInvoice
                        FROM
                            tblinvoice
                        JOIN
                            tblclients ON tblinvoice.ClientID = tblclients.ClientID
                        JOIN
                            tblstatusinvoice ON tblinvoice.Invoice_Status = tblstatusinvoice.StatusInvoiceID
                        LEFT JOIN
                            tblpayments ON tblinvoice.InvoiceID = tblpayments.invoiceID
                        WHERE
                            tblinvoice.InvoiceID LIKE ? OR
                            CONCAT(tblclients.Client_FName, ' ', tblclients.Client_LName) LIKE ? OR
                            tblstatusinvoice.StatusInvoice LIKE ?
                        ORDER BY
                            tblstatusinvoice.StatusInvoice;
                    ");

$sql->execute(array($searchParam,$searchParam,$searchParam));
$rows = $sql->fetchAll();
foreach ($rows as $row) {
    if ($row['Total Amount'] !== null) {
        $paymentDetails = calculatePaymentDetails($row['Total Amount']);
    } else {
        $paymentDetails = array(
            'numberOfPayments' => 0,
            'paymentAmount' => 0,
            'overpayment' => 0,
            'paymentsMade' => 0,
            'remainingPayments' => 0
        );
    }
    echo '
    <tr>
        <td>' . $row['InvoiceID'] . '</td>
        <td>' . $row['Client Name'] . '</td>
        <td>' . $row['InvoiceDate'] . '</td>
        <td>' . $paymentDetails['numberOfPayments'] . ' </td>
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
