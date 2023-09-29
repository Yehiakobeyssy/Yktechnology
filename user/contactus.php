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

    $sql=$con->prepare('SELECT Client_FName,Client_LName,client_active,Client_email FROM  tblclients WHERE ClientID=?');
    $sql->execute(array($clientId));
    $clientinfo = $sql->fetch();
    $clientName = $clientinfo['Client_FName'].' '. $clientinfo['Client_LName'];
    

    if($clientinfo['client_active'] == 0){
        setcookie("user","",time()-3600);
        unset($_SESSION['user']);
        echo '<script> location.href="index.php" </script>';
    }
?>
    <link rel="stylesheet" href="css/contactus.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
    <?php include 'include/navbar.php' ?>
    <div class="contact_us_frm">
        <h1>Contact us</h1>
        <form action="" method="post">
            <label for="">Subject</label>
            <input type="text" name="txtsub" id="" required>
            <label for="">Text Message</label>
            <textarea name="txtmessage" id=""  rows="20" require_once></textarea>
            <div class="corntrolbtn">
                <button type="submit" name="btnsendemail">SEND</button>
            </div>
            <?php
                if(isset($_POST['btnsendemail'])){
                    $subject = $_POST['txtsub'];
                    $message = $_POST['txtmessage'];
                    $clientemail = $clientinfo['Client_email'];

                    require_once '../mail.php';
                    $mail->setFrom($applicationemail, 'Contact US User Form');
                    $mail->addAddress('yehiakobeyssy2018@gmail.com');
                    $mail->Subject = $subject;
                    $mail->Body    = $message. '<br><span style="font-weight: bold;"> The clinet Email is</span> : '. $clientemail;
                    $mail->send();

                    echo '
                        <div class="alert alert-success" role="alert">
                            The email was sent to us. We will respond as fast as possible.
                        </div>
                    ';
                }
            ?>
        </form>
    </div>
    <?php include '../common/jslinks.php' ?>
    <script src="js/navbar.js"></script>
</body>