<?php
session_start();
include '../../settings/connect.php';
include '../../common/function.php';

$searchtext = (isset($_GET['search'])) ? $_GET['search'] : '';
$search = str_replace('_', ' ', $searchtext);

// Use placeholders for the search criteria
$searchParam = "%" . $search . "%";

$sql=$con->prepare("SELECT
                            tblclientservices.ServicesID AS CServiceID ,
                            CONCAT(tblclients.Client_FName, ' ', tblclients.Client_LName) AS `Full Name`,
                            CONCAT(tblservices.Service_Name, ' (', tblclientservices.ServiceTitle, ')') AS `service`,
                            tblclientservices.Price AS `price`,
                            tblclientservices.Date_service AS `Datebegin`,
                            tblclientservices.Dateend AS `Date end`,
                            tblstatusservices.Status AS `status`,
                            tblclientservices.ServiceDone AS `admin done`
                    FROM
                            tblclients
                    JOIN    tblclientservices ON tblclients.ClientID = tblclientservices.ClientID
                    JOIN    tblservices ON tblclientservices.ServiceID = tblservices.ServiceID
                    JOIN    tblstatusservices ON tblclientservices.serviceStatus = tblstatusservices.StatusSerID
                    WHERE
                        tblclients.Client_FName LIKE ? OR
                        tblclients.Client_LName LIKE ? OR
                        tblservices.Service_Name LIKE ? OR
                        tblclientservices.ServiceTitle LIKE ? OR
                        tblstatusservices.Status LIKE ?
                    ORDER BY
                        tblclientservices.ServiceDone ASC; 
                    ");
$sql->execute(array($searchParam,$searchParam,$searchParam,$searchParam,$searchParam));

$rows =$sql->fetchAll();

foreach($rows as $row){
    if($row['admin done']==0){
        $textadmin='in progress';
    }else{
        $textadmin='Finish';
    }
    echo '
        <tr class="rowService" data-index="'.$row['CServiceID'].'">
            <td>'.$row['Full Name'].'</td>
            <td>'.$row['service'].'</td>
            <td>'.number_format($row['price'],2,'.','').' $</td>
            <td>'.$row['Datebegin'].'</td>
            <td>'.$row['Date end'].'</td>
            <td>'.$row['status'].'</td>
            <td>'.$textadmin.'</td>
            <td>
                <a href="ManageServiceclient.php?do=Edit&id='.$row['CServiceID'].'" class="ctlyehia" style="color:orange"><i class="fa-solid fa-pen"></i> <span>Edit<span></a>
                <a href="ManageServiceclient.php?do=done&id='.$row['CServiceID'].'" class="ctlyehia" style="color:green"><i class="fa-solid fa-check-double"></i> <span>done<span></a>
                <a href="ManageServiceclient.php?do=Reactive&id='.$row['CServiceID'].'" class="ctlyehia" style="color:#1c4e80"><i class="fa-solid fa-check-double"></i> <span>active<span></a>
                <a href="ManageServiceclient.php?do=cancelService&id='.$row['CServiceID'].'" class="ctlyehia" style="color:red"><i class="fa-solid fa-eraser"></i></i><span> canceld <span></a>
            </td>
        </tr>
    ';
}
?>
<script>
    jQuery('.rowService').click(function(){
        let serviceID=jQuery(this).attr('data-index');
        location.href="ManageServiceclient.php?do=Deiteil&id="+serviceID;
    })
</script>