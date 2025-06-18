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

    $sql=$con->prepare('SELECT Fname,MidelName,LName,accepted , block FROM tblstaff WHERE staffID  = ?');
    $sql->execute(array($staff_Id));
    $result= $sql->fetch();
    $accepted = $result['accepted'];
    $block = $result['block'];
    $staff_name = $result['Fname'].' '.$result['MidelName'].' '.$result['LName'];

    if($block == 1){
        header('location:index.php');
    }else{
        if($accepted == 0){
            header('location:index.php');
        }
    }

    $do=(isset($_GET['do']))?$_GET['do']:'manage';

?>
    <link rel="stylesheet" href="css/manageProjects.css">
</head>
<body>
    <?php include 'include/headerstaff.php' ?>
    <main>
        <?php include 'include/aside.php' ?>
        <div class="project_container">
            <?php
                if($do=='manage'){
                    $sql = $con->prepare('SELECT 
                                            COUNT(tblprojects.ProjectID) AS total,
                                            SUM(Status = 3) AS finished,
                                            SUM(Status BETWEEN 1 AND 2) AS working,
                                            SUM(Status = 4) AS canceled
                                        FROM tblprojects
                                        INNER JOIN tbldevelopers_project ON tbldevelopers_project.projectID = tblprojects.ProjectID
                                        WHERE tbldevelopers_project.FreelancerID = ?');
                    $sql->execute(array($staff_Id));
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
                            <input type="text" name="" id="txtserarchProject" placeholder="Search ...">
                        </div>
                        <table>
                            <thead>
                                <th>P.Name</th>
                                <th>Client</th>
                                <th>Manager</th>
                                <th>Possition</th>
                                <th>Tasks</th>
                                <th>Status</th>
                                <th>Control</th>
                            </thead>
                            <tbody id=tblprojects></tbody>
                        </table>
                    </div>
                <?php
                }elseif($do=='view'){

                }else{

                }
            ?>


            
        </div>
    </main>
    <?php include '../common/jslinks.php' ?>
    <script src="js/manageProjects.js"></script>
</body>