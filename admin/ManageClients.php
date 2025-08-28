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
    <link rel="stylesheet" href="css/ManageClients.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
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
                                    <td><span id="password"><a href="ManageClients.php?do=resetpass&id=<?php echo $clinetID?>">Reset Password</a></span></td>
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
                }elseif($do=='resetpass'){
                    $clinetID = (isset($_GET['id']))?$_GET['id']:0;
                    
                    $sql=$con->prepare('SELECT Client_email,Client_Password,Client_FName,Client_LName FROM  tblclients WHERE ClientID= ?');
                    $sql->execute(array($clinetID));
                    $result_email = $sql->fetch();
                    $clientEmail = $result_email['Client_email'];
                    $clientName = $result_email['Client_FName'].' ' . $result_email['Client_LName'];

                    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+';
                    $password = '';
                    $passwordLength = 8;
                    for ($i = 0; $i < $passwordLength; $i++) {
                        $randomIndex = mt_rand(0, strlen($characters) - 1);
                        $password .= $characters[$randomIndex];
                    }
                    $newpass = $password;

                    $sql=$con->prepare('UPDATE tblclients SET Client_Password =? WHERE ClientID=  ?');
                    $sql->execute(array(sha1($newpass),$clinetID));

                    require_once '../mail.php';
                    $mail->setFrom($applicationemail, 'Reset Password');
                    $mail->addAddress($clientEmail);
                    $mail->Subject = 'Password Reset Notification';
                    $mail->Body    = '
                                        Dear '.$clientName.', <br>
                                        We hope this email finds you well. We wanted to inform you that your password for your Kawnex  account has been reset as per your request or for security reasons.<br>
                                        Here are your updated account details:<br>
                                        - Username: '.$clientEmail.'<br>
                                        - New Password: '.$password.' <br>
                                        Please make sure to keep your login credentials secure and do not share them with anyone. 
                                        If you did not request this password reset or have any concerns about the security of your account,
                                        please contact our support team immediately at info@kawnex.com .<br>
                                        To access your account, please visit our website at www.kawnex.com/user and use the provided login credentials.<br>
                                        Thank you for choosing Kawnex. We are committed to providing you with a secure and reliable service.<br>
                                        If you have any questions or need further assistance, feel free to reach out to our support team. We are here to help.<br>
                                        Best regards,
                    ';
                    $mail->send();
                    echo '
                    <div class="alert alert-success" role="alert">
                        We reset the password and send the new password to the user 
                    </div>
                    ';
                    echo "
                        <script>
                            setTimeout(function () {
                                window.location.href = 'ManageClients.php'; 
                            }, 1000)
                        </script>
                    ";
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
                            $mail->setFrom($applicationemail, 'Kawnex');
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
                }elseif($do=='block'){
                    $clinetID = (isset($_GET['id']))?$_GET['id']:0;

                    $sql=$con->prepare('SELECT client_active FROM  tblclients WHERE ClientID=?');
                    $sql->execute(array($clinetID));
                    $result_block = $sql->fetch();

                    if($result_block['client_active'] == 1){
                        $sql=$con->prepare('UPDATE tblclients SET client_active =0 WHERE ClientID=?');
                        $sql->execute(array($clinetID));
                    }else{
                        $sql=$con->prepare('UPDATE tblclients SET client_active =1 WHERE ClientID=?');
                        $sql->execute(array($clinetID));
                    }
                    echo '<script> location.href="ManageClients.php" </script>';
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