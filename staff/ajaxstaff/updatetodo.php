<?php
    session_start();
    include '../../settings/connect.php';
    $dotoID= (isset($_GET['index']))?$_GET['index']:0;

    $sql=$con->prepare('SELECT done FROM  tbldoto  WHERE dotoID  = ?');
    $sql->execute(array($dotoID));
    $result = $sql->fetch();
    $done = $result['done'];

    if($done== 1){
        $stat ="UPDATE tbldoto SET done = 0 WHERE dotoID= ?";
    }elseif($done==0){
        $stat ="UPDATE tbldoto SET done = 1 WHERE dotoID= ?";
    }
    $newsql=$con->prepare($stat);
    $newsql->execute(array($dotoID))
    
    
?>