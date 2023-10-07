<?php
    include '../../settings/connect.php';

    $paymentmethod = isset($_GET['id']) ? $_GET['id'] : 0;

    if ($paymentmethod === 0) {
        $sql = $con->prepare('SELECT note FROM tblpayment_method LIMIT 1');
        $sql->execute();
        $result = $sql->fetch();
        echo nl2br($result['note']);
    } else {
        $sql = $con->prepare('SELECT note FROM tblpayment_method WHERE paymentmethodD = ?');
        $sql->execute(array($paymentmethod));
        $result = $sql->fetch();
        if ($result) {
            echo nl2br($result['note']);
        } else {
            $sql = $con->prepare('SELECT note FROM tblpayment_method LIMIT 1');
            $sql->execute();
            $result = $sql->fetch();
            echo nl2br($result['note']);
        }
    }
    
?>