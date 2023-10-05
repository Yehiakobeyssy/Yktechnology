<?php

    include '../settings/connect.php';
    $invoiceID = (isset($_GET['invoiceID']))?$_GET['invoiceID']:0;
    $amount = (isset($_GET['amountpay']))?$_GET['amountpay']:0;
    $transacction = (isset($_GET['id']))?$_GET['id']:0;

    //get invoice Amount 
    $sql = $con->prepare('SELECT TotalAmount, TotalTax,ClientID FROM tblinvoice WHERE InvoiceID = ?');
    $sql->execute(array($invoiceID));
    $result_totalAmount = $sql->fetch();
    $invoiceAmount = $result_totalAmount['TotalAmount'] + $result_totalAmount['TotalTax'];
    $clientID = $result_totalAmount['ClientID'];


    //insert to payment 
    $ClientID       = $clientID;
    $invoiceID      = $invoiceID;
    $paymentMethod  = 1;
    $NoofDocument   = $transacction;
    $Payment_Amount = $amount;
    $Payment_Date   = date('Y-m-d');

    $sql=$con->prepare('INSERT INTO  tblpayments (ClientID,invoiceID,paymentMethod,NoofDocument,Payment_Amount,Payment_Date)
                        VALUES (:ClientID,:invoiceID,:paymentMethod,:NoofDocument,:Payment_Amount,:Payment_Date)');
    $sql->execute(array(
        'ClientID'          => $ClientID,
        'invoiceID'         => $invoiceID,
        'paymentMethod'     => $paymentMethod,
        'NoofDocument'      => $NoofDocument,
        'Payment_Amount'    => $Payment_Amount,
        'Payment_Date'      => $Payment_Date
    ));

    $comition =calculateCommission($invoiceID,$Payment_Amount,$con);
    //send email for payment conformation

    $sql=$con->prepare('SELECT Client_FName,Client_LName,Client_email FROM tblclients WHERE ClientID= ?');
    $sql->execute(array($ClientID));
    $resultclient = $sql->fetch();

    $client_Name = $resultclient['Client_FName'].' '.$resultclient['Client_LName'];
    $clientEmail = $resultclient['Client_email'];

    require_once '../mail.php';
    $mail->setFrom($applicationemail, 'YK technology');
    $mail->addAddress($clientEmail);
    $mail->Subject = 'Confirmation of Successful Payment';
    $mail->Body    = '
                        Dear '.$client_Name.'<br>
                        I hope this message finds you well. I am writing to inform you that your 
                        recent payment has been successfully processed, and we have received the funds.
                        We greatly appreciate your prompt payment, and it helps us to continue providing
                        you with our services/products without interruption.<br>
                        Here are the details of the payment: <br>
                        Invoice/Reference Number: '.$invoiceID.' <br>
                        Payment Amount: '.$amount.' $ <br>
                        Payment Date: '.$Payment_Date.' <br>
                        Payment Method: PayPal <br>
                        If you have any questions or concerns regarding this payment or any other matter 
                        related to our services/products, please feel free to contact our customer support
                        team at info@yktechnology.es. We are here to assist you with any inquiries you may
                        have.<br>
                        Once again, thank you for your timely payment. We value your business and look 
                        forward to serving you in the future. If you require any further documentation or 
                        receipts for your records, please dont hesitate to let us know, and we will be 
                        happy to provide them.<br>
                        Best regards,
    ';
    $mail->send();

        //get the custome Payment of this invoice 
        $sql=$con->prepare('SELECT COALESCE(SUM(Payment_Amount), 0) AS TotalAmount
                            FROM tblpayments
                            WHERE invoiceID = ?;');
        $sql->execute(array($invoiceID));
        $result=$sql->fetch();
        $amountPaid = $result['TotalAmount'];

        if($invoiceAmount <= round($amountPaid,2) ){
            $invoicePaid = 1 ; 
        }else{
            $invoicePaid = 0 ;  
        }

    if($invoicePaid == 1){
        $sql=$con->prepare('UPDATE  tblinvoice SET Invoice_Status = 2 WHERE InvoiceID = ?');
        $sql->execute(array($invoiceID));

        $mail->setFrom($applicationemail, 'YK technology');
        $mail->addAddress($clientEmail);
        $mail->Subject = 'Confirmation of Full Payment for Invoice '.$invoiceID;
        $mail->Body    = '
                            Dear '.$client_Name.'<br>
                            I hope this email finds you well. I am writing to confirm that we have received your payment for Invoice 
                            '.$invoiceID.', and it has been fully settled. <br>
                            We would like to express our sincere appreciation for your prompt payment. Your commitment to fulfilling your 
                            financial obligations is greatly valued, and it helps us maintain the smooth operation of our business.<br>
                            If you have any questions or need further clarification regarding this payment, please do not hesitate to contact 
                            our accounts department at info@yktechnology.es.<br>
                            Once again, thank you for your prompt attention to this matter, and we look forward to continuing our business relationship.<br>
                            Best regards,
                        ';
        $mail->send();
    }

//add to the saleman 
    if($comition['SalemanID'] > 0){
        $sql=$con->prepare('SELECT email_Sale,Sale_FName,Sale_LName FROM tblsalesperson WHERE SalePersonID =?');
        $sql->execute(array($comition['SalemanID']));
        $result=$sql->fetch();
        $saleMan_email = $result['email_Sale'];
        $saleMan_name = $result['Sale_FName'].' '.$result['Sale_LName'];
    
        $Account_Date = date('Y-m-d');
        $agent        = $comition['SalemanID'];
        $Discription  = 'commission from invoice no '. $invoiceID . '( '. $client_Name .' )';
        $Depit        = number_format($comition['commission'], 2);
        $Crieted      = 0;
                                        
        $sql=$con->prepare('INSERT INTO tblaccountstatment_saleperson (Account_Date,SaleManID,Discription,Depit,Crieted) 
                            VALUES (:Account_Date,:SaleManID,:Discription,:Depit,:Crieted)');
        $sql->execute(array(
                        'Account_Date'  =>$Account_Date,
                        'SaleManID'     =>$agent,
                        'Discription'   =>$Discription,
                        'Depit'         =>$Depit ,
                        'Crieted'       =>$Crieted
                    ));
    
        $mail->setFrom($applicationemail, 'YK technology');
        $mail->addAddress($saleMan_email);
        $mail->Subject = 'Commission Notification';
        $mail->Body    = '
                            Dear '.$saleMan_name.',<br>
                            We are pleased to inform you that you have received a commission of 
                            $'.$Depit .' for your recent sales efforts. Your hard work and dedication are 
                            greatly appreciated, and we want to acknowledge your contribution to our teams success.<br>
                            <strong>Commission Amount: $'.$Depit .'</strong> <br>
                            Thank you for your outstanding performance and commitment to achieving our sales goals.
                            We look forward to continued success together.<br>
                            If you have any questions or need further information, 
                            please dont hesitate to reach out to us.<br>
                            Best regards,
                        ';
        $mail->send();
    }
    
    include '../common/head.php';
?>
    <style>
        body{
             background-color: #1c4e80; 
        }
    </style>
</head>
<body>
    <script>
       window.onload = function() {
            window.location.href = "dashboard.php";
        };
    </script>
</body>