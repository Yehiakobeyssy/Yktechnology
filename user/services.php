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
    $category=(isset($_GET['cat']))?$_GET['cat']:0;

    if($category==0){
        header('location:index.php');
    }else{
        $check_category= checkItem('Cat_ID','tblcategory', $category);
        if($check_category == 0){
            header('location:index.php');
        }
    }

    $sql=$con->prepare('SELECT Category_Name FROM tblcategory WHERE Cat_ID=?');
    $sql->execute(array($category));
    $result=$sql->fetch();
    $category_name=$result['Category_Name'];
?>
    <link rel="stylesheet" href="css/services.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
    <?php include 'include/navbar.php' ?>
    <div class="service_container">
        <div class="service_title">
            <div class="info">
                <h1>Services</h1>
                <h5><?php echo $category_name ?></h5>
            </div>
            <div class="search">
                <label for="">search</label>
                <input type="text" name="" id="txtSearch">
            </div>
        </div>  
        <div class="display_services">
        </div>
    </div>
    <?php include '../common/jslinks.php' ?>
    <script src="js/services.js"></script>
    <script src="js/navbar.js"></script>
</body>