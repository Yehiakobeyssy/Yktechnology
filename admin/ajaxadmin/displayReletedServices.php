<?php
    session_start();
    include '../../settings/connect.php';
    include '../../common/function.php';

    $cleintID= (isset($_GET['clid']))?$_GET['clid']:0;

    $sql=$con->prepare('SELECT ServicesID,ServiceTitle,Service_Name 
                        FROM tblclientservices 
                        INNER JOIN  tblservices ON tblservices.ServiceID = tblclientservices.ServiceID
                        WHERE serviceStatus < 4 AND ClientID =?');
    $sql->execute(array($cleintID));
    $check = $sql->rowCount();

    if($check == 0){
        echo '<option value="">NOT Releted</option>';
    }else{
        $services = $sql->fetchAll();
        echo '<option value="">NOT Releted</option>';
        foreach ($services as $ser){
            echo '<option value="'.$ser['ServicesID'].'">'.$ser['Service_Name'].' ( '.$ser['ServiceTitle'] .' )</option>';
        }
    }
?>