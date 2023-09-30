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

    $sql=$con->prepare('SELECT Client_FName,Client_LName,Client_companyName,Client_addresse,Client_city,Client_zipcode,client_active FROM  tblclients WHERE ClientID=?');
    $sql->execute(array($clientId));
    $clientinfo = $sql->fetch();
    $clientName = $clientinfo['Client_FName'].' '. $clientinfo['Client_LName'];

    if($clientinfo['client_active'] == 0){
        setcookie("user","",time()-3600);
        unset($_SESSION['user']);
        echo '<script> location.href="index.php" </script>';
    }

    $invoiceID= isset($_GET['id'])?$_GET['id']:0;
    $checkinvoiceID = checkItem('InvoiceID','tblinvoice',$invoiceID);

    if($checkinvoiceID ==0){
        echo '<script> location.href="index.php" </script>';
    }else{
        $sql=$con->prepare('SELECT InvoiceID,InvoiceDate,ClientID,TotalAmount,TotalTax,StatusInvoice,StatusInvoiceColor
                            FROM  tblinvoice 
                            INNER JOIN tblstatusinvoice ON tblstatusinvoice.StatusInvoiceID = tblinvoice.Invoice_Status
                            WHERE InvoiceID = ?');
        $sql->execute(array($invoiceID));
        $invoiceinfo=$sql->fetch();

        if($invoiceinfo['ClientID'] != $clientId){
            echo '<script> location.href="index.php" </script>';
        }
    }
?>
    <link rel="stylesheet" href="css/viewinvoice.css">
    <link rel="stylesheet" type="text/css" href="css/print-styles.css" media="print">

</head>
<body>
    <div class="conteinerinvoice" id="contentToConvert">
        <div class="title">
            <div class="companyInfo">
                <h3>YK-Technology</h3>
                <label for="">C/ SANT GERMA 6 </label>
                <label for="">Barcelona 08004</label>
                <label for="">www.yktechnology.es</label>
            </div>
            <div class="invoiceTitle">
                <h2>INVOICE</h2>
            </div>
        </div>
        <div class="status_invoice">
            <div class="status">
                <h3 style="color:<?php echo $invoiceinfo['StatusInvoiceColor'] ?>"><?php echo $invoiceinfo['StatusInvoice'] ?></h3>
            </div>
            <div class="info_invoice">
                <label for="">Invoice No : <span> <?php echo $invoiceinfo['InvoiceID'] ?></span></label>
                <label for=""> - </label>
                <label for="">Date : <span><?php echo $invoiceinfo['InvoiceDate'] ?></span></span></label>
            </div>
        </div>
        <div class="customerInfo">
            <div class="customer_title">
                <h4>BILL TO</h4>
            </div>
            <div class="customerdeiteil">
                <label for=""><?php echo  $clientName ?></label>
                <label for=""><?php echo  $clientinfo['Client_companyName'] ?></label>
                <label for=""><?php echo  $clientinfo['Client_addresse'] ?></label>
                <label for=""><?php echo  $clientinfo['Client_city'].'/'.$clientinfo['Client_zipcode'] ?></label>
            </div>
        </div>
        <div class="deietil_invoice">
            <table>
                <thead>
                    <td>SERVICE ID</td>
                    <td>DESCRIPTION</td>
                    <td>PRICE</td>
                </thead>
                <tbody>
                    <?php
                        $sql=$con->prepare('SELECT Description,UnitPrice,ServiceID,Service_Name
                                            FROM  tbldetailinvoice 
                                            INNER JOIN  tblservices ON  tblservices.ServiceID = tbldetailinvoice.Service
                                            WHERE Invoice = ?');
                        $sql->execute(array($invoiceID));
                        $rows = $sql->fetchAll();
                        foreach($rows as $row){
                            echo '
                                <tr>
                                    <td>'.$row['ServiceID'].'</td>
                                    <td>'.$row['Service_Name'].'('.$row['Description'].')</td>
                                    <td>'.number_format($row['UnitPrice'],2,'.','').'$</td>
                                </tr>
                            ';
                        }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="invoiceamounts"><label for="">SUB TOTAL</label></td>
                        <td class="amountfinish"><span><?php echo number_format($invoiceinfo['TotalAmount'],2,'.','').'$' ?></span></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="invoiceamounts"><label for="">Tax (21%)</label></td>
                        <td class="amountfinish"><span><?php echo number_format($invoiceinfo['TotalTax'],2,'.','').'$' ?></span></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="invoiceamounts"><label for="">GRAND TOTAL</label></td>
                        <td class="amountfinish"><span><?php echo number_format($invoiceinfo['TotalAmount'] + $invoiceinfo['TotalTax'] ,2,'.','').'$' ?></span></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="paymenttable">
            <table>
                <thead>
                    <td>Deposit Date</td>
                    <td>Payment Methot</td>
                    <td>Operation Number</td>
                    <td>Deposit Amount</td>
                </thead>
                <tbody>
                    <tr>
                        <td>25/10/2023</td>
                        <td>Pay pal</td>
                        <td>521451</td>
                        <td>25.25 $</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php include '../common/jslinks.php' ?>
    <script src="js/viewinvoice.js"></script>
    

</body>