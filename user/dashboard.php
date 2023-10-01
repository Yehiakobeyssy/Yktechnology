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
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
    <?php include 'include/navbar.php' ?>
    <div class="card_user">
        <div class="userinfo">
            <?php
                $sql=$con->prepare('SELECT Client_FName,Client_LName,Client_addresse,Client_city,CountryName 
                                    FROM  tblclients 
                                    INNER JOIN  tblcountrys ON tblclients.Client_country = tblcountrys.CountryID
                                    WHERE ClientID=?');
                $sql->execute(array($clientId));
                $result=$sql->fetch();
            ?>
            <h3><?php echo $clientName ?> <span>| <span id="user_Balance"></span></span></h3>
            <h1>Your Dashboard</h1>
            <label for=""><?php echo $result['Client_addresse'] ?></label><br>
            <label for=""><?php echo $result['CountryName'] ?> <span>| <?php echo $result['Client_city'] ?></span></label> <a href=""><i class="fa-solid fa-pen"></i></a>
        </div>
        <div class="dashboradimg">
            <img src="../images/synpoles/userDashboard.png" alt="" srcset="">
        </div>
    </div>
    <div class="statistic">
        <div class="card_satstic card1">
            <img src="../images/synpoles/Services.png" alt="" srcset="">
            <h4>Services</h4>
            <?php
                $sql=$con->prepare('SELECT ServicesID FROM tblclientservices WHERE serviceStatus<4 AND ClientID=?');
                $sql->execute(array($clientId));
                $countService= $sql->rowCount();
            ?>
            <h1><?php echo $countService ?></h1>
        </div>
        <div class="card_satstic card2">
            <img src="../images/synpoles/Domain.png" alt="" srcset="">
            <h4>Domains</h4>
            <h1>5</h1>
        </div>
        <div class="card_satstic card3">
            <img src="../images/synpoles/ticket.png" alt="" srcset="">
            <h4>Tickets</h4>
            <?php
                $sql=$con->prepare('SELECT ticketID FROM  tblticket WHERE ticketStatus <> 4 AND ClientID = ?');
                $sql->execute(array($clientId));
                $countticket=$sql->rowCount();
            ?>
            <h1><?php echo $countticket ?></h1>
        </div>
        <div class="card_satstic card4">
            <img src="../images/synpoles/Invoices.png" alt="" srcset="">
            <h4>Invoices</h4>
            <?php
                $sql=$con->prepare('SELECT InvoiceID FROM tblinvoice WHERE Invoice_Status=1 AND ClientID = ?');
                $sql->execute(array($clientId));
                $countInvoice=$sql->rowCount();
            ?>
            <h1><?php echo $countInvoice ?></h1>
        </div>
    </div>
    <?php include '../common/jslinks.php' ?>
    <script src="js/dashboard.js"></script>
    <script src="js/navbar.js"></script>
</body>