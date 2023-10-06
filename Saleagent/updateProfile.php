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

    $sql=$con->prepare('SELECT * FROM tblsalesperson WHERE SalePersonID =?');
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
    <link rel="stylesheet" href="css/updateProfile.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head> 
<body>
    <?php include 'include/navbar.php' ?>
    <div class="containerbody">
        <?php include 'include/sidebar.php' ?>
        <div class="includebody">
        <div class="container">
            <form action="" method="post">
                <h2>Update Profile</h2>

                <div class="section">
                    <h3 class="section-title">Personal Information</h3>
                    <label for="firstName">First Name:</label>
                    <input type="text" id="firstName" name="firstName" required value="<?php echo $result['Sale_FName']?>">

                    <label for="lastName">Last Name:</label>
                    <input type="text" id="lastName" name="lastName" required value="<?php echo $result['Sale_LName']?>">

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" disabled value="<?php echo $result['email_Sale']?>">
                </div>

                <div class="section">
                    <h3 class="section-title">Location</h3>
                    <label for="country">Country:</label>
                    <select id="country" name="country" required>
                        <option value="">[select one]</option>
                        <?php
                            $sql=$con->prepare('SELECT CountryID,CountryName FROM tblcountrys ');
                            $sql->execute();
                            $rows=$sql->fetchAll();

                            foreach($rows as $row){
                                if($result['Country']==$row['CountryID']){
                                    echo '<option value="'.$row['CountryID'].'" selected>'.$row['CountryName'].'</option>';
                                }else{
                                    echo '<option value="'.$row['CountryID'].'">'.$row['CountryName'].'</option>';
                                }
                                
                            }
                        ?>
                    </select>

                    <label for="city">City:</label>
                    <input type="text" name="city" id="city" value="<?php echo $result['City']?>">

                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" value="<?php echo $result['Addresse']?>">
                </div>

                <div class="section">
                    <h3 class="section-title">Financial Information</h3>
                    <label for="commissionRate">Commission Rate:</label>
                    <input type="number" id="commissionRate" name="commissionRate" disabled value="<?php echo $result['ComitionRate']?>">

                    <label for="promoCode">Promo Code:</label>
                    <input type="text" id="promoCode" name="promoCode" disabled value="<?php echo $result['PromoCode']?>">

                    <label for="paymentType">Payment Type:</label>
                    <select id="paymentType" name="paymentType">
                        <option value="">[select One]</option>
                        <?php
                            $sql=$con->prepare('SELECT paymentmethodD,methot FROM  tblpayment_method  WHERE paymentmethodD != 2');
                            $sql->execute();
                            $rows=$sql->fetchAll();
                            foreach($rows as $row){
                                if($result['PaymentType'] == $row['paymentmethodD']){
                                    echo '<option value="'.$row['paymentmethodD'].'" selected>'.$row['methot'].'</option>';
                                }else{
                                    echo '<option value="'.$row['paymentmethodD'].'">'.$row['methot'].'</option>';
                                }
                                
                            }
                        ?>
                    </select>
                </div>

                <div class="section">
                    <h3 class="section-title">Additional Notes</h3>
                    <label for="note">Note:</label>
                    <textarea id="note" name="note" rows="4"><?php echo $result['Note']?></textarea>
                </div>
                <div class="btncontrol">
                    <button type="submit" name="btnupdate">Update Profile</button>
                </div>
            </form>
        </div>
        </div>
    <?php
        if(isset($_POST['btnupdate'])){
            $firstName      =$_POST['firstName'];
            $lastName       = $_POST['lastName'];
            $country        = $_POST['country'];
            $city           = $_POST['city'];
            $address        = $_POST['address'];
            $paymentType    = $_POST['paymentType'];
            $note           = $_POST['note'];

            $sql=$con->prepare('UPDATE tblsalesperson SET 
                                        Sale_FName = :Sale_FName,
                                        Sale_LName = :Sale_LName,
                                        Country    = :Country,
                                        City       = :City,
                                        Addresse   = :Addresse,
                                        PaymentType= :PaymentType,
                                        Note       =:Note
                                WHERE   SalePersonID=:SalePersonID ');
            $sql->execute(array(
                'Sale_FName'  => $firstName,
                'Sale_LName'  => $lastName,
                'Country'     => $country,
                'City'        => $city,
                'Addresse'    => $address,
                'PaymentType' => $paymentType,
                'Note'        => $note,
                'SalePersonID'=> $agentId
            ));

            echo '<script>location.href="dashboard.php"</script>';
        }
    ?>
    <?php include '../common/jslinks.php'?>
    <script src="js/updateProfile.js"></script>
    <script src="js/sidebar.js"></script>
</body>