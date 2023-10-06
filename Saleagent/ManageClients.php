<?php
    session_start();
    if(!isset($_COOKIE['AgentID'])){
        if(!isset($_SESSION['AgentID'])){
            header('location:index.php');
        }
    }
    $agentId= (isset($_COOKIE['AgentID']))?$_COOKIE['AgentID']:$_SESSION['AgentID'];

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
    $promocode= $result['PromoCode'];
    if($isActive == 0){
        setcookie("AgentID","",time()-3600);
        unset($_SESSION['AgentID']);
        echo '<script> location.href="index.php" </script>';
    }
?>
    <link rel="stylesheet" href="css/ManageClients.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head> 
<body>
    <?php include 'include/navbar.php' ?>
    <div class="containerbody">
        <?php include 'include/sidebar.php' ?>
        <div class="includebody">
            <div class="title">
                <h1>Manage Clients</h1>
            </div>
            <?php
                $do=(isset($_GET['do']))?$_GET['do']:'manage';
                if($do == 'manage'){?>
                <div class="mangebox">
                    <div class="search-container">
                        <input type="text" class="search-input" placeholder="Search ..." id="txtsearch">
                    </div>
                    <div class="table-container">
                        <table>
                            <thead> 
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone Number</th>
                                    <th>E-mail</th>
                                    <th>Invoices</th>
                                    <th>Payment</th>
                                    <th>Services</th>
                                    <th>Control</th>
                                </tr>
                            </thead>
                            <tbody class="bodyticket">
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
                }elseif($do == 'deitail'){
                    $clinetID = (isset($_GET['id']))?$_GET['id']:0;
                    $sql=$con->prepare('SELECT tblclients.*,CountryName,CountryTVA 
                                        FROM tblclients 
                                        INNER JOIN  tblcountrys ON  tblcountrys.CountryID= tblclients.Client_country
                                        WHERE ClientID = ?');
                    $sql->execute(array($clinetID));
                    $clientinfo= $sql->fetch();
                ?>
                    <div class="container_detail">
                        <div class="title">
                            <h3>About the Client</h3>
                        </div>
                        <div class="personal_information">
                            <h4>Personal Information</h4>
                            <table>
                                <tr>
                                    <td><label for="client-name">Client Name</label></td>
                                    <td><span id="client-name"><?php echo $clientinfo['Client_FName'].' '.$clientinfo['Client_LName'] ?></span></td>
                                </tr>
                                <tr>
                                    <td><label for="company-name">Company Name</label></td>
                                    <td><span id="company-name"><?php echo $clientinfo['Client_companyName'] ?></span></td>
                                </tr>
                                <tr>
                                    <td><label for="phone-number">Phone Number</label></td>
                                    <td><span id="phone-number"><?php echo $clientinfo['Client_Phonenumber'] ?></span></td>
                                </tr>
                                <tr>
                                    <td><label for="email">Email</label></td>
                                    <td><span id="email"><a href="mailto:<?php echo $clientinfo['Client_email'] ?>"><?php echo $clientinfo['Client_email'] ?></a></span></td>
                                </tr>
                                <tr>
                                    <td><label for="password">Password</label></td>
                                    <td><span id="password">******</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="address_info">
                            <h4>Address Info</h4>
                            <table>
                                <tr>
                                    <td><label for="address">Address</label></td>
                                    <td><span id="address"><?php echo $clientinfo['Client_addresse'] ?></span></td>
                                </tr>
                                <tr>
                                    <td><label for="country">Country</label></td>
                                    <td><span id="country"><?php echo $clientinfo['CountryName'] ?></span></td>
                                </tr>
                                <tr>
                                    <td><label for="city">City</label></td>
                                    <td><span id="city"><?php echo $clientinfo['Client_city'] ?></span></td>
                                </tr>
                                <tr>
                                    <td><label for="zip-code">Zip Code</label></td>
                                    <td><span id="zip-code"><?php echo $clientinfo['Client_zipcode'] ?></span></td>
                                </tr>
                                <tr>
                                    <td><label for="tax-percent">Tax (Percent)</label></td>
                                    <td><span id="tax-percent"><?php echo $clientinfo['CountryTVA'] ?> %</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                <?php
                }elseif($do=='sendemail'){
                    $clinetID = (isset($_GET['id']))?$_GET['id']:0;
                    
                    $sql=$con->prepare('SELECT Client_email,Client_Password,Client_FName,Client_LName FROM  tblclients WHERE ClientID= ?');
                    $sql->execute(array($clinetID));
                    $result_email = $sql->fetch();
                    $clientEmail = $result_email['Client_email'];
                    $clientName = $result_email['Client_FName'].' ' . $result_email['Client_LName'];
                ?>
                <h3>Send E-mail</h3>
                <div class="container_email">
                    <form action="" method="post">
                        <div class="subject">
                            <label for="">Subject</label>
                            <input type="text" name="txtsubject" id="" required>
                        </div>
                        <div class="bodytext">
                            <label for="">Body</label>
                            <textarea name="txtemailbody" id=""  rows="10" required></textarea>
                        </div>
                        <div class="btncontroler">
                            <button type="submit" name="btnsent">Send</button>
                        </div>
                    </form>
                    <?php
                        if(isset($_POST['btnsent'])){
                            $userEmail      = $clientEmail;
                            $subjectemail   = $_POST['txtsubject'];
                            $bodyemail      = $_POST['txtemailbody'];

                            require_once '../mail.php';
                            $mail->setFrom($applicationemail, 'YK-Technology');
                            $mail->addAddress($userEmail);
                            $mail->Subject = $subjectemail;
                            $mail->Body    = $bodyemail;
                            $mail->send();
                            echo '
                            <div class="alert alert-success" role="alert">
                                We Send the E-mail to the Client  
                            </div>
                            ';
                            echo "
                                <script>
                                    setTimeout(function () {
                                        window.location.href = 'ManageClients.php'; 
                                    }, 1000)
                                </script>
                            ";
                        }
                    ?>
                </div>
                <?php
                }else{
                    echo '<script> location.href="index.php" </script>';
                }
            ?>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/ManageClients.js"></script>
    <script src="js/sidebar.js"></script>
</body>