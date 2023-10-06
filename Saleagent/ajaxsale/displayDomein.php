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


$searchParam = "%" . $search . "%";

$sql = $con->prepare("SELECT
                        CONCAT(tblclients.Client_FName, ' ', tblclients.Client_LName) AS 'Client name',
                        CONCAT(tbldomaintype.ServiceName, ' (', tbldomeinclients.DomeinName, ')') AS 'Plan',
                        tbldomeinclients.DateBegin AS 'Date',
                        tbldomeinclients.RenewDate AS 'Renewal Date',
                        tbldomeinclients.Price_Renew AS 'Renewal Price',
                        tbldomeinclients.Note AS 'Note',
                        tblstatusdomein.StatusDomein AS 'status',
                        tbldomeinclients.DomeinID AS 'DID'
                    FROM
                        tbldomeinclients
                    INNER JOIN
                        tblclients ON tbldomeinclients.Client = tblclients.ClientID
                    INNER JOIN
                        tbldomaintype ON tbldomeinclients.ServiceType = tbldomaintype.DomainTypeID
                    INNER JOIN
                        tblstatusdomein ON tbldomeinclients.Status = tblstatusdomein.StatusDomeinID
                    WHERE
                        tblclients.promo_Code = ? AND 
                        (tblclients.Client_FName LIKE ?
                        OR tblclients.Client_LName LIKE ?
                        OR tbldomaintype.ServiceName LIKE ?
                        OR tbldomeinclients.DomeinName LIKE ?
                        OR tbldomeinclients.Note LIKE ?
                        OR tblstatusdomein.StatusDomein LIKE ?)
                    ORDER BY
                        tbldomeinclients.Status,tbldomeinclients.RenewDate
                    ");

$sql->execute(array($promocode,$searchParam,$searchParam,$searchParam,$searchParam,$searchParam,$searchParam));
$rows = $sql->fetchAll();

foreach ($rows as $row) {
    echo '
    <tr>
        <td>'.$row['Client name'].'</td>
        <td>'.$row['Plan'].'</td>
        <td>'.$row['Date'].'</td>
        <td>'.$row['Renewal Date'].'</td>
        <td>'.number_format($row['Renewal Price'],2,'.','').' $</td>
        <td>'.$row['Note'].'</td>
        <td>'.$row['status'].'</td>
        <td class="icon-cell">
            <i class="fa-solid fa-ellipsis-vertical"></i>
            <div class="hover-content">
                <a href="ManageDomein.php?do=transfered&id='.$row['DID'].'">Transfer</a>
                <a href="ManageDomein.php?do=cancel&id='.$row['DID'].'">Cancel</a>
            </div>
        </td>
    </tr>
    ';
}
?>
