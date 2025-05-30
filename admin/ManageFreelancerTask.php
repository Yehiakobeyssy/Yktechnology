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
    <link rel="stylesheet" href="css/ManageFreelancerTask.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1> Task Manager </h1>
                <a href="ManageFreelancerTask.php?do=add" class="btn btn-success btnnewtask"> + New Task </a>
            </div>
            <?php
                if($do=='manage'){?>
                    <div class="statistic">
                        <?php
                            $sql = $con->prepare('SELECT 
                                                    COUNT(taskID) AS total,
                                                    SUM(Status = 4) AS finished,
                                                    SUM(Status BETWEEN 1 AND 3) AS working,
                                                    SUM(Status = 5) AS canceled
                                                FROM tbltask');
                            $sql->execute();
                            $stats = $sql->fetch();
                        ?>

                        <i class="fa-solid fa-calendar-check icon"></i>
                        <h4>All Tasks</h4>
                        <span><?= $stats['total'] ?></span>

                        <div class="numbers">
                            <div class="number_display">
                                <label>Finished</label>
                                <span><?= $stats['finished'] ?></span>
                            </div>
                            <div class="number_display">
                                <label>Working</label>
                                <span><?= $stats['working'] ?></span>
                            </div>
                            <div class="number_display">
                                <label>Canceled</label>
                                <span><?= $stats['canceled'] ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="tableview">
                        <div class="searchbox">
                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                <circle cx="10.3054" cy="10.3055" r="7.49047" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15.5151 15.9043L18.4518 18.8333" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <input type="text" name="" id="txtSearchTask" placeholder="Search tasks..." >
                        </div>
                        <table>
                            <thead>
                                <th>Project</th>
                                <th>Assginment</th>
                                <th>Title</th>
                                <th>Dates</th>
                                <th>Communication</th>
                                <th>Status</th>
                                <th>Control</th>
                            </thead>
                            <tbody class="viewtask">

                            </tbody>
                        </table>
                    </div>
                <?php
                }elseif($do=='view'){
                    $taskID = isset($_GET['task'])?$_GET['task']:0;
                    $checkTask= checkItem('taskID','tbltask',$taskID);

                    if($checkTask == 1){
                        $sql=$con->prepare('SELECT taskTitle,taskDiscription,BudjectTerms,notes FROM tbltask WHERE taskID = ?');
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
                            </div>
                            <div class="btncontrol">
                                <button class="btn btn-danger btncanceltask" data-index="<?=$taskID?>">Cancel Task</button>
                            </div>
                        </div>
                        
                    <?php
                    }else{
                        echo '<script> location.href="ManageFreelancerTask.php" </script>';
                    }
                }elseif($do=='add'){
                    $freeID = isset($_GET['freeID'])?$_GET['freeID']:0;
                ?>
                <div class="newform">
                    <form action="" method="post">
                        <div class="title_form">
                            <h3>Freelancer Task Assignment Form</h3>
                        </div>
                        <div class="tiletaske">
                            <label for="">Task Title</label>
                            <input type="text" name="taskTitle" id=""  required>
                            <label for="">Project Name</label>
                            <select name="ProjectID" id="">
                                <option value="0">[Not Releted]</option>
                                <?php 
                                    $sql=$con->prepare('SELECT ProjectID,project_Name
                                                        FROM tblprojects');
                                    $sql->execute();
                                    $projects = $sql->fetchAll();
                                    foreach($projects as $pro){
                                        echo '<option value="'.$pro['ProjectID'].'">'.$pro['project_Name'].'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="assginment">
                            <div class="newassgiment">
                                <label for="">Assigned From</label>
                                <select name="assignFrom_temp" disabled>
                                    <?php
                                        $sql=$con->prepare('SELECT admin_ID,admin_FName,admin_LName FROM tbladmin ORDER BY admin_FName');
                                        $sql->execute();
                                        $admins = $sql->fetchAll();
                                        foreach ($admins as $admin) {
                                            $selected = ($admin['admin_ID'] == $adminId) ? 'selected' : '';
                                            echo '<option value="' . $admin['admin_ID'] . '" ' . $selected . '>'
                                                    . $admin['admin_FName'] . ' ' . $admin['admin_LName'] .
                                                    '</option>';
                                        }
                                    ?>
                                </select>
                                <input type="hidden" name="assignFrom" value="<?php echo $adminId; ?>">
                            </div>
                            <div class="newassgiment">
                                <label for="">Assigned To</label>
                                <select name="Assign_to" id="" required>
                                    <option value="0">[Select Freelancer]</option>
                                    <?php
                                        $sql=$con->prepare('SELECT staffID,Fname,MidelName,LName FROM tblstaff WHERE block=0 AND accepted = 1 ORDER BY Fname');
                                        $sql->execute();
                                        $freelancers =$sql->fetchAll();
                                        foreach($freelancers as $free){
                                            $selected = ($free['staffID'] == $freeID) ? 'selected' : '';
                                            echo '<option value="' . $free['staffID'] . '"  ' . $selected . ' >'
                                                    . $free['Fname'] . ' ' . $free['MidelName'] . ' '.$free['LName'].
                                                    '</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="task_discription">
                            <label for="">Description</label>
                            <textarea name="taskDiscription" id="" rows="7"></textarea>
                        </div>
                        <div class="dates">
                            <div class="newdate">
                                <label for="">Start Date</label>
                                <input type="date" name="StartDate" id="" required>
                            </div>
                            <div class="newdate">
                                <label for="">Due Date</label>
                                <input type="date" name="DueDate" id="" required>
                            </div>
                            <div class="newdate">
                                <label for="">Finish Date</label>
                                <input type="date" name="FinishDate" id="" disabled>
                            </div>
                        </div>
                        <div class="budget">
                            <label for="">Budget & Payment Terms</label>
                            <textarea name="BudjectTerms" id="" rows="5"></textarea>
                        </div>
                        <div class="Communication">
                            <label for="">Communication Channel</label>
                            <input type="text" name="communicationChannel" id="" placeholder="Like email or phone or whatsup ">
                        </div>
                        <div class="Freelancer_Report">
                            <label for="">Freelancer Report</label>
                            <textarea name="notes" id="" rows="7" disabled></textarea>
                        </div>
                        <div class="ctrl">
                            <button type="reset" class="btn btn-danger">Reset</button>
                            <button type="submit" class="btn btn-success" name="btnsendTask">Sent Task</button>
                        </div>
                    </form>
                    <?php
                        if(isset($_POST['btnsendTask'])){
                            $acceptedbyfreelancer = 0;
                            $Status = 1;

                            $sql=$con->prepare('INSERT INTO  tbltask (assignFrom,Assign_to,taskTitle,ProjectID,taskDiscription,StartDate,DueDate,BudjectTerms,communicationChannel,notes,Finish,acceptedbyfreelancer,Status) 
                                                VALUES (:assignFrom,:Assign_to,:taskTitle,:ProjectID,:taskDiscription,:StartDate,:DueDate,:BudjectTerms,:communicationChannel,:notes,:Finish,:acceptedbyfreelancer,:Status) ');
                            $sql->execute(array(
                                'assignFrom'            => $_POST['assignFrom'],
                                'Assign_to'             => $_POST['Assign_to'],
                                'taskTitle'             => $_POST['taskTitle'],
                                'ProjectID'             => $_POST['ProjectID'],
                                'taskDiscription'       => $_POST['taskDiscription'],
                                'StartDate'             => $_POST['StartDate'],
                                'DueDate'               => $_POST['DueDate'],
                                'BudjectTerms'          => $_POST['BudjectTerms'],
                                'communicationChannel'  => $_POST['communicationChannel'],
                                'notes'                 => '',
                                'Finish'                => 0,
                                'acceptedbyfreelancer'  => $acceptedbyfreelancer,
                                'Status'                => $Status 
                            ));


                        }
                    ?>
                </div>
                <?php
                }elseif($do=='cancel'){
                    $taskID = isset($_GET['task'])?$_GET['task']:0;
                    $checkTask= checkItem('taskID','tbltask',$taskID);
                    if($checkTask == 1){?>
                        <div class="blocksection alert alert-danger">
                            <h3>Are You shure to Cancel task (<?= $taskID ?>)</h3>
                            <form action="" method="post">
                                <button class="btn btn-danger btntaskno">No</button>
                                <button class="btn btn-success" type="submit" name="btntaskyes">yes</button>
                            </form>
                        </div>
                    <?php
                        if(isset($_POST['btntaskyes'])){
                            $sql=$con->prepare('UPDATE  tbltask SET Status = 5 WHERE taskID = ?');
                            $sql->execute(array($taskID));
                            echo '<script> location.href="ManageFreelancerTask.php" </script>';
                        }
                    }else{
                        echo '<script> location.href="ManageFreelancerTask.php" </script>';
                    }
                }else{
                    echo '<script> location.href="ManageFreelancerTask.php" </script>';
                }
            ?>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/ManageFreelancerTask.js"></script>
    <script src="js/sidebar.js"></script>
</body>