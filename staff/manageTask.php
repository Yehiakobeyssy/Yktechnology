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
    <link rel="stylesheet" href="css/managetask.css">
</head>
<body>
    <?php include 'include/headerstaff.php' ?>
    <main>
        <?php include 'include/aside.php' ?>
        <div class="task_container">
            <?php 
                if($do == 'manage'){
                        $sql = $con->prepare('SELECT 
                                                COUNT(tbltask.taskID) AS total,
                                                SUM(Status = 1) AS toaccept,
                                                SUM(Status = 4) AS finished,
                                                SUM(Status BETWEEN 2 AND 3) AS working,
                                                SUM(Status = 5) AS canceled
                                            FROM  tbltask
                                            WHERE Assign_to = ?');
                        $sql->execute(array($staff_Id));
                        $stats = $sql->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <div class="statistic">
                        <i class="fas fa-tasks"></i></i>
                        <h3>Total Tasks</h3>
                        <h2><?= $stats['total'] ?? 0 ?></h2>
                        <div class="numbers">
                            <div class="number_display">
                                <label for="">Complete</label>
                                <span><?= $stats['finished'] ?? 0 ?></span>
                            </div>
                            <div class="number_display">
                                <label for="">to Accept</label>
                                <span><?= $stats['toaccept'] ?? 0 ?></span>
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
                            <input type="text" name="" id="txtserarchTask" placeholder="Search ...">
                        </div>
                        <table>
                            <thead>
                                <th>#</th>
                                <th>Manager</th>
                                <th>Project </th>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Control</th>
                            </thead>
                            <tbody id=tblTask></tbody>
                        </table>
                    </div>

                <?php
                }elseif($do=='view'){
                    $taskID= (isset($_GET['task']))?$_GET['task']:0;
                    $check_task = checkItem($select, $from, $value);
                    

                }elseif($do=='edid'){

                }elseif($do=='accepted'){

                }elseif($do=='cancel'){

                }else{
                    header('location:index.php');
                }
            ?>
        </div>
    </main>
    <?php include '../common/jslinks.php' ?>
    <script src="js/manageTask.js"></script>
</body>