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
    $sql=$con->prepare('SELECT key_payPal FROM tblsetting WHERE SettingID = 1');
    $sql->execute();
    $result= $sql->fetch();
    $paypalKey = $result['key_payPal'];


?>
    <link rel="stylesheet" href="css/managepaypal.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>PayPal key</h1>
            </div>
            <form action="" method="post">
                <a href="https://www.paypal.com/commercesetup/APICredentials?guided=true" target="_blank">get your Key</a>
                <input type="text" name="txtkey" id="" value="<?php echo $paypalKey ?>">
                <div class="btncontrol">
                    <button type="submit" name="btnedit">Edit</button>
                </div>
            </form>
            <?php
                    if(isset($_POST['btnedit'])){
                        $newKey = $_POST['txtkey'];

                
                        if($adminId == 1){
                            $sql=$con->prepare('UPDATE  tblsetting  SET key_payPal = ? WHERE SettingID = 1 ');
                            $sql->execute(array($newKey));
                            echo '<script> location.href="ManageSetting.php" </script>';
                        }else{
                            echo 'You dont have permition to change';
                        }
                    }
            ?>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/sidebar.js"></script>
</body>