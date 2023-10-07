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

    $sql=$con->prepare('SELECT Client_FName,Client_LName,Client_companyName,Client_addresse,Client_city,Client_zipcode,client_active,Client_country FROM  tblclients WHERE ClientID=?');
    $sql->execute(array($clientId));
    $clientinfo = $sql->fetch();
    $clientName = $clientinfo['Client_FName'].' '. $clientinfo['Client_LName'];

    if($clientinfo['client_active'] == 0){
        setcookie("user","",time()-3600);
        unset($_SESSION['user']);
        echo '<script> location.href="index.php" </script>';
    }

    $sql=$con->prepare('SELECT CountryTVA FROM tblcountrys WHERE CountryID = ?');
    $sql->execute(array($clientinfo['Client_country']));
    $checkusercountry=$sql->rowCount();
    if($checkusercountry==1){
        $resulttva=$sql->fetch();
        $tva = $resulttva['CountryTVA'];
    }else{
        $tva = 0;
    }

    $invoiceID= isset($_GET['id'])?$_GET['id']:0;
    $checkinvoiceID = checkItem('InvoiceID','tblinvoice',$invoiceID);

    if($checkinvoiceID ==0){
        echo '<script> location.href="index.php" </script>';
    }else{
        $sql=$con->prepare('SELECT InvoiceID,InvoiceDate,ClientID,TotalAmount,TotalTax,StatusInvoice,StatusInvoiceColor,Invoice_Status
                            FROM  tblinvoice 
                            INNER JOIN tblstatusinvoice ON tblstatusinvoice.StatusInvoiceID = tblinvoice.Invoice_Status
                            WHERE InvoiceID = ?');
        $sql->execute(array($invoiceID));
        $invoiceinfo=$sql->fetch();

        if($invoiceinfo['Invoice_Status'] == 1){
            $displaypay='block';
        }else{
            $displaypay='none';
        }
        if($invoiceinfo['ClientID'] != $clientId){
            echo '<script> location.href="index.php" </script>';
        }
    }

    $sql = $con->prepare('SELECT paymentmethodD, methot FROM tblpayment_method WHERE method_active = 1');
    $sql->execute();
    $methodspay = $sql->fetchAll();
?>
    <link rel="stylesheet" href="css/viewinvoice.css">
    <link rel="stylesheet" type="text/css" href="css/print-styles.css" media="print">
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>

</head>
<body>
    <div class="conteinerinvoice" id="contentToConvert">
        <div class="title">
            <div class="companyInfo">
                <h3>YK-Technology</h3>
                <label for="">Y9954331Z</label>
                <label for="">C/ SANT GERMA 6 </label>
                <label for="">Barcelona 08004</label>
                <label for="">www.ykinnovate.com</label>
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
                <h4>BILL TO :</h4>
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
                                    <td>'.number_format($row['UnitPrice'],2,'.','').' $</td>
                                </tr>
                            ';
                        }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td rowspan="3">
                            <div id="qrcode"></div>
                        </td>
                        <td  class="invoiceamounts"><label for="">SUB TOTAL</label></td>
                        <td class="amountfinish"><span><?php echo number_format($invoiceinfo['TotalAmount'],2,'.','').' $' ?></span></td>
                    </tr>
                    <tr>
                        <td  class="invoiceamounts"><label for="">Tax (<?php echo number_format($tva,2,'.','') ?>%)</label></td>
                        <td class="amountfinish"><span><?php echo number_format($invoiceinfo['TotalTax'],2,'.','').' $' ?></span></td>
                    </tr>
                    <tr>
                        <td  class="invoiceamounts"><label for="">GRAND TOTAL</label></td>
                        <td class="amountfinish"><span><?php echo number_format($invoiceinfo['TotalAmount'] + $invoiceinfo['TotalTax'] ,2,'.','').' $' ?></span></td>
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
                    <?php
                        $sql=$con->prepare('SELECT Payment_Date,NoofDocument,Payment_Amount,methot
                                            FROM tblpayments
                                            INNER JOIN  tblpayment_method ON  tblpayment_method.paymentmethodD= tblpayments.paymentMethod
                                            WHERE invoiceID = ?');
                        $sql->execute(array($invoiceID));
                        $checkpaymentcount= $sql->rowCount();
                        if($checkpaymentcount > 0){
                            $rows= $sql->fetchAll();
                            $total_due = 0;
                            foreach($rows as $row){
                                $total_due += $row['Payment_Amount'];
                                echo '
                                    <tr>
                                        <td>'.$row['Payment_Date'].'</td>
                                        <td>'.$row['methot'].'</td>
                                        <td>'.$row['NoofDocument'].'</td>
                                        <td>'.number_format($row['Payment_Amount'] ,2,'.','').' $'.'</td>
                                    </tr>
                                ';
                            }
                        }else{
                            $total_due = 0;
                            echo '
                                <tr>
                                    <td colspan="4"> There are no previous deposits</td>
                                </tr>
                            ';
                        }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="totalpayment" colspan="3"><label for="">Total due: </label></td>
                        <td><span><?php echo number_format($total_due ,2,'.','').' $' ?></span></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="pay" style='display:<?php echo  $displaypay ?>'>
            <button id="btnpaydeiteil"> Pay Now </button>
        </div>
        <label for="" style="font-weight:bold;">Print Date : <?php echo date('d/m/Y') ?></label>
    </div>
    <div class="popuppayment">
        <div class="containerpayment">
            <div class="closePayment">+</div>
            <div class="page1">
                <div class="titlepayment">
                    <h3>Payment Plan Established</h3>
                </div>
                <div class="disription">
                    <?php
                        $invoiceAmount = $invoiceinfo['TotalAmount'] + $invoiceinfo['TotalTax'];

                        $sql=$con->prepare('SELECT COALESCE(SUM(Payment_Amount), 0) AS TotalAmount
                                            FROM tblpayments
                                            WHERE invoiceID = ?;');
                        $sql->execute(array($invoiceID));
                        $result=$sql->fetch();

                        $amountPaid = $result['TotalAmount'];
                        $paymentDetails = calculatePaymentDetails($invoiceAmount,$amountPaid);
                    ?>
                    <p>This invoice will be divided into <?php echo $paymentDetails['numberOfPayments']?> equal payments as outlined below:</p>
                </div>
                <div class="pay_done">
                    <?php
                        for ($i = 1; $i <= $paymentDetails['numberOfPayments'] - 1; $i++) {
                            echo '<div class="payment_dis">';
                            if ($i <= $paymentDetails['paymentsMade']) {
                                echo "<span class='number_payment paid'> $i </span> <h4>" . number_format($paymentDetails['paymentAmount'], 2) . " $</h4> (Paid)<br>";
                            } else {
                                echo "<span class='number_payment no_paid'> $i </span> <h4 class='amountpayment'>" . number_format($paymentDetails['paymentAmount'], 2) . " $</h4><br>";
                            }
                            echo '</div>';
                        }
                        
                        $i = $paymentDetails['numberOfPayments'];
                        $lastPaymentAmount = $paymentDetails['paymentAmount'] - $paymentDetails['overpayment'];
                        echo '<div class="payment_dis">';
                        if ($i <= $paymentDetails['paymentsMade']) {
                            echo "<span class='number_payment paid'> $i </span>x <h4>" . number_format($lastPaymentAmount, 2) . " $</h4> (Paid)<br>";
                        } else {
                            echo "<span class='number_payment no_paid'> $i  </span> <h4 class='amountpayment'>" . number_format($lastPaymentAmount, 2) . " $</h4><br>";
                        }
                        echo '</div>';
                    ?>
                </div>
                <div class="gonext">
                    <button id="gotopage2">Next</button>
                </div>
            </div>
            <div class="page2">
                <div class="titlepayment">
                    <h3>Payment Method</h3>
                </div>
                <div class="disription">
                    <p>What method of payment would you prefer?</p>
                </div>
                <div class="payments">
                    <select name="" id="txtpaymentmethod">
                        <?php
                            foreach ($methodspay as $type) {
                                echo '<option value="' . $type['paymentmethodD'] . '">' . $type['methot'] . '</option>';
                            }
                        ?>
                    </select>
                </div>
                <div class="note_payment">
                    <p id="textnote"></p>
                </div>
                <div class="amout_to_pay">
                    <h3>Amount To Pay: <span id="Nextamount"></span>$</h3>
                </div>
                <div class="conclution">
                </div>
            </div>
        </div>
    </div>
    <?php include '../common/jslinks.php' ?>
    <script src="https://www.paypal.com/sdk/js?client-id=AesUzW12lpAZ-DmxpH5WPJqADzBR7ws6dtOP4Qd8UvExBXFr0lRt4SAswocUVy7d31FpyLBeE19Jh7yd&disable-funding=credit,card,sofort&locale=es_ES&currency=USD" data-sdk-integration-source="button-factory"></script>
    <script src="js/viewinvoice.js"></script>
    <?php
        /*  AesUzW12lpAZ-DmxpH5WPJqADzBR7ws6dtOP4Qd8UvExBXFr0lRt4SAswocUVy7d31FpyLBeE19Jh7yd  real*/
        /* AY-CMfLiUS2VuombfG2u83bOq4fqNetZg9qor6flvV5kpgKxMDgAlGe2PNWUX-wKe6XVsuxs6Fzz6_sa sandbox*/
    ?>
    <script>
        function generateQRCode(link) {
            var qrcode = new QRCode(document.getElementById("qrcode"), {
                text: link,
                width: 128,
                height: 128
            });
        }
        var link = window.location.href; 
        generateQRCode(link);

    </script>

</body>