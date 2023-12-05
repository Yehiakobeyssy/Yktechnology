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

    $serviseID= (isset($_GET['id']))?$_GET['id']:0;
    $checkservice = checkItem('ServicesID','tblclientservices',$serviseID);
    if($checkservice == 1){
        $sql=$con->prepare('SELECT *,Status,Status_Color
                            FROM tblclientservices 
                            INNER JOIN tblstatusservices ON tblstatusservices.StatusSerID = tblclientservices.serviceStatus
                            WHERE ServicesID=?');
        $sql->execute(array($serviseID));
        $serviceInfo =$sql->fetch(); 
    }else{
        echo '<script> location.href="index.php" </script>';
    }
?>
    <link rel="stylesheet" href="css/viewService.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
    <?php include 'include/navbar.php' ?>
    <div class="containerService">
        <div class="tilte">
            <h3>Manage Service</h3>
        </div>
        <div class="titleService">
            <div class="infoservice">
                <?php
                    $sql=$con->prepare('SELECT Service_Name,DurationName 
                                        FROM tblservices 
                                        INNER JOIN tblduration ON tblduration.DurationID = tblservices.Duration
                                        WHERE ServiceID = ?');
                    $sql->execute(array($serviceInfo['ServiceID']));
                    $resultS = $sql->fetch();
                ?>
                <h1><?php echo $resultS['Service_Name'] ?></h1>
                <h3><?php echo $serviceInfo['ServiceTitle'] ?></h3>
            </div>
            <div class="info_Price">
                <h1><?php echo number_format($serviceInfo['Price'],2,'.','').' $' ?></h1>
                <label for=""><?php echo $resultS['DurationName'] ?></label>
            </div>
        </div>
        <div class="general_info">
            <table>
                <tr>
                    <td class="genegalinfo"><label for="">Service Domain Name : </label></td>
                    <td class="result"><span><?php echo $serviceInfo['ServiceDomain'] ?>sdssdsd</span></td>
                </tr>
                <tr>
                    <td class="genegalinfo"><label for="">This service is specialized for</label></td>
                    <td class="result"><span><?php echo $serviceInfo['forwhat'] ?></span></td>
                </tr>
                <tr>
                    <td class="genegalinfo"><label for="">Colors use </label></td>
                    <td class="result"><span><?php echo $serviceInfo['Colors'] ?></span></td>
                </tr>
            </table>
        </div>
        <div class="discription">
            <label for="">Discription</label>
            <p style="text-decoration:none"><?php echo nl2br($serviceInfo['Discription']) ?></p>
        </div>
        <div class="athathment">
            <a href="../Documents/<?php echo $serviceInfo['filename'] ?>" download=""><i class="fa-solid fa-paperclip"></i>  <?php echo $serviceInfo['filename'] ?></a>
        </div>
        <div class="final">
            <div class="status">
                <!-- Status,Status_Color -->
                <h1 style="color:<?php echo $serviceInfo['Status_Color']?>"><?php echo $serviceInfo['Status']?></h1>
            </div>
        </div>
    </div>
    <?php include '../common/jslinks.php' ?>
    <script src="js/viewService.js"></script>
    <script src="js/navbar.js"></script>
</body>