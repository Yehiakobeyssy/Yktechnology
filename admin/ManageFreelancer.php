<?php
    session_start();

    if(!isset($_COOKIE['useradmin'])){
        if(!isset($_SESSION['useradmin'])){
            header('location:index.php');
        }
    }
    $adminId= (isset($_COOKIE['useradmin']))?$_COOKIE['useradmin']:$_SESSION['useradmin'];

    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';

    $sql=$con->prepare('SELECT admin_active,admin_FName,admin_LName FROM  tbladmin WHERE admin_ID=?');
    $sql->execute(array($adminId));
    $result=$sql->fetch();
    $isActive=$result['admin_active'];
    $firstname= $result['admin_FName'];
    $lastName = $result['admin_LName'];
    $full_name = $firstname .' ' . $lastName ;

    if($isActive == 0){
        setcookie("useradmin","",time()-3600);
        unset($_SESSION['useradmin']);
        echo '<script> location.href="index.php" </script>';
    }

    $do=isset($_GET['do'])?$_GET['do']:'manage';
?>
    <link rel="stylesheet" href="css/ManageFreelancer.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1> <i class="fa-solid fa-user-tie"></i> Freelancers</h1>
                <button class="btn btn-primary btnPosstion">Posstion</button>
            </div>
            <?php
                if($do=='manage'){?>
                    <div class="statistic">
                        <?php
                            $sql=$con->prepare('SELECT COUNT(staffID) AS count_staff FROM tblstaff ');
                            $sql->execute();
                            $result = $sql->fetch();
                            $total_Freelancere= $result['count_staff'];

                            $sql=$con->prepare('SELECT COUNT(staffID) AS Accepted FROM tblstaff WHERE accepted= 1 AND block = 0');
                            $sql->execute();
                            $result = $sql->fetch();
                            $Accepted= $result['Accepted'];

                            $sql=$con->prepare('SELECT COUNT(staffID) AS notAccepted FROM tblstaff WHERE accepted= 0 AND block = 0');
                            $sql->execute();
                            $result = $sql->fetch();
                            $not_Accepted= $result['notAccepted'];

                            $sql=$con->prepare('SELECT COUNT(staffID) AS Blocked FROM tblstaff WHERE  block = 1');
                            $sql->execute();
                            $result = $sql->fetch();
                            $blocked= $result['Blocked'];
                        ?>  
                        <i class="fa-solid fa-user-tie"></i>
                        <h3>Total Freelanncers</h3>
                        <h2><?php echo $total_Freelancere ?></h2>
                        <div class="numbers">
                            <div class="number_display">
                                <label for="">Accepted</label>
                                <span><?php echo $Accepted ?></span>
                            </div>
                            <div class="number_display">
                                <label for="">Not accepted</label>
                                <span><?php echo $not_Accepted ?></span>
                            </div>
                            <div class="number_display">
                                <label for="">Blocked</label>
                                <span><?php echo $blocked ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="tableview">
                        <div class="searchbox">
                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                <circle cx="10.3054" cy="10.3055" r="7.49047" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15.5151 15.9043L18.4518 18.8333" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <input type="text" name="" id="txtserarchFree">
                        </div>
                        <table>
                            <thead>
                                <th>Freelancers</th>
                                <th>Address</th>
                                <th>Role</th>
                                <th>Expected sal.</th>
                                <th>Status</th>
                                <th>Control</th>
                            </thead>
                            <tbody class="viewFrelancer">

                            </tbody>
                        </table>
                    </div>
                <?php
                }elseif($do=='view'){

                }elseif($do =='accepted'){

                }elseif($do=='blocked'){

                }
            ?>
            
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/ManageFreelancer.js"></script>
    <script src="js/sidebar.js"></script>
</body>