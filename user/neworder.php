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

    $sql=$con->prepare('SELECT Client_FName,Client_LName,client_active FROM  tblclients WHERE ClientID=?');
    $sql->execute(array($clientId));
    $clientinfo = $sql->fetch();
    $clientName = $clientinfo['Client_FName'].' '. $clientinfo['Client_LName'];

    if($clientinfo['client_active'] == 0){
        setcookie("user","",time()-3600);
        unset($_SESSION['user']);
        echo '<script> location.href="index.php" </script>';
    }
?>
    <link rel="stylesheet" href="css/neworder.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body> 
    <?php include 'include/navbar.php' ?>
    <div class="container_category">
        <div class="title_cat">
            <h3>Services</h3>
        </div>
        <div class="allservice">
            <?php
                $sql=$con->prepare('SELECT Cat_ID,Category_Icon,Category_Name,Cat_Discription FROM tblcategory WHERE Cat_Active=1');
                $sql->execute();
                $Services=$sql->fetchAll();
                foreach($Services as $service){
                    echo '
                        <div class="card_service" data-index="'.$service['Cat_ID'].'">
                            <img src="../images/Services/'.$service['Category_Icon'].'" alt="">
                            <div class="dis">
                                <h5>'.$service['Category_Name'].'</h5>
                                <label for="">'.$service['Cat_Discription'].'</label>
                            </div>
                        </div>
                    ';
                }
            ?>
        </div>
    </div>
    <?php include '../common/jslinks.php' ?>
    <script src="js/neworder.js"></script>
    <script src="js/navbar.js"></script>
</body>