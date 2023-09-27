<?php
    include '../../settings/connect.php';

    $serviceID = (isset($_GET['serviceID']))?$_GET['serviceID']:0;
    $textspeafication = (isset($_GET['text']))?$_GET['text']:'';
    $speaficationtext = str_replace('_', ' ', $textspeafication);

    if(!empty($speaficationtext) && $serviceID > 0){
        $sql=$con->prepare('INSERT INTO tblspeafications (ServiceID,Speafications)
                            VALUES (:ServiceID,:Speafications)');
        $sql->execute(array(
        'ServiceID'     =>$serviceID,
        'Speafications' =>$speaficationtext
        ));
    }

?>