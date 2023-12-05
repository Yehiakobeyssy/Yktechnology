<?php
    include '../../settings/connect.php';

    $service = (isset($_GET['pos']))?$_GET['pos']:0;

    echo '<option value="">Select Duration</option>';
    if($service > 0){
        $sql=$con->prepare('SELECT CodeID,Time,Service_Price FROM  tblcodetime INNER JOIN  tblservices ON  tblservices.ServiceID = tblcodetime.ServiceID WHERE POSprog=?');
        $sql->execute(array($service));
        $rows = $sql->fetchAll();
        foreach ($rows as $row){
            echo '<option value="'.$row['CodeID'].'">'.$row['Time'].' ( '.$row['Service_Price'].' $)</option>';
        }
    }else{
        $sql=$con->prepare('SELECT CodeID,Time,Service_Price FROM  tblcodetime INNER JOIN  tblservices ON  tblservices.ServiceID = tblcodetime.ServiceID');
        $sql->execute();
        $rows = $sql->fetchAll();
        foreach ($rows as $row){
            echo '<option value="'.$row['CodeID'].'">'.$row['Time'].' ( '.$row['Service_Price'].' $)</option>';
        }
    }
?>