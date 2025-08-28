<?php
session_start();

if (!isset($_COOKIE['useradmin'])) {
    if (!isset($_SESSION['useradmin'])) {
        header('location:index.php');
    }
}
$adminId = (isset($_COOKIE['useradmin'])) ? $_COOKIE['useradmin'] : $_SESSION['useradmin'];

include '../settings/connect.php';
include '../common/function.php';
include '../common/head.php';

$sql = $con->prepare('SELECT admin_active,admin_FName,admin_LName FROM  tbladmin WHERE admin_ID=?');
$sql->execute(array($adminId));
$result = $sql->fetch();
$isActive = $result['admin_active'];
$firstname = $result['admin_FName'];
$lastName = $result['admin_LName'];
$full_name = $firstname . ' ' . $lastName;

if ($isActive == 0) {
    setcookie("useradmin", "", time() - 3600);
    unset($_SESSION['useradmin']);
    echo '<script> location.href="index.php" </script>';
}



$do = (isset($_GET['do'])) ? $_GET['do'] : '';
?>
<link rel="stylesheet" href="css/makeainvoice.css">
<link rel="stylesheet" href="css/navbar.css">
<link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <form action="" method="post">
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Make the Invoice</h1>
            </div>
            <div class="form-group">
                <label for="client-select">Select Client:</label>
                <select id="client-select" name="client">
                    <option value="">[SELECT ONE]</option>
                    <?php
                    $sql = $con->prepare('SELECT ClientID, Client_FName, Client_LName FROM tblclients');
                    $sql->execute();
                    $clients = $sql->fetchAll();
                    foreach ($clients as $client) {
                        $fullName = $client['Client_FName'] . ' ' . $client['Client_LName'];
                        echo '<option value="' . $client['ClientID'] . '">' . $fullName . '</option>';
                    }
                    ?>
                </select>
            </div>
            <?php
            if ($do == 'ser') {
                if (isset($_SESSION['AD_Service'])) {
                    foreach ($_SESSION['AD_Service'] as $serviceData) {
                        $isAlreadyAdded = false;
                        foreach ($_SESSION['ad-dlinvoice'] as $invoiceData) {
                            if ($invoiceData['ServiceID'] == $serviceData['service'] && $invoiceData['Description'] == $serviceData['title']) {
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
            } elseif ($do == 'domein') {
                if (isset($_SESSION['AD_Domein'])) {
                    foreach ($_SESSION['AD_Domein'] as $domainData) {
                        $isAlreadyAdded = false;
                        foreach ($_SESSION['ad-dlinvoice'] as $invoiceData) {
                            if ($invoiceData['ServiceID'] == $domainData['service'] && $invoiceData['Description'] == $domainData['domainName']) {
                                $isAlreadyAdded = true;
                                break;
                            }
                        }
                        if (!$isAlreadyAdded) {
                            $invoiceData = array(
                                'ServiceID' => $domainData['service'],
                                'Description' => $domainData['domainName'],
                                'Price' => $domainData['renewalPrice']
                            );
                            $_SESSION['ad-dlinvoice'][] = $invoiceData;
                        }
                    }
                }
            } else {
                echo '<script> location.href="index.php" </script>';
            } ?>
            <?php
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

                        if ($do == 'ser'){
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
                        }elseif ($do == 'domein'){
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

                        echo '<script> location.href ="ManageInvoices.php"</script>';
                    }else{
                        echo '
                        <div class="alert alert-danger" role="alert">
                            The Invoice is empty
                        </div>
                        ';
                    }
                }
            ?>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/makeainvoice.js"></script>
    <script src="js/delete_item.js"></script>
    <script src="js/sidebar.js"></script>
</body>
