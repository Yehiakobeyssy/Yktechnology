<?php
    session_start();
    
    if(isset($_SESSION['shooping'])){
        $count = count($_SESSION['shooping']);
    }else{
        $count = 0;
    }
    

    echo $count ;
?>