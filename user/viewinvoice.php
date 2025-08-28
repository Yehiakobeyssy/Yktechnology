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
    <script src="https://gateway.sumup.com/gateway/ecom/card/v2/sdk.js"></script>

</head>
<body>
    <div class="conteinerinvoice" id="contentToConvert">
        <div class="title">
            <div class="companyInfo">
                <h3>Kawnex</h3>
                <?php
                    $sql=$con->prepare('SELECT tax_number,addresse,zip_code,region,website FROM tblsetting WHERE SettingID=1');
                    $sql->execute();
                    $info=$sql->fetch();

                ?>
                <label for=""><?php echo $info['tax_number'] ?></label>
                <label for=""><?php echo $info['addresse'] ?></label>
                <label for=""><?php echo $info['zip_code'].' - '. $info['region'] ?></label>
                <label for=""><?php echo $info['website'] ?></label>
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
            <?php  $grandTotal = $invoiceinfo['TotalAmount'] + $invoiceinfo['TotalTax'] ?>
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

        
        <label for="" style="font-weight:bold;">Print Date : <?php echo date('d/m/Y') ?></label>
        <?php 
            //Calacute Payment 
            $invoiceAmount = $invoiceinfo['TotalAmount'] + $invoiceinfo['TotalTax'];

            $sql=$con->prepare('SELECT COALESCE(SUM(Payment_Amount), 0) AS TotalAmount
                                FROM tblpayments
                                WHERE invoiceID = ?;');
            $sql->execute(array($invoiceID));
            $result=$sql->fetch();

            $amountPaid = $result['TotalAmount'];
            $paymentDetails = calculatePaymentDetails($invoiceAmount,$amountPaid);
            
            $Amounttopay=$paymentDetails['paymentAmount']
        ?>
        <?php
            if($paymentDetails['remainingPayments'] == 0){
                $displaypements = 'none';
            }else{
                $displaypements = 'block';
            }
        ?>
        <table id="dlpay" style="display:<?php echo $displaypements ?>">
            <tr>
                <th>Payment </th>
                <td><?php echo $paymentDetails['paymentsMade']+1  .' of '.$paymentDetails['numberOfPayments']  ?></td>
            </tr>
            <tr>
                <th>Amount Due </th>
                <td id="thisAmount"><?php echo number_format($paymentDetails['paymentAmount'],2)  ?></td>
            </tr>
            <tr>
                <th>Remaining After This Payment </th>
                <td><?php echo number_format($grandTotal - $paymentDetails['paymentAmount']   ,2)  ?></td>
            </tr>
        </table>
        
        <div class="paymentTyps" style="display:<?php echo $displaypements ?>">
            <h3>Payment Method </h3>
            
            <?php
                $sql=$con->prepare('SELECT paymentmethodD,methot,note FROM tblpayment_method WHERE paymentmethodD != 3 AND method_active = 1');
                $sql->execute();
                $payments=$sql->fetchAll();

                foreach($payments as $payment){

                    if($payment['paymentmethodD'] == 1){
                        echo '
                        <div class="card_pay">
                            <div class="title_pay">
                                <h3>'.$payment['methot'].'</h3>
                            </div>
                            <div class="dis">
                                <label for="">'.$payment['note'].'</label>
                                <div id="paypal-button-container"></div>
                            </div>
                        </div>
                    ';
                    }elseif($payment['paymentmethodD'] == 2){
                        echo '
                        <div class="card_pay">
                            <div class="title_pay">
                                <h3>'.$payment['methot'].'</h3>
                            </div>
                            <div class="dis">
                                <label for="">'.$payment['note'].'</label>
                                <div id="sumup-card" class="sumupcontainer"></div>
                            </div>
                        </div>
                    ';
                    $checkout_id = create_checkout($Amounttopay,'Payment for Invoice no.'. $invoiceID , $invoiceID);
                    }else{
                        echo '
                        <div class="card_pay">
                            <div class="title_pay">
                                <h3>'.$payment['methot'].'</h3>
                            </div>
                            <div class="dis">
                                <label for="">'.$payment['note'].'</label>
                            </div>
                        </div>
                    ';
                    }
                    
                }
            ?>
            
        </div>
    </div>

    <?php include '../common/jslinks.php' ?>
    <?php
        $sql=$con->prepare('SELECT key_payPal FROM  tblsetting WHERE SettingID =1');
        $sql->execute();
        $paypalresult=$sql->fetch();
        $paypalKey=$paypalresult['key_payPal'];
    ?>
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $paypalKey?>&disable-funding=credit,card,sofort&locale=es_ES&currency=EUR" data-sdk-integration-source="button-factory"></script>
    <script src="js/viewinvoice.js"></script>
    <?php
        /*  AesUzW12lpAZ-DmxpH5WPJqADzBR7ws6dtOP4Qd8UvExBXFr0lRt4SAswocUVy7d31FpyLBeE19Jh7yd  real*/
        /* AY-CMfLiUS2VuombfG2u83bOq4fqNetZg9qor6flvV5kpgKxMDgAlGe2PNWUX-wKe6XVsuxs6Fzz6_sa sandbox*/
        // <?php echo $paypalKey
    ?>
    <script>
        //let amount_pay = 
        let amountText = document.getElementById("thisAmount").innerText;
        let amountInvoice = parseFloat(amountText).toFixed(2);

        var url = window.location.href;
        var urlParams = new URLSearchParams(url.split('?')[1]); // Extract the query parameters from the URL
        var invid = urlParams.get('id');
        function generateQRCode(link) {
            var qrcode = new QRCode(document.getElementById("qrcode"), {
                text: link,
                width: 128,
                height: 128
            });
        }
        var link = window.location.href; 
        generateQRCode(link);

        try{
            paypal.Buttons({
                createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                    amount: {
                        value: amountInvoice
                    }
                    }],
                    application_context: {
                    shipping_preference: 'NO_SHIPPING'
                    }
                });
                },
                onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    let tras=details.id
                    location.href = "paymentpaypal.php?id="+tras+"&invoiceID="+invid+"&amountpay="+ encodeURIComponent(amountInvoice);
                });
                }
            }).render('#paypal-button-container');
        } catch (e) {
            console.warn("PayPal Button failed to render or initialize:", e);
            // Continue with other scripts or show a fallback UI
        }
        

        try{
            const checkoutId = '<?php echo $checkout_id; ?>';
        
            SumUpCard.mount({
                id: 'sumup-card',
                checkoutId: checkoutId,
                currency: 'EUR',
                onResponse: function (type, data) {
                    switch (type) {
                        case 'sent':
                            console.log('Form sent to the server for processing.');
                            console.log('Card details:', data);
                            break;
                        case 'invalid':
                            console.error('Form has validation errors.');
                            break;
                        case 'auth-screen':
                            console.log('User is prompted to authenticate the payment.');
                            break;
                        case 'error':
                            console.error('Server responded with an error:', data);
                            break;
                        case 'success':
                            console.log('Payment successful. Response:', data);
                            tras=data.id;
                            location.href = "paymentCard.php?id="+tras+"&invoiceID="+invid+"&amountpay="+ encodeURIComponent(amountInvoice);
                            break;
                        case 'fail':
                            console.error('Payment failed:', data);
                            break;
                        default:
                            console.warn('Unknown status:', type);
                    }
                    console.log('Type:', type);
                    console.log('Body:', data);
                }
            });
        }catch(e){
            console.warn("Sumup Button failed to render or initialize:", e);
        }
        
    </script>

</body>