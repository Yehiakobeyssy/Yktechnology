<?php
    include 'settings/connect.php';
    include 'common/function.php';
    include 'common/head.php';

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
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="services.css">
</head>
<body>
    <header>
        <div class="shopinfo">
            <img src="images/logo.png" alt="">
            <h2>YK-Technology</h2>
        </div>
        <div class="navbar">
            <a href="index.php"><i class="fa-solid fa-house"></i></a>
            <a href="user/index.php"><i class="fa-solid fa-cart-shopping"></i> <span id="count_cart"></span></a>
        </div>
    </header>
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
    <?php include 'common/jslinks.php'?>
    <script src="services.js"></script>
</body>