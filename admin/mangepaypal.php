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
    $sql=$con->prepare('SELECT key_payPal,Sumup_AccessToken,Sumup_merchat_code,Sumup_Email,tax_number,addresse,zip_code,region,website FROM tblsetting WHERE SettingID = 1');
    $sql->execute();
    $result= $sql->fetch();
    $paypalKey = $result['key_payPal'];
    $accessToken = $result['Sumup_AccessToken'];
    $merchatCode = $result['Sumup_merchat_code'];
    $email = $result['Sumup_Email'];
    $tax_number =  $result['tax_number'];
    $addresse = $result['addresse'];
    $zipcode= $result['zip_code'];
    $region = $result['region'];
    $website = $result['website'];



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
                <h1>Company Information</h1>
            </div>
            <form action="" method="post" >
                <label for="" style="margin-top: 10px;">Tax number</label>
                <input type="text" name="newTaxNumber" id="" placeholder="Tax Number"  value="<?php echo $tax_number ?>">
                <label for="">Address</label>
                <input type="text" name="newAddresse" id="" placeholder="Addresse" value="<?php echo $addresse ?>">
                <label for="">Rejion</label>
                <input type="text" name="newRejion" id="" placeholder="Rejion" value="<?php echo $zipcode ?>">
                <label for="">Zip code</label>
                <input type="text" name="newZip" id="" placeholder="Zip" value="<?php echo $region ?>">
                <label for="">Website</label>
                <input type="text" name="newWebsite" id="" placeholder="website" value="<?php echo $website ?>">
                <div class="btncontrol">
                    <button type="submit" name="btnedit_peson">Edit</button>
                </div>
                <?php
                    if(isset($_POST['btnedit_peson'])){
                        $newtaxNumber = $_POST['newTaxNumber'];
                        $newaddresse = $_POST['newAddresse'];
                        $newrejion = $_POST['newRejion'];
                        $newzip = $_POST['newZip'];
                        $newweb = $_POST['newWebsite'];

                        if($adminId == 1){
                            $sql= $con->prepare('UPDATE tblsetting SET tax_number=?,addresse=?,zip_code=?,region=?,website=? WHERE SettingID = 1');
                            $sql->execute(array($newtaxNumber,$newaddresse,$newzip,$newrejion,$newweb));
                            echo '<script> location.href="mangepaypal.php" </script>';
                        }else{
                            echo 'You dont have permition to change';
                        }
                    }
                ?>
            </form>
            <div class="title">
                <h1>PayPal key</h1>
            </div>
            <form action="" method="post">
                <a href="https://www.paypal.com/commercesetup/APICredentials?guided=true" target="_blank">get your Key</a>
                <input type="text" name="txtkey" id="" value="<?php echo $paypalKey ?>" placeholder="Paypal Key">
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
                        echo '<script> location.href="mangepaypal.php" </script>';
                    }else{
                        echo 'You dont have permition to change';
                    }
                }
            ?>
            <div class="title">
                <h1>SumUp key</h1>
            </div>
            <form action="" method="post">
                <label for="">Sumup Access Token</label>
                <input type="text" name="newAccess" id="" placeholder="Access Token" value="<?php echo $accessToken ?>">
                <label for="">Sumup Merchat code</label>
                <input type="text" name="newMerchat" id="" placeholder="Merchat Code" value="<?php echo $merchatCode ?>">
                <label for="">Sumup Email</label>
                <input type="email" name="newEmail" id="" placeholder="Email user" value="<?php echo $email ?>">
                <div class="btncontrol">
                    <button type="submit" name="btneditSumup">Edit</button>
                </div>
            </form>
            <?php
                if(isset($_POST['btneditSumup'])){
                    $newAccesscode = $_POST['newAccess'];
                    $newMerchate = $_POST['newMerchat'];
                    $newemail = $_POST['newEmail'];

                    if($adminId == 1){
                        $sql=$con->prepare('UPDATE  tblsetting  SET Sumup_AccessToken = ? ,Sumup_merchat_code = ?,Sumup_Email = ? WHERE SettingID = 1 ');
                        $sql->execute(array($newAccesscode,$newMerchate,$newemail));
                        echo '<script> location.href="mangepaypal.php" </script>';
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