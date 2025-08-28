<?php
    session_start();

    if(!isset($_COOKIE['useradmin'])){
        if(!isset($_SESSION['useradmin'])){
            header('location:index.php');
        }
    }
    $adminId= (isset($_COOKIE['useradmin']))?$_COOKIE['useradmin']:$_SESSION['useradmin'];

    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';

    $sql=$con->prepare('SELECT admin_active,admin_FName,admin_LName FROM  tbladmin WHERE admin_ID=?');
    $sql->execute(array($adminId));
    $result=$sql->fetch();
    $isActive=$result['admin_active'];
    $firstname= $result['admin_FName'];
    $lastName = $result['admin_LName'];
    $full_name = $firstname .' ' . $lastName ;

    if($isActive == 0){
        setcookie("useradmin","",time()-3600);
        unset($_SESSION['useradmin']);
        echo '<script> location.href="index.php" </script>';
    }
?>
    <link rel="stylesheet" href="css/activationPOS.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h2>Activate Programs</h2>
            </div>
            <form action="" method="post">
                <label for="userName">User Name:</label>
                <select id="userName" name="userName" required>
                    <option value="">Select User</option>
                    <?php
                        $sql=$con->prepare('SELECT ClientID,Client_FName,Client_LName,Client_email FROM tblclients WHERE client_active = 1 ORDER BY Client_FName');
                        $sql->execute();
                        $result=$sql->fetchAll();
                        foreach($result as $client){
                            echo '<option value="'.$client['ClientID'].'">'.$client['Client_FName'].' '.$client['Client_LName'].' ( '.$client['Client_email'].' )</option>';
                        }
                    ?>
                </select>

                <label for="posType">POS Type:</label>
                <select id="posType" name="posType" required onchange="fetchDurations()">
                    <option value="">Select POS</option>
                    <?php
                        $sql=$con->prepare('SELECT POSID,POSName FROM tbllposactivationcode ');
                        $sql->execute();
                        $sytems = $sql->fetchAll();
                        foreach($sytems as $pos){
                            echo '<option value="'.$pos['POSID'].'">'.$pos['POSName'].'</option>';
                        }
                    ?>
                </select>

                <label for="duration">Duration:</label>
                <select id="duration" name="duration" required>
                </select>

                <label for="pin">PIN:</label>
                <input type="text" id="pin" name="pin" pattern="\d{4}" title="Please enter a 4-digit PIN" required>

                <label for="">Payment Method</label>
                <select name="paymentmethod" id="">
                    <option value="">SELECT Payment Method</option>
                    <?php 
                        $sql=$con->prepare('SELECT paymentmethodD,methot FROM tblpayment_method WHERE method_active=1');
                        $sql->execute();
                        $payments = $sql->fetchAll();
                        foreach($payments as $pay){
                            echo '<option value="'.$pay['paymentmethodD'].'">'.$pay['methot'].'</option>';
                        }
                    ?>
                </select>
                <button type="submit" name="btnGeratePIN">Generate Activation Code</button>
            </form>
            <?php 
                if(isset($_POST['btnGeratePIN'])){
                    $client = $_POST['userName'];
                    $pos = $_POST['posType'];
                    $duration = $_POST['duration'];
                    $pin = $_POST['pin'];
                    $pay = $_POST['paymentmethod'];

                    $sql=$con->prepare('SELECT POSNumber,POSCode FROM  tbllposactivationcode WHERE POSID =?');
                    $sql->execute(array($pos));
                    $res_pos =$sql->fetch();
                    $pos_number = $res_pos['POSNumber'];
                    $pos_code = $res_pos['POSCode'];

                    $sql=$con->prepare('SELECT Code FROM tblcodetime WHERE CodeID = ?');
                    $sql->execute(array($duration));
                    $res_duration = $sql->fetch();
                    $duration_code = $res_duration['Code'];

                    $usercode = intval(($pos_number * $pin)/$pos_code);
                    $newcode = sha1($usercode);
                    $activationcode = $duration_code.$newcode;

                    echo '
                        <div class="alert alert-info" role="alert" style="margin: 10px !important;text-align:center;padding:10px !important;font-size:18px;">
                            '.$activationcode.'
                        </div>
                    ';


                    $sql=$con->prepare('SELECT CodeID,Time,Service_Price,tblcodetime.ServiceID FROM  tblcodetime INNER JOIN  tblservices ON  tblservices.ServiceID = tblcodetime.ServiceID WHERE CodeID=?');
                    $sql->execute(array($duration));
                    $res_Price = $sql->fetch();
                    
                    $InvoiceDate = date('Y-m-d');
                    $InvoiceTime = date("H:i:s");
                    $ClientID = $client ; 
                    $TotalAmount = $res_Price['Service_Price'];
                    $TotalTax = 0;
                    $Invoice_Status = 2;

                    $sqlInsertInvoice = $con->prepare('INSERT INTO tblinvoice (InvoiceDate, InvoiceTime, ClientID, TotalAmount, TotalTax, Invoice_Status) VALUES (?, ?, ?, ?, ?, ?)');
                    $sqlInsertInvoice->execute([$InvoiceDate, $InvoiceTime, $ClientID, $TotalAmount, $TotalTax, $Invoice_Status]);
                    $NewInvoiceID = $con->lastInsertId();

                    $Invoice = $NewInvoiceID;
                    $Service = $res_Price['ServiceID'];
                    $Description = $res_Price['Time'];
                    $UnitPrice = $res_Price['Service_Price'];
                    $ClientServiceID = 1;
                
                    $sqlInsertDetailInvoice = $con->prepare('INSERT INTO tbldetailinvoice (Invoice, Service, Description, UnitPrice, ClientServiceID) VALUES (?, ?, ?, ?, ?)');
                    $sqlInsertDetailInvoice->execute([$Invoice, $Service, $Description, $UnitPrice, $ClientServiceID]);

                    $stat=$con->prepare('SELECT Client_FName,Client_LName,Client_email FROM tblclients WHERE ClientID =?');
                    $stat->execute(array($client));
                    $res_email = $stat ->fetch();
                    $email_client = $res_email['Client_email'];
                    $clientName = $res_email['Client_FName'] .' '. $res_email['Client_LName'];

                    


                    $sql=$con->prepare('INSERT INTO  tblpayments (ClientID,invoiceID,paymentMethod,NoofDocument,Payment_Amount,Payment_Date)
                                        VALUES (:ClientID,:invoiceID,:paymentMethod,:NoofDocument,:Payment_Amount,:Payment_Date)');
                    $sql->execute(array(
                        'ClientID'          => $client,
                        'invoiceID'         => $Invoice,
                        'paymentMethod'     => $pay,
                        'NoofDocument'      => 'POS Active',
                        'Payment_Amount'    => $TotalAmount ,
                        'Payment_Date'      => date('Y-m-d')
                    ));

                    require_once '../mail.php';

                    $mail->setFrom($applicationemail, 'Kawnex');
                    $mail->addAddress($email_client );
                    $mail->Subject = 'Invoice for Service - Invoice #'.$Invoice.'';
                    $mail->Body    = '
                                        Dear '.$clientName.',<br>
                                        I hope this email finds you well. I want to express my gratitude for choosing Kawnex for your Service needs. As part of our commitment to transparency and efficient communication, we are sending you the invoice for the Service provided to you.<br>
                                        Below are the details of your invoice:<br>
            
                                        Invoice Number: #'.$Invoice.'<br>
                                        Invoice Date: '.$InvoiceDate.'<br>
                                        Amount Due: $'.$TotalAmount .'<br>
                                        Please review the invoice . You can find the  invoice  on the following link. <br>
                                        <a href="'.$websiteaddresse.'user/viewinvoice.php?id='.$Invoice.'">view Invoice </a>
                                        If you have any questions or concerns regarding this invoice or need any additional information, please do not hesitate to reach out to our dedicated support team at info@kawnex.com.<br>
                                        We kindly request that you make the payment by the due date to ensure there are no disruptions to your Service. Your prompt attention to this matter is greatly appreciated.<br>
                                        Thank you once again for choosing Kawnex. We value your business and are committed to providing you with the best Service experience.<br>
                                        Best Regards,
                                    ';
                    $mail->send();

                    $mail->setFrom($applicationemail, 'Kawnex');
                    $mail->addAddress($email_client);
                    $mail->Subject = 'POS YK-Live Activation Code - Kawnex';
                    $mail->Body    = '
                                        Dear '.$clientName.'<br>
                                        I trust this email finds you well. We appreciate your business and are excited to provide you with the activation code for POS YK-Live.<br>
                                        Activation Code: <br>
                                        <h1>'.$activationcode.'</h1>
                                        If you encounter any issues during the activation process, feel free to reach out to our support team at info@kawnex.com.<br>
                                        Thank you for choosing POS YK-Live. We look forward to assisting you and ensuring a smooth experience with our system. 
                    ';
                    $mail->send();


                    $comition =calculateCommission($Invoice,$TotalAmount,$con);
                    if($comition['SalemanID'] > 0){
                        $sql=$con->prepare('SELECT email_Sale,Sale_FName,Sale_LName FROM tblsalesperson WHERE SalePersonID =?');
                        $sql->execute(array($comition['SalemanID']));
                        $result=$sql->fetch();
                        $saleMan_email = $result['email_Sale'];
                        $saleMan_name = $result['Sale_FName'].' '.$result['Sale_LName'];

                        $Account_Date = date('Y-m-d');
                        $agent        = $comition['SalemanID'];
                        $Discription  = 'commission from invoice no '. $Invoice . '( '.$clientName .' )';
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

                        $mail->setFrom($applicationemail, 'Kawnex');
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
                }
            ?>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/sidebar.js"></script>
    <script>
        function fetchDurations() {
            var posTypeSelect = document.getElementById('posType');
            var durationSelect = document.getElementById('duration');
            var selectedPos = posTypeSelect.value;

            if (selectedPos === "") {
                alert("Please select a POS Type first.");
                return;
            }

            // Make an asynchronous request to your PHP script
            fetch('ajaxadmin/fetchDuration.php?pos=' + selectedPos)
                .then(response => response.text())
                .then(data => {
                    // Update the options of the duration select element
                    durationSelect.innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        }
    </script>

</body>