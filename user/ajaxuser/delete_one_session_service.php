<?php
    session_start();
    
    $itemToUpdate  = (isset($_GET['key']))?$_GET['key']:'';

    if(isset($_SESSION['shooping'][$itemToUpdate])){
        unset($_SESSION['shooping'][$itemToUpdate]);
    }
?>