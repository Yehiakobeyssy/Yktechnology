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
    <link rel="stylesheet" href="css/ManageService.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
    <?php include 'include/navbar.php' ?>
    <div class="containerticket">
        <div class="title">
            <h1>Services</h1>
        </div>  
        <?php
            $do=(isset($_GET['do']))?$_GET['do']:'manage';
            if($do=='manage'){?>
            <div class="containertable">
                <div class="searchsection">
                    <div class="searchinput">
                        <label for="">Search :</label>
                        <input type="text" name="" id="txtsearch">
                    </div>
                </div>
                <div class="table">
                    <table>
                        <thead>
                            <td>Service / Product</td>
                            <td>Price</td>
                            <td>Renewal Date</td>
                            <td>Status</td>
                        </thead>
                        <tbody class="bodyService">
                        </tbody>
                    </table>
                </div>
                <div class="conclujen">
                    <label for="">Total Service : <span id="countService"></span></label>
                </div>
            </div>
            <?php
            }else{  
                header('location:index.php');
            }
        ?>
    </div>
    <?php include '../common/jslinks.php' ?>
    <script src="js/ManageService.js"></script>
    <script src="js/navbar.js"></script>
</body>