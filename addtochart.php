<?php
    session_start();
    $serviceID=(isset($_GET['serID']))?$_GET['serID']:0;
    if( $serviceID > 0){
        if(isset($_SESSION['shooping'])){
            $itemarray=$serviceID;
            array_push($_SESSION['shooping'],$itemarray);
        }else{
            $itemarray=$serviceID;
        $_SESSION['shooping'][0]= $itemarray;
        };
    }
?>