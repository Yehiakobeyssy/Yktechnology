<?php
    session_start();

    if(!isset($_COOKIE['staff'])){
        if(!isset($_SESSION['staff'])){
            header('location:index.php');
        }
    }
    $staff_Id= (isset($_COOKIE['staff']))?$_COOKIE['staff']:$_SESSION['staff'];

    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';


    $sql=$con->prepare('SELECT accepted , block FROM tblstaff WHERE staffID  = ?');
    $sql->execute(array($staff_Id));
    $result= $sql->fetch();
    $accepted = $result['accepted'];
    $block = $result['block'];

    if($block == 1){
        $do = 'blocked';
    }else{
        
        if($accepted == 1){
            $do = 'accepted';
        }else{
            $do ='waiting';
        }
    }
?>
    <link rel="stylesheet" href="css/checkstaff.css">
</head>
<body>
    <?php 
        if($do == 'accepted'){
            header('location:dashbord.php');
        }elseif($do == 'waiting'){?>
            <div class="discution">
                <img src="../images/synpoles/Waitning.png" alt="" srcset="">
                <p>
                    Your profile is currently under review by our administration team. 
                    We are carefully studying your information to ensure everything is in order. 
                    Once your profile is approved, you will receive an email notification with 
                    further instructions to begin your work.
                </p>
                <button class="btngohome">Back home</button>
            </div>
        <?php 
        }elseif($do == 'blocked'){?>
            <div class="discution">
                <img src="../images/synpoles/Blocked.png" alt="" srcset="">
                <p>
                    Your profile review has been completed. Unfortunately, 
                    we found that the information provided does not meet our 
                    requirements or policies, or contains mistakes. 
                    Please review your data and correct any errors. 
                    If you believe this decision is incorrect, 
                    you may contact our support team for assistance.
                </p>
                <button class="btngohome">Back home</button>
            </div>
        <?php
        }else{
            header('location:../index.php');
        }
    ?>
    <?php include '../common/jslinks.php' ?>
    <script src="js/checkstaff.js"></script>
</body>