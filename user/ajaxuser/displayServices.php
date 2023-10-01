<?php
    session_start();
    include '../../settings/connect.php';
    include '../../common/function.php';

    $clientId= (isset($_COOKIE['user']))?$_COOKIE['user']:$_SESSION['user'];

    $searchtext=(isset($_GET['search']))?$_GET['search']:'';
    $search = str_replace('_', ' ', $searchtext);


    $sql = $con->prepare('SELECT
                                cs.ServicesID,
                                ts.Service_Name,
                                cs.ServiceTitle,
                                cs.Price,
                                d.DurationName,
                                cs.Dateend,
                                ss.Status,
                                ss.Status_Color
                        FROM
                            tblclientservices cs
                        JOIN
                            tblservices ts ON cs.ServiceID = ts.ServiceID
                        JOIN
                            tblduration d ON ts.Duration = d.DurationID
                        JOIN
                            tblstatusservices ss ON cs.serviceStatus = ss.StatusSerID
                        WHERE
                            cs.ClientID = ?
                            AND (ts.Service_Name LIKE ? OR cs.ServiceTitle LIKE ? OR d.DurationName LIKE ? OR ss.Status LIKE ?)
                        ORDER BY
                            cs.Dateend DESC;');

    if(!empty($search)){
        $search="%".$search."%";
    }else{
        $search="%"." "."%";  
    }
    $sql->execute(array($clientId,$search,$search,$search,$search));
    $count=$sql->rowCount();
    $rows = $sql->fetchAll();

    if(isset($_GET['count'])){
        echo $count;
    }else{
        foreach($rows as $row){
            echo '<tr class="rowService" data-index="'. $row['ServicesID'] .'">
                    <td>
                        <h4>'.$row['Service_Name'].'</h4>
                        <label>'.$row['ServiceTitle'].'</label>
                    </td>
                    <td>
                        <h4>'.number_format($row['Price'] ,2,'.','').' $'.'</h4>
                        <label>'.$row['DurationName'].'</label>
                    </td>
                    <td>' . $row['Dateend'] . '</td>
                    <td style="color:'.$row['Status_Color'].'">' . $row['Status'] . '</td>
                    
                </tr>
            ';
        }
    }
?>

<script>
    jQuery('.rowService').click(function(){
        let serviceID = jQuery(this).attr('data-index');
        location.href="viewService.php?id="+serviceID;
    })
</script>