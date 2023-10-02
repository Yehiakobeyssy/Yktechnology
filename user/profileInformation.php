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

    $sql=$con->prepare('SELECT * FROM  tblclients WHERE ClientID=?');
    $sql->execute(array($clientId));
    $clientinfo = $sql->fetch();
    $clientName = $clientinfo['Client_FName'].' '. $clientinfo['Client_LName'];

    if($clientinfo['client_active'] == 0){
        setcookie("user","",time()-3600);
        unset($_SESSION['user']);
        echo '<script> location.href="index.php" </script>';
    }
?>
    <link rel="stylesheet" href="css/profileInformation.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
    <?php include 'include/navbar.php' ?>
    <div class="container_profile">
        <div class="title">
            <h1>Proile user (<?php echo  $clientName ?>)</h1>
        </div>
        <form action="" method="post">
            <div class="personal_info">
                <h3>Personal Information</h3>
                <table>
                    <tr>
                        <td><label for="">Full Name</label></td>
                        <td><input type="text" name="Client_FName" id="" placeholder="First Name" value="<?php echo $clientinfo['Client_FName']  ?>"></td>
                        <td><input type="text" name="Client_LName" id="" placeholder="Last Name" value="<?php echo $clientinfo['Client_LName']  ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="">E-mail</label></td>
                        <td colspan="2"><input type="email" name="Client_email" id="" value="<?php echo $clientinfo['Client_email']  ?>" disabled></td>
                    </tr>
                    <tr>
                        <td><label for="">Phone Number</label></td>
                        <td colspan="2"><input type="text" name="Client_Phonenumber" id="" placeholder="Phone Number" value="<?php echo $clientinfo['Client_Phonenumber'] ?>" ></td>
                    </tr>
                </table>
            </div>
            <div class="invoiceAdd">
                <h3>invoice Address</h3>
                <input type="text" name="Client_companyName" id="" placeholder="Company Name (optional)" value="<?php echo $clientinfo['Client_companyName'] ?>">
                <input type="text" name="Client_addresse" id="" placeholder="Addresse" value="<?php echo $clientinfo['Client_addresse'] ?>">
                <div class="city">
                    <input type="text" name="Client_city" id="" placeholder="City" value="<?php echo $clientinfo['Client_city'] ?>">
                    <input type="text" name="Client_Region" id="" placeholder="Region" value="<?php echo $clientinfo['Client_Region'] ?>">
                    <input type="text" name="Client_zipcode" id="" placeholder="Zipcode" value="<?php echo $clientinfo['Client_zipcode'] ?>">
                </div>
                <select name="Client_country" id="" required>
                    <option value="">[Country]</option>
                    <?php
                        $sql=$con->prepare('SELECT * FROM tblcountrys');
                        $sql->execute();
                        $countrys=$sql->fetchAll();
                        foreach($countrys as $country){
                            if($clientinfo['Client_country'] == $country['CountryID']){
                                echo '<option value="'.$country['CountryID'].'" selected>'.$country['CountryName'].'</option>';
                            }else{
                                echo '<option value="'.$country['CountryID'].'">'.$country['CountryName'].'</option>';
                            }
                            
                        }
                    ?>
                </select>
            </div>
            <div class="control">
                <a href="changePassword.php" class="btn btn-secondary" >Change Password</a>
                <button type="submit" class="btn btn-warning" name="btnupdate">Update Data</button>
            </div>
            <?php
                if(isset($_POST['btnupdate'])){
                    $Client_FName           =$_POST['Client_FName'];
                    $Client_LName           =$_POST['Client_LName'];
                    $Client_Phonenumber     =$_POST['Client_Phonenumber'];
                    $Client_companyName     =$_POST['Client_companyName'];
                    $Client_addresse        =$_POST['Client_addresse'];
                    $Client_city            =$_POST['Client_city'];
                    $Client_Region          =$_POST['Client_Region'];
                    $Client_zipcode         =$_POST['Client_zipcode'];
                    $Client_country         =$_POST['Client_country'];

                    $sql=$con->prepare('UPDATE tblclients 
                                        SET     Client_FName        = :Client_FName,
                                                Client_LName        = :Client_LName,
                                                Client_Phonenumber  = :Client_Phonenumber,
                                                Client_companyName  = :Client_companyName,
                                                Client_addresse     = :Client_addresse,
                                                Client_city         = :Client_city,
                                                Client_Region       = :Client_Region,
                                                Client_zipcode      = :Client_zipcode,
                                                Client_country      = :Client_country
                                        WHERE   ClientID            = :ClientID ');
                    $sql->execute(array(
                        'Client_FName'          =>$Client_FName,
                        'Client_LName'          =>$Client_LName,
                        'Client_Phonenumber'    =>$Client_Phonenumber,
                        'Client_companyName'    =>$Client_companyName,
                        'Client_addresse'       =>$Client_addresse,
                        'Client_city'           =>$Client_city,
                        'Client_Region'         =>$Client_Region,
                        'Client_zipcode'        =>$Client_zipcode,
                        'Client_country'        =>$Client_country,
                        'ClientID'              =>$clientId 
                    ));
                    
                    echo '<script> location.href ="dashboard.php" </script>';
                }
            ?>
        </form>
    </div>
    <?php include '../common/jslinks.php' ?>
    <script src="js/profileInformation.js"></script>
    <script src="js/navbar.js"></script>
</body>