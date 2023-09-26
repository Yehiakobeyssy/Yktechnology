<?php
    include '../../settings/connect.php';

    $portid=(isset($_GET['port']))?$_GET['port']:0;

    if($portid > 0){
        $sql=$con->prepare('SELECT Discription FROM tblportfolio WHERE portfolio_ID = ?');
        $sql->execute(array($portid));
        $result=$sql->fetch();
        echo $result['Discription'];
    }
?>