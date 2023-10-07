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
    <link rel="stylesheet" href="css/Manageinvoices.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Manage Invoices</h1>
                <div class="addinvoice">
                    <a href="ManageInvoices.php?do=allPayments" class="btn btn-light btnnewinv">Show all payments</a>
                    <a href="ManageInvoices.php?do=addservice"  class="btn btn-primary btnnewinv"> + Service Invoice</a>
                    <a href="ManageInvoices.php?do=addDomeinSer" class="btn btn-secondary btnnewinv"> + Domein Invoice</a>
                </div>
            </div>
            <?php
                $do=(isset($_GET['do']))?$_GET['do']:'manage';
                if($do=='manage'){?>
                <div class="mangebox">
                    <div class="search-container">
                        <input type="text" class="search-input" placeholder="Search ..." id="txtsearch">
                    </div>
                    <div class="table-container">
                        <table>
                            <thead> 
                                <tr>
                                    <th>#</th>
                                    <th>Client Name</th>
                                    <th>Date</th>
                                    <th>Total Amount</th>
                                    <th>Payments</th>
                                    <th>Remain</th>
                                    <th>Status</th>
                                    <th>Control</th>
                                </tr>
                            </thead>
                            <tbody class="bodyticket">
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
                }elseif($do=='addservice'){?>
                    <div class="add-service-invoice">
                        <h2>Add Service Invoice</h2>
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="service-select">Service:</label>
                                <select id="service-select" name="service" required >
                                    <?php
                                        $sql=$con->prepare('SELECT ServiceID,Service_Name,Service_Price FROM tblservices WHERE Active=1');
                                        $sql->execute();
                                        $services=$sql->fetchAll();
                                        foreach($services as $service){
                                            echo '<option value="'.$service['ServiceID'].'">'.$service['Service_Name'].' ('.$service['Service_Price'].' $) </option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="title">Title:</label>
                                <input type="text" id="title" name="title" required >
                            </div>
                            <div class="form-group">
                                <label for="for-what">For What:</label>
                                <input type="text" id="for-what" name="for-what" >
                            </div>
                            <div class="form-group">
                                <label for="color">Color:</label>
                                <input type="text" id="color" name="color" >
                            </div>
                            <div class="form-group">
                                <label for="description">Description:</label>
                                <textarea id="description" name="description" rows="4" ></textarea>
                            </div>
                            <div class="form-group">
                                <label for="price">Price:</label>
                                <input type="number" id="price" name="price" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="start-date">Start Date:</label>
                                <input type="date" id="start-date" name="start-date" required>
                            </div>
                            <div class="form-group">
                                <label for="end-date">End Date:</label>
                                <input type="date" id="end-date" name="end-date" required>
                            </div>
                            <div class="form-group controlbtn">
                                <button type="reset">Clear</button>
                                <button type="submit" name="add-service">Add Service</button>
                            </div>
                            <div class="finish">
                                <a href="makeainvoice.php?do=ser">Make a invoice</a>
                            </div>
                        </form>
                        <?php
                            if(isset($_POST['add-service'])){
                                $service = $_POST['service'];
                                $title = $_POST['title'];
                                $forwhat = $_POST['for-what'];
                                $color = $_POST['color'];
                                $description = $_POST['description'];
                                $price = $_POST['price'];
                                $dateBegin = $_POST['start-date'];
                                $endDate = $_POST['end-date'];

                                if(isset($_SESSION['AD_Service'])){
                                    $itemarray=array(
                                        'service'   => $service,
                                        'title'     => $title,
                                        'forwhat'   => $forwhat,
                                        'color'     => $color,
                                        'description'   => $description,
                                        'price'     => $price,
                                        'dateBegin' => $dateBegin,
                                        'endDate'   => $endDate

                                    );
                                    array_push($_SESSION['AD_Service'],$itemarray);
                                }else{
                                    $itemarray=array(
                                        'service'   => $service,
                                        'title'     => $title,
                                        'forwhat'   => $forwhat,
                                        'color'     => $color,
                                        'description'   => $description,
                                        'price'     => $price,
                                        'dateBegin' => $dateBegin,
                                        'endDate'   => $endDate
                                    );
                                    $_SESSION['AD_Service'][0]= $itemarray;
                                };
                            }
                        ?>
                    </div>
                    
                <?php
                }elseif($do=='addDomeinSer'){?>
                <div class="add-service-invoice">
                    <h2>Add Domein Invoice</h2>
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="service-select">Service Type:</label>
                            <select id="service-type-select" name="service-type" required>
                                <?php
                                    $sql = $con->prepare('SELECT DomainTypeID, ServiceName FROM tbldomaintype ');
                                    $sql->execute();
                                    $services = $sql->fetchAll();
                                    foreach ($services as $service) {
                                        echo '<option value="' . $service['DomainTypeID'] . '">' . $service['ServiceName'] . ' </option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="service-select">Service:</label>
                            <select id="service-select" name="service" required>
                                <?php
                                    $sql = $con->prepare('SELECT ServiceID, Service_Name, Service_Price FROM tblservices WHERE Active = 1');
                                    $sql->execute();
                                    $services = $sql->fetchAll();
                                    foreach ($services as $service) {
                                        echo '<option value="' . $service['ServiceID'] . '">' . $service['Service_Name'] . ' (' . $service['Service_Price'] . ' $) </option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="domain-name">Domain Name:</label>
                            <input type="text" id="domain-name" name="domain-name" required>
                        </div>
                        <div class="form-group">
                            <label for="renewal-price">Renewal Price:</label>
                            <input type="number" id="renewal-price" name="renewal-price" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="end-date">End Date:</label>
                            <input type="date" id="end-date" name="end-date" required>
                        </div>
                        <div class="form-group">
                            <label for="note">Note:</label>
                            <textarea id="note" name="note" rows="4"></textarea>
                        </div>
                        <div class="form-group controlbtn">
                            <button type="reset">Clear</button>
                            <button type="submit" name="add-domein-service">Add Domein Service</button>
                        </div>
                        <div class="finish">
                            <a href="makeainvoice.php?do=domein">Make a invoice</a>
                        </div>
                    </form>
                    <?php
                        if (isset($_POST['add-domein-service'])) {
                            $serviceType = $_POST['service-type'];
                            $service = $_POST['service'];
                            $domainName = $_POST['domain-name'];
                            $renewalPrice = $_POST['renewal-price'];
                            $endDate = $_POST['end-date'];
                            $note = $_POST['note'];

                            if (isset($_SESSION['AD_Domein'])) {
                                $itemarray = array(
                                    'serviceType' => $serviceType,
                                    'service' => $service,
                                    'domainName' => $domainName,
                                    'renewalPrice' => $renewalPrice,
                                    'endDate' => $endDate,
                                    'note' => $note
                                );
                                array_push($_SESSION['AD_Domein'], $itemarray);
                            } else {
                                $itemarray = array(
                                    'serviceType' => $serviceType,
                                    'service' => $service,
                                    'domainName' => $domainName,
                                    'renewalPrice' => $renewalPrice,
                                    'endDate' => $endDate,
                                    'note' => $note
                                );
                                $_SESSION['AD_Domein'][0] = $itemarray;
                            };
                            
                        }
                    ?>
                </div>
                
                <?php
                }elseif($do=='detail'){
                    $invoiceID = (isset($_GET['id']))?$_GET['id']:0;
                    $sql=$con->prepare('SELECT InvoiceID,InvoiceDate,TotalAmount,TotalTax,Client_FName,Client_LName,Client_companyName,Client_addresse,Client_city,Client_zipcode,StatusInvoice,StatusInvoiceColor
                                        FROM tblinvoice 
                                        INNER JOIN  tblclients ON tblclients.ClientID  =  tblinvoice.ClientID
                                        INNER JOIN tblstatusinvoice ON tblstatusinvoice.StatusInvoiceID = tblinvoice.Invoice_Status
                                        WHERE InvoiceID = ?');
                    $sql->execute(array($invoiceID));
                    $invoiceinfo = $sql->fetch();
                ?>
                <div class="deiteilinvoice">
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
                            <label for=""><?php echo  $invoiceinfo['Client_FName'].' '.$invoiceinfo['Client_LName'] ?></label>
                            <label for=""><?php echo  $invoiceinfo['Client_companyName'] ?></label>
                            <label for=""><?php echo  $invoiceinfo['Client_addresse'] ?></label>
                            <label for=""><?php echo  $invoiceinfo['Client_city'].'/'.$invoiceinfo['Client_zipcode'] ?></label>
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
                                    <td  class="invoiceamounts"><label for="">Tax ( %)</label></td>
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
                </div>
                <?php
                }elseif($do=='payment'){
                    $invoiceID = (isset($_GET['id']))?$_GET['id']:0;

                    $sql=$con->prepare('SELECT TotalAmount,TotalTax,ClientID FROM  tblinvoice WHERE InvoiceID =?');
                    $sql->execute(array($invoiceID));
                    $invoiceinfo=$sql->fetch();

                    $invoiceAmount = $invoiceinfo['TotalAmount'] + $invoiceinfo['TotalTax'];

                    $sql=$con->prepare('SELECT COALESCE(SUM(Payment_Amount), 0) AS TotalAmount
                                        FROM tblpayments
                                        WHERE invoiceID = ?;');
                    $sql->execute(array($invoiceID));
                    $result=$sql->fetch();

                    $amountPaid = $result['TotalAmount'];
                    $paymentDetails = calculatePaymentDetails($invoiceAmount,$amountPaid);

                    echo '<p>This invoice will be divided into '. $paymentDetails['numberOfPayments'].' equal payments as outlined below:</p>';
                    ?>
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
                    <div class="conclution">
                        <table>
                            <tr>
                                <td>Invoice Amount</td>
                                <td><?php echo  number_format($invoiceAmount, 2) ?> $</td>
                            </tr>
                            <tr>
                                <td>Amount Paid</td>
                                <td><?php echo  number_format($amountPaid, 2) ?> $</td>
                            </tr>
                            <tr>
                                <td>Remain Amount</td>
                                <td><?php echo  number_format($invoiceAmount - $amountPaid, 2) ?> $</td>
                            </tr>
                        </table>
                    </div>
                    <div class="frmaddpayment">
                        <form action="" method="post">
                            <input type="text" name="ClientID" id="" value="<?php echo  $invoiceinfo['ClientID'] ?>" hidden>
                            <input type="text" name="invoiceID" id="" value="<?php echo $invoiceID ?>" hidden>
                            <div class="deietilpayment">
                                <div class="linepayment">
                                    <label for="">Payment Methot :</label>
                                    <select name="paymentMethod" id="" required>
                                        <option value="">[SELECT ONE ]</option>
                                        <?php
                                            $sql=$con->prepare('SELECT paymentmethodD,methot FROM tblpayment_method');
                                            $sql->execute();
                                            $methods = $sql->fetchAll();
                                            foreach($methods as $method){
                                                echo '<option value="'.$method['paymentmethodD'].'">'.$method['methot'].'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="linepayment">
                                    <label for="">Operation Number :</label>
                                    <input type="text" name="NoofDocument" id="">
                                </div>
                                <div class="linepayment">
                                    <label for="">Payment Amount : </label>
                                    <input type="number" name="Payment_Amount" id="" step="0.01" required>
                                </div>
                                <div class="btncontrol_pay">
                                    <button type="submit" name="btnSavePay">Save Payment</button>
                                </div>
                            </div>
                        </form>
                        <?php
                            if(isset($_POST['btnSavePay'])){
                                $ClientID =$_POST['ClientID'];
                                $invoiceID = $_POST['invoiceID'];
                                $paymentMethod = $_POST['paymentMethod'];
                                $NoofDocument = $_POST['NoofDocument'];
                                $Payment_Amount = $_POST['Payment_Amount'];
                                
                                $comition =calculateCommission($invoiceID,$Payment_Amount,$con);

                                $sql=$con->prepare('SELECT methot FROM  tblpayment_method WHERE paymentmethodD=?');
                                $sql->execute(array($paymentMethod));
                                $resultmethod= $sql->fetch();
                                $paymentnameMethod= $resultmethod['methot'];

                                $sql=$con->prepare('SELECT Client_email,Client_FName,Client_LName FROM tblclients WHERE ClientID=?');
                                $sql->execute(array($ClientID));
                                $resultemail=$sql->fetch();
                                $cleintEmail = $resultemail['Client_email'];
                                $client_Name = $resultemail['Client_FName'].' ' .$resultemail['Client_LName'];

                                $sql=$con->prepare('INSERT INTO  tblpayments (ClientID,invoiceID,paymentMethod,NoofDocument,Payment_Amount,Payment_Date)
                                                    VALUES (:ClientID,:invoiceID,:paymentMethod,:NoofDocument,:Payment_Amount,:Payment_Date)');
                                $sql->execute(array(
                                    'ClientID'          => $ClientID,
                                    'invoiceID'         => $invoiceID,
                                    'paymentMethod'     => $paymentMethod,
                                    'NoofDocument'      => $NoofDocument,
                                    'Payment_Amount'    => $Payment_Amount,
                                    'Payment_Date'      => date('Y-m-d')
                                ));
                                

                                require_once '../mail.php';
                                $mail->setFrom($applicationemail, 'YK technology');
                                $mail->addAddress($cleintEmail);
                                $mail->Subject = 'Confirmation of Successful Payment';
                                $mail->Body    = '
                                                    Dear '.$client_Name.'<br>
                                                    I hope this message finds you well. I am writing to inform you that your 
                                                    recent payment has been successfully processed, and we have received the funds.
                                                    We greatly appreciate your prompt payment, and it helps us to continue providing
                                                    you with our services/products without interruption.<br>
                                                    Here are the details of the payment: <br>
                                                    Invoice/Reference Number: '.$invoiceID.' <br>
                                                    Payment Amount: '.$Payment_Amount.' $ <br>
                                                    Payment Date: '.date('Y-m-d').' <br>
                                                    Payment Method: '.$paymentnameMethod.'  <br>
                                                    If you have any questions or concerns regarding this payment or any other matter 
                                                    related to our services/products, please feel free to contact our customer support
                                                    team at info@ykinnovate.com. We are here to assist you with any inquiries you may
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
                                $amountPaid1 = $result['TotalAmount'];

                                if($invoiceAmount <= round($amountPaid1,2) ){
                                    $invoicePaid = 1 ; 
                                }else{
                                    $invoicePaid = 0 ;  
                                }

                                if($invoicePaid == 1){
                                    $sql=$con->prepare('UPDATE  tblinvoice SET Invoice_Status = 2 WHERE InvoiceID = ?');
                                    $sql->execute(array($invoiceID));

                                    $mail->setFrom($applicationemail, 'YK technology');
                                    $mail->addAddress($cleintEmail);
                                    $mail->Subject = 'Confirmation of Full Payment for Invoice '.$invoiceID;
                                    $mail->Body    = '
                                            Dear '.$client_Name.'<br>
                                            I hope this email finds you well. I am writing to confirm that we have received your payment for Invoice 
                                            '.$invoiceID.', and it has been fully settled. <br>
                                            We would like to express our sincere appreciation for your prompt payment. Your commitment to fulfilling your 
                                            financial obligations is greatly valued, and it helps us maintain the smooth operation of our business.<br>
                                            If you have any questions or need further clarification regarding this payment, please do not hesitate to contact 
                                            our accounts department at info@ykinnovate.com.<br>
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
                                echo '<script> location.href="ManageInvoices.php" </script>';
                            }
                        ?>
                    </div>
                    <?php
                }elseif($do=='cancel'){
                    $invoiceID = (isset($_GET['id']))?$_GET['id']:0;
                ?>
                    <div class="conform_cancel_invoice">
                        <form action="" method="post">
                            <h3>Are you sure you want to cancel the invoice?</h3>
                            <h1>invoice # <?php echo  $invoiceID ?></h1>
                            <div class="btncontrol">
                                <input type="text" name="txtinvoiceID" id="" value="<?php echo  $invoiceID ?>" hidden>
                                <a href="ManageInvoices.php">NO</a>
                                <button type="submit" name="btncancelInvoice">Yes</button>
                            </div>
                        </form>
                        <?php
                            if(isset($_POST['btncancelInvoice'])){
                                $invoiceID=$_POST['txtinvoiceID'];

                                $sql=$con->prepare('SELECT InvoiceID,Client_email,Client_FName,Client_LName,TotalAmount,TotalTax,InvoiceDate
                                                    FROM  tblinvoice
                                                    INNER JOIN  tblclients ON tblclients.ClientID = tblinvoice.InvoiceID 
                                                    WHERE InvoiceID =?');
                                $sql->execute(array($invoiceID));
                                $result=$sql->fetch();
                                $userEmail= $result['Client_email'];
                                $UserName = $result['Client_FName'].' '. $result['Client_LName'];
                                $totalAmount = $result['TotalAmount'] + $result['TotalTax'];

                                $sql=$con->prepare('UPDATE tblinvoice SET Invoice_Status = 3 WHERE InvoiceID = ?');
                                $sql->execute(array($invoiceID));
                                
                                require_once '../mail.php';
                                $mail->setFrom($applicationemail, 'YK technology');
                                $mail->addAddress($userEmail);
                                $mail->Subject = 'Invoice Cancellation Notice';
                                $mail->Body    = '
                                Dear '.$UserName.',<br>
                                I hope this message finds you well. I am writing to inform you that we have had 
                                to cancel Invoice #'.$invoiceID.' dated '.$result['InvoiceDate'].' for the amount of '.$totalAmount.'.
                                After careful review and consideration, we have determined that it is necessary to void this invoice. <br>
                                The decision to cancel this invoice is based on not paid or User request or dublicated invoice or changing service
                                We understand that this may cause inconvenience, and we sincerely apologize for any inconvenience this may have caused you.<br>
                                If you have any questions or concerns regarding this cancellation, or if you require any further clarification, 
                                please do not hesitate to reach out to our Accounts Department at info@ykinnovate.com .<br>
                                Once again, we apologize for any inconvenience this may have caused, and we appreciate your understanding in this matter. 
                                We value your business and look forward to continuing our positive business relationship.<br>
                                Thank you for your attention to this matter.<br>
                                Sincerely,<br>
                                ';
                                $mail->send();
                                echo '<script> location.href="ManageInvoices.php" </script>';
                            }
                        ?>
                    </div>
                <?php
                }elseif($do=='allPayments'){?>
                <div class="allpaymentdiv">
                    <div class="searchpaymenttype">
                        <input type="text" id="search" placeholder="Search Payments">
                    </div>
                    <div id="payments-list"></div>
                    <div id="total-amount"></div>
                </div>
                <?php
                }else{
                    header('location:index.php');
                }
            ?>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/Manageinvoices.js"></script>
    <script src="js/sidebar.js"></script>
</body>