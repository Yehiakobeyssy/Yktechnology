<?php
    session_start();
    if(!isset($_COOKIE['AgentID'])){
        if(!isset($_SESSION['AgentID'])){
            header('location:index.php');
        }
    }
    $agentId= (isset($_COOKIE['AgentID']))?$_COOKIE['AgentID']:$_SESSION['AgentID'];
    $do = (isset($_GET['do']))?$_GET['do']:'Manage';

    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';

    $sql=$con->prepare('SELECT saleActive,Sale_FName,Sale_LName,PromoCode FROM tblsalesperson WHERE SalePersonID =?');
    $sql->execute(array($agentId));
    $result=$sql->fetch();
    $isActive=$result['saleActive'];
    $firstname= $result['Sale_FName'];
    $lastName = $result['Sale_LName'];
    $full_name = $firstname .' ' . $lastName ;
    $promoCode = $result['PromoCode'];

    if($isActive == 0){
        setcookie("AgentID","",time()-3600);
        unset($_SESSION['AgentID']);
        echo '<script> location.href="index.php" </script>';
    }
?>
    <link rel="stylesheet" href="css/ManageAddService.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head> 
<body>
    <?php include 'include/navbar.php' ?>
    <div class="containerbody">
        <?php include 'include/sidebar.php' ?>
        <div class="includebody">
            <div class="title">
                <h3>Add Service</h3>
            </div>
            <?php
                if($do == 'Manage'){?>
                    <div class="two_buttons">
                        <button class="btn primary" id="btnaddnewServices">Add Services</button>
                        <button class="btn secondary" id="btnaddnewDomains">Add Domains</button>
                    </div>
                <?php
                }elseif($do=='Service'){?>
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
                                <a href="ManageAddService.php?do=CreateInvoice&type=ser">Make a invoice</a>
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

                }elseif($do=='Domain'){?>
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
                            <a href="ManageAddService.php?do=CreateInvoice&type=domein">Make a invoice</a>
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
                }elseif($do=='CreateInvoice'){
                    $type = (isset($_GET['type'])) ? $_GET['type'] : '';
                    ?>
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="client-select">Select Client:</label>
                            <select id="client-select" name="client" required>
                                <option value="">[SELECT ONE]</option>
                                <?php
                                $sql = $con->prepare("
                                                        SELECT
                                                            ClientID,
                                                            Client_FName,
                                                            Client_LName
                                                        FROM
                                                            tblclients
                                                        WHERE
                                                            promo_Code = ?
                                                    ");
                                $sql->execute([$promoCode]);
                                $clients = $sql->fetchAll();
                                foreach ($clients as $client) {
                                    $fullName = $client['Client_FName'] . ' ' . $client['Client_LName'];
                                    echo '<option value="' . $client['ClientID'] . '">' . $fullName . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <?php
                            if($type=="ser"){
                                if (isset($_SESSION['AD_Service'])) {
                                    if (!isset($_SESSION['ad-dlinvoice'])) {
                                        $_SESSION['ad-dlinvoice'] = [];
                                    }

                                    foreach ($_SESSION['AD_Service'] as $serviceData) {
                                        $isAlreadyAdded = false;

                                        foreach ($_SESSION['ad-dlinvoice'] as $invoiceData) {
                                            if (
                                                $invoiceData['ServiceID'] == $serviceData['service'] &&
                                                $invoiceData['Description'] == $serviceData['title']
                                            ) {
                                                $isAlreadyAdded = true;
                                                break;
                                            }
                                        }

                                        if (!$isAlreadyAdded) {
                                            $invoiceData = array(
                                                'ServiceID' => $serviceData['service'],
                                                'Description' => $serviceData['title'],
                                                'Price' => $serviceData['price']
                                            );
                                            $_SESSION['ad-dlinvoice'][] = $invoiceData;
                                        }
                                    }
                                }

                            }elseif($type="domein"){
                                if (isset($_SESSION['AD_Domein'])) {
                                    if (!isset($_SESSION['ad-dlinvoice'])) {
                                        $_SESSION['ad-dlinvoice'] = [];
                                    }

                                    foreach ($_SESSION['AD_Domein'] as $domainData) {
                                        $isAlreadyAdded = false;

                                        foreach ($_SESSION['ad-dlinvoice'] as $invoiceData) {
                                            if (
                                                $invoiceData['ServiceID'] == $domainData['service'] &&
                                                $invoiceData['Description'] == $domainData['domainName']
                                            ) {
                                                $isAlreadyAdded = true;
                                                break;
                                            }
                                        }

                                        if (!$isAlreadyAdded) {
                                            $invoiceData = array(
                                                'ServiceID'   => $domainData['service'],
                                                'Description' => $domainData['domainName'],
                                                'Price'       => $domainData['renewalPrice']
                                            );
                                            $_SESSION['ad-dlinvoice'][] = $invoiceData;
                                        }
                                    }
                                }
                            }else{
                                echo '<script> location.href="index.php" </script>';
                            }
                            if (isset($_SESSION['ad-dlinvoice']) && !empty($_SESSION['ad-dlinvoice'])) {
                                echo '<table>';
                                echo    '<thead>';
                                echo        '<tr>';
                                echo            '<th>Service ID</th>';
                                echo            '<th>Description</th>';
                                echo            '<th>Price</th>';
                                echo            '<th>Action</th>'; 
                                echo        '</tr>';
                                echo    '</thead>';
                                echo '<tbody>';

                                foreach ($_SESSION['ad-dlinvoice'] as $item) {
                                    $sql=$con->prepare('SELECT Service_Name FROM  tblservices WHERE ServiceID =?');
                                    $sql->execute(array($item['ServiceID']));
                                    $serviceName=$sql->fetch();
                                    echo '<tr>';
                                    echo '<td>' . $serviceName['Service_Name'] . '</td>';
                                    echo '<td>' . $item['Description'] . '</td>';
                                    echo '<td>' . $item['Price'] . '</td>';
                                    echo '<td><button onclick="deleteItem(' . $item['ServiceID'] . ')" class="delete-button">Delete</button></td>';
                                    echo '</tr>';
                                }

                                echo '</tbody>';
                                echo '</table>';
                            } else {
                                echo 'No items in the invoice.';
                            }
                            $totalAmount = 0;
                            if (isset($_SESSION['ad-dlinvoice']) && !empty($_SESSION['ad-dlinvoice'])) {
                                foreach ($_SESSION['ad-dlinvoice'] as $item) {
                                    $totalAmount += (float) $item['Price'];
                                }
                            }
                            
                        ?>
                        <div class="total-amount">
                            <strong>Total Amount:</strong> <?php echo '$' . number_format($totalAmount, 2); ?>
                        </div>

                        <div class="finish_Invoice">
                            <input type="text" name="txttotalAmount" id="" value="<?php echo $totalAmount ?>" hidden>
                            <button type="submit" name="btncreateInvoice">Save Invoice</button>
                        </div>
                    </form>
                <?php
                if(isset($_POST['btncreateInvoice'])){
                    $client_invoice_ID = $_POST['client'];
                    $totalAmount_Invoice = $_POST['txttotalAmount'];

                    $sql=$con->prepare('SELECT CountryTVA FROM tblcountrys 
                                        INNER JOIN  tblclients ON tblclients.Client_country =tblcountrys.CountryID
                                        WHERE ClientID = ?');
                    $sql->execute(array($client_invoice_ID));
                    $result_tax= $sql->fetch();
                    $percentTax= $result_tax['CountryTVA'];

                    if($totalAmount_Invoice > 0){
                        $InvoiceDate = date('Y-m-d');
                        $InvoiceTime = date("H:i:s");
                        $ClientID = $client_invoice_ID ; 
                        $TotalAmount = $totalAmount_Invoice;
                        $TotalTax = $totalAmount_Invoice * $percentTax / 100;
                        $Invoice_Status = 1;

                        $sqlInsertInvoice = $con->prepare('INSERT INTO tblinvoice (InvoiceDate, InvoiceTime, ClientID, TotalAmount, TotalTax, Invoice_Status) VALUES (?, ?, ?, ?, ?, ?)');
                        $sqlInsertInvoice->execute([$InvoiceDate, $InvoiceTime, $ClientID, $TotalAmount, $TotalTax, $Invoice_Status]);
                        $NewInvoiceID = $con->lastInsertId();

                        foreach ($_SESSION['ad-dlinvoice'] as $item) {
                            $Invoice = $NewInvoiceID;
                            $Service = $item['ServiceID'];
                            $Description = $item['Description'];
                            $UnitPrice = $item['Price'];
                            $ClientServiceID = 1;
                        
                            $sqlInsertDetailInvoice = $con->prepare('INSERT INTO tbldetailinvoice (Invoice, Service, Description, UnitPrice, ClientServiceID) VALUES (?, ?, ?, ?, ?)');
                            $sqlInsertDetailInvoice->execute([$Invoice, $Service, $Description, $UnitPrice, $ClientServiceID]);
                        }

                        if ($type == 'ser'){
                            foreach ($_SESSION['AD_Service'] as $serviceData) {
                                $ClientID = $client_invoice_ID;
                                $Date_service = $serviceData['dateBegin'];
                                $ServiceID = $serviceData['service'];
                                $Price = $serviceData['price'];
                                $Dateend = $serviceData['endDate'];
                                $ServiceTitle = $serviceData['title'];
                                $ServiceDomain = '';
                                $ServiceTransfer = 0;
                                $CodeTransfer = '';
                                $forwhat = $serviceData['forwhat'];
                                $Colors = $serviceData['color'];
                                $Discription = $serviceData['description'];
                                $filename = '';
                                $ServiceDone = 0;
                                $serviceStatus = 1;
                            
                                $sqlInsertClientService = $con->prepare('INSERT INTO tblclientservices (ClientID, Date_service, ServiceID, Price, Dateend, ServiceTitle, ServiceDomain, ServiceTransfer, CodeTransfer, forwhat, Colors, Discription, filename, ServiceDone, serviceStatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                                $sqlInsertClientService->execute([$ClientID, $Date_service, $ServiceID, $Price, $Dateend, $ServiceTitle, $ServiceDomain, $ServiceTransfer, $CodeTransfer, $forwhat, $Colors, $Discription, $filename, $ServiceDone, $serviceStatus]);
                            }                            
                        }elseif ($type == 'domein'){
                            foreach ($_SESSION['AD_Domein'] as $domainData) {
                                $DateBegin = date('Y-m-d'); // Set the DateBegin to the current date or another appropriate value
                                $Client = $client_invoice_ID;
                                $ServiceType = $domainData['serviceType'];
                                $DomeinName = $domainData['domainName'];
                                $RenewDate = $domainData['endDate'];
                                $Price_Renew = $domainData['renewalPrice'];
                                $Note = $domainData['note'];
                                $Status = 1;
                                $ServiceID = $domainData['service'];
                            
                                // Prepare and execute the INSERT query for tbldomeinclients
                                $sqlInsertDomainClient = $con->prepare('INSERT INTO tbldomeinclients (DateBegin, Client, ServiceType, DomeinName, RenewDate, Price_Renew, Note, Status, ServiceID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                                $sqlInsertDomainClient->execute([$DateBegin, $Client, $ServiceType, $DomeinName, $RenewDate, $Price_Renew, $Note, $Status, $ServiceID]);
                            }
                            
                        }
                        unset($_SESSION['AD_Domein']);
                        unset($_SESSION['AD_Service']);
                        unset($_SESSION['ad-dlinvoice']);

                        $sql=$con->prepare('SELECT ClientID,Client_FName,Client_LName,Client_email FROM  tblclients WHERE ClientID = ?');
                        $sql->execute(array($client_invoice_ID));
                        $result_client = $sql->fetch();
                        $clientemail = $result_client['Client_email'];
                        $clientName  = $result_client['Client_FName'].' '.$result_client['Client_LName'];
                        $InvoiceDate = date('Y-m-d');
                        $Invoice     = $NewInvoiceID;
                        $expirationDate = date('Y-m-d', strtotime($InvoiceDate . ' + ' . 30 . ' days'));

                        require_once '../mail.php';
                        $mail->setFrom($applicationemail, 'Kawnex');
                        $mail->addAddress($clientemail);
                        $mail->Subject = 'Invoice for Service - Invoice #'.$Invoice.'';
                        $mail->Body    = '
                                            Dear '.$clientName.',<br>
                                            I hope this email finds you well. I want to express my gratitude for choosing Kawnex for your Service needs. As part of our commitment to transparency and efficient communication, we are sending you the invoice for the Service provided to you.<br>
                                            Below are the details of your invoice:<br>
                
                                            Invoice Number: #'.$Invoice.'<br>
                                            Invoice Date: '.$InvoiceDate.'<br>
                                            Due Date: '.$expirationDate.'<br>
                                            Amount Due: $'.$TotalAmount+$TotalTax .'<br>
                                            Please review the invoice . You can find the  invoice  on the following link. <br>
                                            <a href="'.$websiteaddresse.'user/viewinvoice.php?id='.$Invoice.'">view Invoice </a>
                                            If you have any questions or concerns regarding this invoice or need any additional information, please do not hesitate to reach out to our dedicated support team at info@kawnex.com.<br>
                                            We kindly request that you make the payment by the due date to ensure there are no disruptions to your Service. Your prompt attention to this matter is greatly appreciated.<br>
                                            Thank you once again for choosing Kawnex. We value your business and are committed to providing you with the best Service experience.<br>
                                            Best Regards,
                                        ';
                        $mail->send();

                        echo '<script> location.href ="ManageAddService.php"</script>';
                    }else{
                        echo '
                        <div class="alert alert-danger" role="alert">
                            The Invoice is empty
                        </div>
                        ';
                    }
                }
                }else{

                }
            ?>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/ManageAddService.js"></script>
    <script src="js/sidebar.js"></script>
    <script>
        function deleteItem(serviceID) {
            //alert("Deleting item with ServiceID: " + serviceID);
            // Send an AJAX request to a PHP script to delete the item
            $.ajax({
                type: 'POST',
                url: 'delete_item.php', // Create this PHP script
                data: { serviceID: serviceID },
                success: function(response) {
                    // Handle the response here, such as removing the row from the table
                    if (response === 'success') {
                        // Assuming you have a table row with an ID matching the serviceID
                        $('#' + serviceID).remove();
                    } else {
                        alert('Failed to delete item.');
                    }
                }
            }); 
            location.reload();
        }
    </script>
</body>