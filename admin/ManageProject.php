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
    <link rel="stylesheet" href="css/ManageProject.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1> <i class="fa-solid fa-diagram-project "></i> Project Managment</h1>
                <button class="btn btn-primary btnPosstion">New Project</button>
            </div>
            <?php
                if($do == 'manage'){?>
                    <?php
                        $sql = $con->prepare('SELECT 
                                                COUNT(ProjectID) AS total,
                                                SUM(Status = 3) AS finished,
                                                SUM(Status BETWEEN 1 AND 2) AS working,
                                                SUM(Status = 4) AS canceled
                                            FROM tblprojects');
                        $sql->execute();
                        $stats = $sql->fetch(PDO::FETCH_ASSOC);
                    ?>

                    <div class="statistic">
                        <i class="fa-solid fa-diagram-project"></i>
                        <h3>Total Projects</h3>
                        <h2><?= $stats['total'] ?? 0 ?></h2>
                        <div class="numbers">
                            <div class="number_display">
                                <label for="">Complete</label>
                                <span><?= $stats['finished'] ?? 0 ?></span>
                            </div>
                            <div class="number_display">
                                <label for="">Working</label>
                                <span><?= $stats['working'] ?? 0 ?></span>
                            </div>
                            <div class="number_display">
                                <label for="">Canceled</label>
                                <span><?= $stats['canceled'] ?? 0 ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="tableview">
                        <div class="searchbox">
                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                <circle cx="10.3054" cy="10.3055" r="7.49047" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15.5151 15.9043L18.4518 18.8333" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <input type="text" name="" id="txtserarchProject">
                        </div>
                        <table>
                            <thead>
                                <th>Project</th>
                                <th>Manager</th>
                                <th>Services</th>
                                <th>Developers</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Control</th>
                            </thead>
                            <tbody class="viewProject">

                            </tbody>
                        </table>
                    </div>
                <?php
                }elseif($do=='view'){

                }elseif($do=='add'){

                }elseif($do=='edid'){

                }elseif($do=='cancel'){

                }else{
                    echo '<script> location.href="ManageProject.php" </script>';
                }
            ?>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/ManageProject.js"></script>
    <script src="js/sidebar.js"></script>
</body>