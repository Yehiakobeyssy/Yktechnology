<?php
    session_start();
    $serviceID=(isset($_GET['serID']))?$_GET['serID']:0;
    if( $serviceID > 0){
        if(isset($_SESSION['shooping'])){
            $itemarray=array(
                    'id' => $serviceID,
                    'titlename' => '',
                    'domain' => '',
                    'transfer' => '',
                    'code' => '',
                    'forwhat' => '',
                    'colors' => '',
                    'description' => '',
                    'filename' => ''
                );
            array_push($_SESSION['shooping'],$itemarray);
        }else{
            $itemarray=array(
                    'id' => $serviceID,
                    'titlename' => '',
                    'domain' => '',
                    'transfer' => '',
                    'code' => '',
                    'forwhat' => '',
                    'colors' => '',
                    'description' => '',
                    'filename' => ''
                );
        $_SESSION['shooping'][0]= $itemarray;
        };
    }
?>