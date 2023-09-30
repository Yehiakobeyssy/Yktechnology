<?php
    session_start();
    if(!isset($_COOKIE['user'])){
        if(!isset($_SESSION['user'])){
            header('location:index.php');
        }
    }
    $clientId= (isset($_COOKIE['user']))?$_COOKIE['user']:$_SESSION['user'];
    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';

    $sql=$con->prepare('SELECT Client_FName,Client_LName,client_active FROM  tblclients WHERE ClientID=?');
    $sql->execute(array($clientId));
    $clientinfo = $sql->fetch();
    $clientName = $clientinfo['Client_FName'].' '. $clientinfo['Client_LName'];

    if($clientinfo['client_active'] == 0){
        setcookie("user","",time()-3600);
        unset($_SESSION['user']);
        echo '<script> location.href="index.php" </script>';
    }
?>
    <link rel="stylesheet" href="css/manageinvoice.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
    <?php include 'include/navbar.php' ?>
    <div class="container_invoice">
        <div class="title">
            <h3>Invoices</h3>
            <div class="result">
                <table>
                    <thead>
                        <td>Invoice #</td>
                        <td>Invoice Date</td>
                        <td>Due Date</td>
                        <td>Amount</td>
                        <td>Status</td>
                    </thead>
                    <tbody>
                        <?php
                            $sql=$con->prepare('SELECT InvoiceID,InvoiceDate,TotalAmount,TotalTax,StatusInvoice,StatusInvoiceColor
                                                FROM tblinvoice
                                                INNER JOIN tblstatusinvoice ON tblstatusinvoice.StatusInvoiceID = tblinvoice.Invoice_Status
                                                WHERE ClientID = ?
                                                ORDER BY Invoice_Status');
                            $sql->execute(array($clientId));
                            $rows=$sql->fetchAll();
                            foreach($rows as $row){
                                $expirationDate = date('Y-m-d', strtotime($row['InvoiceDate'] . ' + ' . 30 . ' days'));
                                $totalamount= $row['TotalAmount'] + $row['TotalTax'];
                                echo '
                                    <tr class="gotoinvoice" data-index="'.$row['InvoiceID'].'">
                                        <td>'.$row['InvoiceID'].'</td>
                                        <td>'.$row['InvoiceDate'].'</td>
                                        <td>'.$expirationDate.'</td>
                                        <td>'.number_format($totalamount,2,'.','').' $</td>
                                        <td style="color:'.$row['StatusInvoiceColor'].'">'.$row['StatusInvoice'].'</td>
                                    </tr>
                                ';
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include '../common/jslinks.php' ?>
    <script src="js/manageinvoice.js"></script>
    <script src="js/navbar.js"></script>
</body>