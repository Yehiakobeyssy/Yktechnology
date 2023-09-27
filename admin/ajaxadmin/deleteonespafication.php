<?php
    include '../../settings/connect.php';

    $spaficID = (isset($_GET['spaID']))?$_GET['spaID']:0;

    if($spaficID > 0){

        $sql=$con->prepare('DELETE FROM tblspeafications WHERE SpeaficationsID = ?');
        $sql->execute(array($spaficID));
    }
?>