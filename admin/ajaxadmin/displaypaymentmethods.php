<?php
    session_start();
    include '../../settings/connect.php';
    include '../../common/function.php';

    $searchtext = (isset($_GET['search'])) ? $_GET['search'] : '';
    $search = str_replace('_', ' ', $searchtext);

    $searchParam = "%" . $search . "%";

    $sql=$con->prepare('SELECT paymentmethodD, methot, note, method_active
                        FROM tblpayment_method
                        WHERE methot LIKE ? OR note LIKE ?;');
    $sql->execute(array($searchParam,$searchParam));
    $rows= $sql->fetchAll();

    foreach($rows as $row){
        if($row['method_active'] == 1){
            $link = '<a href="managePaymentMethod.php?do=Delete&id='.$row['paymentmethodD'].'" class="btn btn-danger">Delete</a>';
        }else{
            $link = '<a href="managePaymentMethod.php?do=Delete&id='.$row['paymentmethodD'].'" class="btn btn-success">Return</a>';
        }
        echo '
            <div class="cardPay">
                <h1>'.$row['methot'].'</h1>
                <div class="dis">
                    <p>'.$row['note'].'</p>
                </div>
                <div class="control">
                    <a href="managePaymentMethod.php?do=edid&id='.$row['paymentmethodD'].'" class="btn btn-warning">Edid</a>
                    '.$link.'
                </div>
            </div>
        ';
    }
?>