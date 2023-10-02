<?php
    session_start();
    include '../../settings/connect.php';
    include '../../common/function.php';

    $clientId= (isset($_COOKIE['user']))?$_COOKIE['user']:$_SESSION['user'];

    $searchtext=(isset($_GET['search']))?$_GET['search']:'';
    $search = str_replace('_', ' ', $searchtext);


    $sql = $con->prepare('SELECT
                            dt.ServiceName AS ServiceName,
                            dc.DomeinName AS DomeinName,
                            dc.DateBegin AS DateBegin,
                            dc.RenewDate AS RenewDate,
                            dc.Price_Renew AS Price_Renew,
                            sd.StatusDomein AS StatusDomein,
                            sd.StatusColor AS StatusColor,
                            dc.ServiceID AS ServiceID
                        FROM
                            tbldomeinclients dc
                        JOIN
                            tbldomaintype dt ON dc.ServiceType = dt.DomainTypeID
                        JOIN
                            tblstatusdomein sd ON dc.Status = sd.StatusDomeinID
                        WHERE
                            dc.Client = ?
                            AND (dt.ServiceName LIKE ? OR sd.StatusDomein LIKE ? OR dc.DomeinName LIKE ?)
                        ORDER BY
                            dc.RenewDate DESC;
                        ');

    if(!empty($search)){
        $search="%".$search."%";
    }else{
        $search="%"." "."%";  
    }
    $sql->execute(array($clientId,$search,$search,$search));
    $count=$sql->rowCount();
    $rows = $sql->fetchAll();

    if(isset($_GET['count'])){
        echo $count;
    }else{
        foreach($rows as $row){
            echo '<tr class="rowService" >
                    <td>
                        <h4>'.$row['ServiceName'].'</h4>
                        <label>'.$row['DomeinName'].'</label>
                    </td>
                    <td>' . $row['DateBegin'] . ' </td>
                    <td>' . $row['RenewDate'] . '</td>
                    <td>'.number_format($row['Price_Renew'] ,2,'.','').' $'.'</td>
                    <td style="color:'.$row['StatusColor'].'">' . $row['StatusDomein'] . '</td>
                    <td></td>
                </tr>
            ';
        }
    }
?>

<script>

</script>