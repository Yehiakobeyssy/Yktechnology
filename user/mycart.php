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

    $sql=$con->prepare('SELECT Client_FName,Client_LName,client_active,Client_email,Client_country FROM  tblclients WHERE ClientID=?');
    $sql->execute(array($clientId));
    $clientinfo = $sql->fetch();
    $clientName = $clientinfo['Client_FName'].' '. $clientinfo['Client_LName'];
    $clientemail= $clientinfo['Client_email'];

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


    if(isset($_POST['btncancelOrder'])){
        if(isset($_SESSION['dlinvoice'])){
            foreach ($_SESSION['dlinvoice'] as $key => $item){
                $serviceID = $item['clientserviceID'];
                $sql=$con->prepare('UPDATE tblclientservices SET serviceStatus = 4 WHERE ServicesID=?');
                $sql->execute(array($serviceID));
            }
            unset($_SESSION['dlinvoice']);
        }
    }


    if(isset($_POST['btnMakeInvoice'])){
        $totalprice= 0;
        foreach ($_SESSION['dlinvoice'] as $key => $item){
            $Price       = $item['Price'];
            $totalprice += $Price ;
        }

        $InvoiceDate    = date('Y-m-d');
        $InvoiceTime    = date("H:i:s");
        $ClientID       = $clientId;
        $TotalAmount    = $totalprice;
        $TotalTax       = $totalprice * $tva/100;
        $Invoice_Status = 1;

        $sql=$con->prepare('INSERT INTO  tblinvoice (InvoiceDate,InvoiceTime,ClientID,TotalAmount,TotalTax,Invoice_Status)
                            VALUES (:InvoiceDate,:InvoiceTime,:ClientID,:TotalAmount,:TotalTax,:Invoice_Status)');
        $sql->execute(array(
            'InvoiceDate'       => $InvoiceDate,
            'InvoiceTime'       => $InvoiceTime,
            'ClientID'          => $ClientID,
            'TotalAmount'       => $TotalAmount,
            'TotalTax'          => $TotalTax,
            'Invoice_Status'    => $Invoice_Status
        ));

        $InvoiceID = get_last_ID('InvoiceID','tblinvoice');

        foreach ($_SESSION['dlinvoice'] as $key => $item){
            $Invoice     = $InvoiceID;
            $Service     = $item['Service'];
            $Description = $item['serviceTitle'];
            $UnitPrice   = $item['Price'];

            $stat = $con->prepare('INSERT INTO  tbldetailinvoice (Invoice,Service,Description,UnitPrice)
                                    VALUES (:Invoice,:Service,:Description,:UnitPrice)');
            $stat->execute(array(
                'Invoice'     => $Invoice,
                'Service'     => $Service ,
                'Description' => $Description,
                'UnitPrice'   => $UnitPrice
            ));
        }

        unset($_SESSION['dlinvoice']);
        $expirationDate = date('Y-m-d', strtotime($InvoiceDate . ' + ' . 30 . ' days'));
        require_once '../mail.php';
        $mail->setFrom($applicationemail, 'YK technology');
        $mail->addAddress($clientemail);
        $mail->Subject = 'Invoice for Service - Invoice #'.$Invoice.'';
        $mail->Body    = '
                            Dear '.$clientName.',<br>
                            I hope this email finds you well. I want to express my gratitude for choosing YK-technology for your Service needs. As part of our commitment to transparency and efficient communication, we are sending you the invoice for the Service provided to you.<br>
                            Below are the details of your invoice:<br>

                            Invoice Number: #'.$Invoice.'<br>
                            Invoice Date: '.$InvoiceDate.'<br>
                            Due Date: '.$expirationDate.'<br>
                            Amount Due: $'.$TotalAmount+$TotalTax.'<br>
                            Please review the invoice . You can find the  invoice  on the following link. <br>
                            <a href="'.$websiteaddresse.'user/viewinvoice.php?id='.$Invoice.'">view Invoice </a>
                            If you have any questions or concerns regarding this invoice or need any additional information, please do not hesitate to reach out to our dedicated support team at info@yktechnology.es.<br>
                            We kindly request that you make the payment by the due date to ensure there are no disruptions to your Service. Your prompt attention to this matter is greatly appreciated.<br>
                            Thank you once again for choosing YK-technology. We value your business and are committed to providing you with the best Service experience.<br>
                            Best Regards,
                        ';
        $mail->send();
        header('location:manageinvoice.php');
    }
?>
    <link rel="stylesheet" href="css/mycart.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
    <?php include 'include/navbar.php' ?>
    <div class="conteiner_cart">
        <div class="title">
            <h3>My Cart</h3>
        </div>
        <div class="runajax"></div>
        <div class="deitielcart">
            <?php
                if(isset($_SESSION['shooping'])){
                    $array= $_SESSION['shooping'];
                    $count=count($array);
                    if($count==0){
                        unset($_SESSION['shooping']);
                        echo '<script> loaction.href.reload() </script>';
                    }else{
                        foreach ($_SESSION['shooping'] as $key => $item) {
                        $sql=$con->prepare('SELECT Service_Name,Service_Price,CategoryID FROM  tblservices WHERE ServiceID=?');
                        $sql->execute(array($item['id']));
                        $serviceinfo=$sql->fetch();?>
                        <form action="ajaxuser/updatecart.php" method="post" enctype="multipart/form-data">
                            <div  class="card_service card_service_item" data-key="<?php echo $key; ?>">
                                <div class="titleservice">
                                    <h3><?php echo  $serviceinfo['Service_Name'] ?></h3>
                                    <h3><?php echo  number_format($serviceinfo['Service_Price'],2,'.','').' $' ?></h3>
                                </div>
                                <div class="frmadddaiteils">
                                    <div class="title">
                                        <input type="text" name="serviceID" id="" value="<?php echo $item['id'] ?>" hidden>
                                        <label for="">Project title</label>
                                        <input type="text" name="titlename" id="titlename" placeholder="for example : your company name">
                                    </div>
                                    <?php
                                        if($serviceinfo['CategoryID'] ==1){?>
                                            <div class="domaininfo">
                                                <h4>Domain Info </h4>
                                                <label for="">Domain status</label>
                                                <div class="ststus_domain">
                                                    <div class="new">
                                                        <label for="newtransfer">New</label>
                                                        <input type="checkbox" name="transfer" class="newtransfer"  value="0">
                                                    </div>
                                                    <div class="tarnsfer">
                                                        <label for="transfer">Transfer</label>
                                                        <input type="checkbox" name="transfer" class="transfer" value="1">
                                                    </div>
                                                </div>
                                                <input type="text" name="domain" id="domain" placeholder="Domain Name" >
                                                <input type="text" name="code" class="codetransfer" id="code" placeholder="Authorization code / EPP code" >
                                            </div>
                                        <?php
                                        }
                                    ?>
                                    <div class="forwhat">
                                        <label for="">What is the purpose of this service?</label>
                                        <input type="text" name="forwhat" id="">
                                    </div>
                                    <div class="coloruse">
                                        <label for="">Visual Identity</label>
                                        <input type="text" id="colors" name="colors" placeholder="Color use for example : blue and white">
                                    </div>
                                    <div class="discription">
                                        <label for="">Discription</label>
                                        <textarea name="description" id="description"  rows="5" placeholder="Descripe your request to make your servise better"></textarea>
                                    </div>
                                    <div class="atthment">
                                        <label for="" id="titleAtt">Attachments</label>
                                        <div class="attachments">
                                            <input type="file" accept="application/pdf" name="filename" id="filename"/>
                                        </div>
                                        <p id="allowfiles">Allowed attachment file extension: .pdf</p>
                                    </div>
                                </div>
                                <div class="savebutton">
                                    <button class="btn btn-danger" id="btndeleteone" value="<?php echo $key; ?>">Delete</button>
                                    <button class="btn btn-success btnupdatestatus" value="<?php echo $key; ?>" name="btnsave">Save</button>
                                </div>
                            </div>
                            <div class="loadajax"></div>
                        </form>
                        <?php
                    }
                    }
                    ?>
                        <div class="savebutton">
                            <button id="prev-button" class="btn btn-secondary">Previous</button>
                            <button id="next-button" class="btn btn-secondary">Next</button>
                        </div>
                    <?php
                }elseif(isset($_SESSION['dlinvoice'])){
                    $arrayinv= $_SESSION['dlinvoice'];
                    $count=count($arrayinv);
                    if($count==0){
                        unset($_SESSION['dlinvoice']);
                        echo '<script> loaction.href.reload() </script>';
                    }
                    ?>
                    <div class="summary">
                                <div class="title_summary">
                                    <h3>Summary</h3>
                                </div>
                                <div class="table_summary">
                                    <table>
                                        <thead>
                                            <td>Service</td>
                                            <td colspan="2">Discription</td>
                                            <td>Price</td>
                                        </thead>
                                        <tbody>
                                            <?php
                                                if (isset($_SESSION['dlinvoice'])) {
                                                    $totalprice= 0;
                                                    foreach ($_SESSION['dlinvoice'] as $key => $item){
                                                        $serviceID = $item['Service'];
                                                        $sql=$con->prepare('SELECT Service_Name FROM tblservices WHERE  ServiceID = ?');
                                                        $sql->execute(array($serviceID));
                                                        $result=$sql->fetch();

                                                        $serviceName =$result['Service_Name'];
                                                        $discription = $item['serviceTitle'] ;
                                                        $Price       = $item['Price'];
                                                        $totalprice += $Price ;

                                                        echo '
                                                            <tr>
                                                                <td>'.$serviceName.'</td>
                                                                <td colspan="2">'.$discription.'</td>
                                                                <td>'.number_format($Price,2,'.','').' $</td>
                                                            </tr>
                                                        ';
                                                    }
                                                    $tax = $totalprice * $tva/100;
                                                    $totalinvoice = $totalprice + $tax;
                                                }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3"> <label for="">Subtotal:</label></td>
                                                <td class="totalamount"><span><?php echo  number_format($totalprice,2,'.','') ?> $</span></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"> <label for="">Tax (<?php echo number_format($tva,2,'.','') ?>%):</label></td>
                                                <td class="totalamount"><span><?php echo number_format($tax,2,'.','') ?> $</span></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"> <label for="">Total:</label></td>
                                                <td class="totalamount" id="totalInvoice"><span><?php echo number_format($totalinvoice,2,'.','') ?> $</span></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="create_invoice">
                                    <form action="" method="post">
                                        <button type="submit" class="btn btn-danger" name="btncancelOrder"> Cancel </button>
                                        <button type="submit" class="btn btn-primary" name="btnMakeInvoice"> Create Invoice</button>
                                    </form>
                                </div>
                            </div>
                        <?php
                }else{
                    echo '
                        <div class="alert alert-danger" role="alert">
                            Your Cart is empty 
                        </div>
                    ';
                };
            ?>
            
        </div>
    </div>
    <?php include '../common/jslinks.php' ?>
    <script src="js/mycart.js"></script>
    <script src="js/navbar.js"></script>
</body>