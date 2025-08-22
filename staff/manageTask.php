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

    if(isset($_POST['btnedidreport'])){
        $taskID= $_POST['txttaskID'];
        $newReport = $_POST['newReport'];

        $sql=$con->prepare('UPDATE tbltask SET notes = ?, Status = 3 WHERE taskID=?');
        $sql->execute(array($newReport,$taskID));
        
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
                    $check_task = checkItem('taskID', 'tbltask',$taskID);

                    if($check_task == 1){
                        $sql=$con->prepare('SELECT taskTitle,taskDiscription,BudjectTerms,notes,Status FROM tbltask WHERE taskID = ?');
                        $sql->execute(array($taskID));
                        $task_info = $sql->fetch();
                    ?>
                        <div class="newform">
                            <div class="title_form">
                                <h3><?= $task_info['taskTitle']?></h3>
                            </div>
                            <?php
                            

                            try {
                                $sql = "SELECT
                                            CONCAT(a.admin_FName, ' ', a.admin_LName) AS assignFrom,
                                            CONCAT(s.Fname, ' ', s.LName) AS assignTo,
                                            t.StartDate,
                                            t.DueDate,
                                            t.ProjectID,
                                            t.communicationChannel,
                                            t.taskID,
                                            st.Status_name AS status,
                                            t.taskTitle,
                                            t.Status,
                                            t.ProjectID,
                                            p.project_Name,
                                            CASE 
                                                WHEN t.ProjectID = 0 THEN 'Not Related Project' 
                                                ELSE p.project_Name 
                                            END AS project_name,
                                            CASE 
                                                WHEN t.ProjectID = 0 THEN '' 
                                                ELSE CONCAT(c.Client_FName, ' ', c.Client_LName) 
                                            END AS client_name
                                        FROM tbltask t
                                        LEFT JOIN tbladmin a ON a.admin_ID = t.assignFrom
                                        LEFT JOIN tblstaff s ON s.staffID = t.Assign_to
                                        LEFT JOIN tblprojects p ON p.ProjectID = t.ProjectID
                                        LEFT JOIN tblclients c ON c.ClientID = p.ClientID
                                        LEFT JOIN tblstatus_task_freelancer st ON st.StatusID = t.Status
                                        WHERE t.taskID = :taskID
                                        LIMIT 1";

                                $stmt = $con->prepare($sql);
                                $stmt->bindParam(':taskID', $taskID, PDO::PARAM_INT);
                                $stmt->execute();

                                $task = $stmt->fetch(PDO::FETCH_ASSOC);

                                if ($task) {
                                    // Output example
                                    echo '<div class="generaglinfo">
                                            <div class="oneline">
                                                <div class="data_one">
                                                    <label>Assign From :</label>
                                                    <span>' . htmlspecialchars($task['assignFrom']) . '</span>
                                                </div>
                                                <div class="data_one">
                                                    <label>Start Date</label>
                                                    <span>' . htmlspecialchars($task['StartDate']) . '</span>
                                                </div>
                                            </div>
                                            <div class="oneline">
                                                <div class="data_one">
                                                    <label>Assign To :</label>
                                                    <span>' . htmlspecialchars($task['assignTo']) . '</span>
                                                </div>
                                                <div class="data_one">
                                                    <label>Due Date</label>
                                                    <span>' . htmlspecialchars($task['DueDate']) . '</span>
                                                </div>
                                            </div>
                                            <div class="oneline">
                                                <div class="data_one">
                                                    <label>Project</label>
                                                    <span>' . htmlspecialchars($task['project_name']) . '</span>
                                                </div>
                                                <div class="data_one">
                                                    <label>Finish Date:</label>
                                                    <span>' . htmlspecialchars($task['DueDate']) . '</span>
                                                </div>
                                            </div>
                                            <div class="oneline">
                                                <div class="data_one">
                                                    <label>Commmunicat.</label>
                                                    <span>' . htmlspecialchars($task['communicationChannel']) . '</span>
                                                </div>
                                                <div class="data_one">
                                                    <label>Status</label>
                                                    <span>' . htmlspecialchars($task['status']) . '</span>
                                                </div>
                                            </div>
                                        </div>';
                                } else {
                                    echo "No task found.";
                                }

                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                            }
                            ?>
                            <div class="longtext">
                                <label for="">Discription</label>
                                <p><?php echo nl2br($task_info['taskDiscription']) ?></p>
                            </div>
                            <div class="longtext">
                                <label for="">Budget & Payment Terms</label>
                                <p><?php echo nl2br($task_info['BudjectTerms']) ?></p>
                            </div>
                            <div class="longtext">
                                <label for="">Freelancer Report</label>
                                <p><?php echo nl2br($task_info['notes']) ?></p>
                                <button id="btnedidReord" class="btn btn-warning">Edid Report</button>
                            </div>
                            
                            <div class="btncontrol">
                                <?php
                                    if($task_info['Status'] == 1){
                                        echo '  <button class="btn btn-success btnAccepttask" data-index="'.$taskID.'">Accept </button>
                                                <button class="btn btn-danger btncanceltask" data-index="'.$taskID.'">Cancel Task</button>';
                                    }elseif($task_info['Status'] == 2){
                                        echo '  <button class="btn btn-success btnFInishtask" data-index="'.$taskID.'">Finish </button>
                                                <button class="btn btn-danger btncanceltask" data-index="'.$taskID.'">Cancel Task</button>';
                                    }elseif($task_info['Status'] == 3){
                                        echo '  <button class="btn btn-success btnFinishtask" data-index="'.$taskID.'">Finish </button>
                                                <button class="btn btn-danger btncanceltask" data-index="'.$taskID.'">Cancel Task</button>';
                                    }elseif($task_info['Status'] == 4){
                                        echo '<span class="alert alert-success"> The Task is Finish </span>';
                                    }elseif($task_info['Status'] == 5){
                                        echo '<span class="alert alert-danger"> The Task is Canceled </span>';
                                    }
                                ?>
                                
                            </div>
                        </div>
                        
                    <?php
                    }else{
                        echo '<script> location.href="manageTask.php"</script>';
                    }

                }elseif($do=='finish'){
                    $taskID= (isset($_GET['task']))?$_GET['task']:0;
                    $check_task = checkItem('taskID', 'tbltask',$taskID);

                    if($check_task == 1){
                        $today =date("Y-m-d");
                        $sql=$con->prepare('UPDATE tbltask SET Status=4,FinishDate=?  WHERE taskID = ?');
                        $sql->execute(array($today,$taskID));
                        echo '<script> location.href="manageTask.php"</script>';
                    }else{
                        echo '<script> location.href="manageTask.php"</script>';
                    }    
                }elseif($do=='accepted'){
                    $taskID= (isset($_GET['task']))?$_GET['task']:0;
                    $check_task = checkItem('taskID', 'tbltask',$taskID);

                    if($check_task == 1){
                        $sql=$con->prepare('UPDATE tbltask SET Status=2  WHERE taskID = ?');
                        $sql->execute(array($taskID));
                        echo '<script> location.href="manageTask.php"</script>';
                    }else{
                        echo '<script> location.href="manageTask.php"</script>';
                    }                     
                }elseif($do=='cancel'){
                    $taskID= (isset($_GET['task']))?$_GET['task']:0;
                    $check_task = checkItem('taskID', 'tbltask',$taskID);

                    if($check_task == 1){
                        $sql=$con->prepare('UPDATE tbltask SET Status=5  WHERE taskID = ?');
                        $sql->execute(array($taskID));
                        echo '<script> location.href="manageTask.php"</script>';
                    }else{
                        echo '<script> location.href="manageTask.php"</script>';
                    }
                }else{
                    header('location:index.php');
                }
            ?>
        </div>
    </main>
    <div class="popupreport">
        <div class="conteiner_report">
            <div class="closepopup_report">+</div>
            <form action="" method="post">
                <h3>Freelancer Report</h3>
                <p>Please write your work in Daitail that the admin know what you make</p><input type="text" name="txttaskID" id="txttaskID" hidden>
                <textarea name="newReport" id="newReport" cols="120" rows="10"></textarea>
                <div class="btncontrol">
                    <button type="submit" class="btn btn-success" name="btnedidreport">Save</button>
                </div> 
            </form>
        </div>
    </div>
    <?php include '../common/jslinks.php' ?>
    <script src="js/manageTask.js"></script>
</body>