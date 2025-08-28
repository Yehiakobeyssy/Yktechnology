<?php
    include 'settings/connect.php';
    include 'common/function.php';
    include 'common/head.php';

    $pro=(isset($_GET['port']))?$_GET['port']:0;

    if($pro==0){
        header('location:index.php');
    }else{
        $check_pro= checkItem('portfolio_ID','tblportfolio',$pro);
        if($check_pro == 0){
            header('location:index.php');
        }
    }

    $sql=$con->prepare('SELECT * FROM  tblportfolio WHERE portfolio_ID=?');
    $sql->execute(array($pro));
    $portfolio=$sql->fetch();
?>
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="portfolio.css">
</head>
<body>
    <header>
        <div class="shopinfo">
            <img src="images/logo.png" alt="">
            <h2>Kawnex</h2>
        </div>
        <div class="navbar">
            <a href="index.php"><i class="fa-solid fa-house"></i></a>
            <a href="user/index.php"><i class="fa-solid fa-cart-shopping"></i> <span id="count_cart"></span></a>
        </div>
    </header>
    <div class="container_port">
        <div class="gernegal_info">
            <div class="img_pro">
                <img src="images/Profolio/<?php echo $portfolio['portfolio_Pic'] ?>" alt="" srcset="">
            </div>
            <div class="info_pro">
                <h2><?php echo $portfolio['portfolio_Title'] ?></h2>
                <p><span>Duration :</span> <?php echo $portfolio['Duration_working'] ?></p>
                <div class="lag_use">
                    <p>Languge use</p>
                        <div class="use">
                            <?php
                                $languages = explode(" - ", $portfolio['Lan_use']);

                                foreach ($languages as $language) {
                                    echo '<div class="circle"><span>' . $language . '</span></div>';
                                }
                            ?>
                        </div>
                </div>
            </div>
        </div>
        <div class="discription">
            <p><?php echo nl2br($portfolio['Discription']) ?></p>
        </div>
        <div class="go_to_website">
            <?php
                if(!empty($portfolio['linkWebsite'])){?>
                    <a href="https://<?php echo $portfolio['linkWebsite'] ?>" target="_blank" class="btn btn-primary">Live Demo</a>
                <?php
                }
            ?>
            
        </div>
    </div>
    <?php include 'common/jslinks.php'?>
    <script src="portfolio.js"></script>
</body>