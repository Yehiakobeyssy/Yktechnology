<?php
    session_start();

    if(isset($_COOKIE['user'])){
        header('location:dashboard.php');
    }elseif(isset($_SESSION['user'])){
        header('location:dashboard.php');
    }

    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';
?>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <?php
        $do=(isset($_GET['do']))?$_GET['do']:'login';
        if($do == 'login'){?>
            <div class="login_frm">
                <div class="login_title">
                    <img src="../images/logo.png" alt="" srcset="">
                    <h1>Implementing Client Login Protection</h1>
                </div>
                <form action="" method="post">
                    <div class="input_deiteils">
                        <div class="user">
                            <i class="fa-solid fa-user"></i>
                            <input type="email" name="txtusername" id="" placeholder="E-Mail" required>
                        </div>
                        <div class="pass">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="txtpassword" id="" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="forget">
                        <div class="rem">
                            <input type="checkbox" name="txtrem" id="rememberme">
                            <label for="rememberme"> Remeber me</label>
                        </div>
                        <div class="forget_pass">
                            <a href="forgetpass.php">Forget Password? </a>
                        </div>
                    </div>
                    <div class="control">
                        <button type="submit" name="btnlogin">LOGIN</button>
                    </div>
                    <?php
                        if(isset($_POST['btnlogin'])){
                            $username = $_POST['txtusername'];
                            $password = sha1($_POST['txtpassword']);
                            $remember = (isset($_POST['txtrem']))?1:0;

                            $sql=$con->prepare('SELECT ClientID FROM tblclients WHERE Client_email=? AND Client_Password=? AND client_active=1');
                            $sql->execute(array($username,$password));
                            $check_login=$sql->rowCount();

                            if($check_login == 1){
                                $result=$sql->fetch();
                                $clientID = $result['ClientID'];
                                if($remember == 1){
                                    setcookie('user',$clientID, time()+3600 * 24 * 365);
                                }else{
                                    $_SESSION['user'] = $clientID;
                                }
                                header('location:dashboard.php');
                            }else{
                                echo '
                                    <div class="alert alert-danger">
                                        Error! The UserName or Password is Incorect
                                    </div>
                                ';
                            }
                        }
                    ?>
                </form>
                <div class="register">
                    <a href="index.php?do=signup">don't have an account register now</a>
                </div>
            </div>
        <?php
        }elseif($do =='signup'){?>
            <div class="signup_form">
                <h1>Register Now</h1>
                <form action="" method="post">
                    <div class="form_add">
                        <div class="personal_information">
                            <h3>Personal Information</h3>
                            <input type="text" name="Client_FName" id="" placeholder="First Name" required>
                            <input type="text" name="Client_LName" id="" placeholder="Last Name" required>
                            <input type="email" name="Client_email" id="txtemail" placeholder="E-mail" required>
                            <span id="txtcheckemail"></span>
                            <input type="text" name="Client_Phonenumber" id="" placeholder="Phone Number">
                        </div>
                        <div class="invoiceAdd">
                            <h3>invoice Address</h3>
                            <input type="text" name="Client_companyName" id="" placeholder="Company Name (optional)">
                            <input type="text" name="Client_addresse" id="" placeholder="Addresse">
                            <div class="city">
                                <input type="text" name="Client_city" id="" placeholder="City">
                                <input type="text" name="Client_Region" id="" placeholder="Region">
                                <input type="text" name="Client_zipcode" id="" placeholder="Zipcode">
                            </div>
                            <select name="Client_country" id="" required>
                                <option value="">[Country]</option>
                                <?php
                                    $sql=$con->prepare('SELECT * FROM tblcountrys');
                                    $sql->execute();
                                    $countrys=$sql->fetchAll();
                                    foreach($countrys as $country){
                                        echo '<option value="'.$country['CountryID'].'">'.$country['CountryName'].'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="sequrityinfo">
                            <h3>Account security</h3>
                            <input type="password" name="Client_Password" id="txtnewpass" placeholder="Password" required>
                            <input type="password" name="Client_Password_conform" id="txtconform" placeholder="Conform Password" required>
                        </div>
                        <div class="promo_code">
                            <h3>Promo code</h3>
                            <input type="text" name="txtpromocode" id="" placeholder="Promo Code">
                        </div>
                        <div class="alert alert-danger error"></div>
                    </div>
                    <div class="controllbtn">
                        <button type="submit" name="btnsaveclient" >Register</button>
                    </div>
                    <a href="index.php">Have an account?</a>
                </form>
                <?php
                    if(isset($_POST['btnsaveclient'])){
                        if($_POST['Client_Password'] == $_POST['Client_Password_conform']){
                            $emailuser = $_POST['Client_email'];
                            $checkemail = checkItem('Client_email','tblclients',$emailuser);
                            if($checkemail == 0){
                                $Client_FName          =$_POST['Client_FName'];        
                                $Client_LName          =$_POST['Client_LName'];
                                $Client_email          =$_POST['Client_email'];
                                $Client_Phonenumber    =$_POST['Client_Phonenumber']; 
                                $Client_companyName    =$_POST['Client_companyName'];
                                $Client_addresse       =$_POST['Client_addresse'];
                                $Client_city           =$_POST['Client_city'];
                                $Client_Region         =$_POST['Client_Region'];
                                $Client_zipcode        =$_POST['Client_zipcode'];
                                $Client_country        =$_POST['Client_country'];
                                $Client_Password       =$_POST['Client_Password']; 
                                $Client_Acctivationcode=sha1('date'.$Client_FName);
                                $promo_Code            =$_POST['txtpromocode'];
                                $client_active         =1;

                                $sql=$con->prepare('INSERT INTO tblclients (Client_FName,Client_LName,Client_email,Client_Phonenumber,Client_companyName,Client_addresse,Client_city,Client_Region,Client_zipcode,Client_country,Client_Password,Client_Acctivationcode,promo_Code,client_active) 
                                                    VALUES (:Client_FName,:Client_LName,:Client_email,:Client_Phonenumber,:Client_companyName,:Client_addresse,:Client_city,:Client_Region,:Client_zipcode,:Client_country,:Client_Password,:Client_Acctivationcode,:promo_Code,:client_active)');
                                $sql->execute(array(
                                    'Client_FName'          =>$Client_FName ,
                                    'Client_LName'          =>$Client_LName ,
                                    'Client_email'          =>$Client_email ,
                                    'Client_Phonenumber'    =>$Client_Phonenumber ,
                                    'Client_companyName'    =>$Client_companyName ,
                                    'Client_addresse'       =>$Client_addresse  ,
                                    'Client_city'           =>$Client_city ,
                                    'Client_Region'         =>$Client_Region ,
                                    'Client_zipcode'        =>$Client_zipcode ,
                                    'Client_country'        =>$Client_country ,
                                    'Client_Password'       =>sha1($Client_Password) ,
                                    'Client_Acctivationcode'=>$Client_Acctivationcode ,
                                    'promo_Code'            =>$promo_Code,
                                    'client_active'         =>$client_active
                                ));
                                require_once '../mail.php';
                                $mail->setFrom($applicationemail, 'YK technology');
                                $mail->addAddress($Client_email);
                                $mail->Subject = 'Welcome to YK technology';
                                $mail->Body    = 'Dear '.$Client_FName.',
                                                We are thrilled to inform you that your registration was a success! 🎉<br>
                                                Welcome to our community. You are now part of an exciting journey where. <br>
                                                We are excited to have you on board, and we cant wait to see what amazing things you will accomplish.<br>
                                                If you have any questions or need assistance, please dont hesitate to reach out to our dedicated support team at info@yktechnology.es. 
                                                They are here to help you every step of the way.<br>
                                                Thank you for choosing us, and we look forward to providing you with a fantastic experience.<br>
                                                Best regards,<br>
                                                your username : '.$Client_email.'<br>
                                                password : '.$Client_Password.'';
                                $mail->send();
                                echo '<script> location.href="index.php"</script>';
                            }else{
                                echo '
                                    <div class="alert alert-danger">Error! The email is used Before </div>
                                ';
                            }
                        }else{
                            echo '
                                <div class="alert alert-danger">Error! The Password is not correct </div>
                            ';
                        }
                    }
                ?>
            </div>
        <?php
        }else{
            header('location:../index.php');
        }
    ?>
    <?php include '../common/jslinks.php' ?>
    <script src="js/index.js"></script>
</body>