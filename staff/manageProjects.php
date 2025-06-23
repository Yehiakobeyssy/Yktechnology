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
                    $projectId = (isset($_GET['pid']))?$_GET['pid']:0;
                    $checkProject = checkItem('ProjectID','tblprojects',$projectId);

                    if($checkProject == 1){
                         $sql=$con->prepare('SELECT project_Name,admin_FName,admin_LName,Client_FName,Client_LName,Client_addresse,Client_Phonenumber,
                                                        StartTime,ExpectedDate,EndDate,Discription,shareManagement,shareReserve,note
                                                FROM tblprojects 
                                                INNER JOIN  tbladmin ON  tbladmin.admin_ID  = tblprojects.Project_Manager
                                                INNER JOIN tblclients ON tblclients.ClientID = tblprojects.ClientID
                                                WHERE ProjectID = ?');
                            $sql->execute(array($projectId));
                            $info = $sql->fetch();
                        ?>
                        <div class="titleviewproject">
                            <i class="fa-solid fa-diagram-project"></i>
                            <h3>View Project</h3>
                        </div>
                        <div class="newForm">
                            <div class="formtitle">
                                <h3>Project Form</h3>
                            </div>
                            <div class="long">
                                <label for="">Project Name : </label>
                                <span> <?=$info['project_Name']?></span>
                            </div>
                            <div class="long">
                                <label for="">Project Manager :</label>
                                <span><?= $info['admin_FName'].' ' . $info['admin_LName']?></span>
                            </div>
                            <div class="colmdata">
                                <div class="left">
                                    <div class="double">
                                        <label for="">Client Name :</label>
                                        <span> <?= $info['Client_FName'].' '.$info['Client_LName']?></span>
                                    </div>
                                    <div class="double">
                                        <span><?php echo $info['Client_addresse'] ?></span>
                                    </div>
                                    <div class="double">
                                        <span><?= $info['Client_Phonenumber']?></span>
                                    </div>
                                </div>
                                <div class="right">
                                    <div class="double">
                                        <label for="">Start Date</label>
                                        <span><?php echo $info['StartTime'] ?></span>
                                    </div>
                                    <div class="double">
                                        <label for="">Expected Delvery</label>
                                        <span><?php echo $info['ExpectedDate'] ?></span>
                                    </div>
                                    <div class="double">
                                        <label for="">Actual End Date</label>
                                        <span><?php echo $info['EndDate'] ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="longtextdata viewprojectServices">
                                <label for="">Discription</label>
                                <p><?php echo nl2br($info['Discription']) ?></p>
                            </div>
                            <div class="projectServices viewprojectServices">
                                <h4>Project Services</h4>
                                <div class="selectService">
                                    <table>
                                        <thead>
                                            <th>Service ID</th>
                                            <th>Service Name</th>
                                            <th>Budjet</th>
                                            <th>Notes</th>
                                        </thead>
                                        <tbody >
                                            <?php
                                                $sql=$con->prepare('SELECT tblserviceproject.ServiceID,Note,ServiceTitle,Price 
                                                                    FROM tblserviceproject
                                                                    INNER JOIN tblclientservices ON tblclientservices.ServicesID = tblserviceproject.ServiceID
                                                                    WHERE ProjectID=?');
                                                $sql->execute(array($projectId));
                                                $services = $sql->fetchAll();
                                                $totalBuget = 0;
                                                foreach($services as $ser){
                                                    echo '
                                                        <tr>
                                                            <td>'.$ser['ServiceID'].'</td>
                                                            <td>'.$ser['ServiceTitle'].'</td>
                                                            <td>'.$ser['Price'].'</td>
                                                            <td>'.$ser['Note'].'</td>
                                                        </tr>
                                                    ';
                                                    $totalBuget+=$ser['Price'];
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="Developers viewDevelopers">
                                <h4>Your Shares</h4>
                                <table>
                                    <thead>
                                        <th>Name</th>
                                        <th>Assigned Service</th>
                                        <th>Expected Amount</th>
                                        <th>Notes</th>
                                    </thead>
                                    <tbody >
                                        <?php
                                            $sql=$con->prepare('SELECT tbldevelopers_project.Posstion,PersentageShare,Note,Fname,LName,MidelName
                                                                FROM tbldevelopers_project
                                                                INNER JOIN tblstaff ON tblstaff.staffID = tbldevelopers_project.FreelancerID
                                                                WHERE projectID = ? AND FreelancerID=?');
                                            $sql->execute(array($projectId,$staff_Id));
                                            $freelancers = $sql->fetchAll();
                                            foreach($freelancers as $free){
                                                $amountFree = $free['PersentageShare'] * $totalBuget / 100;
                                                echo '
                                                    <tr>
                                                        <td>'.$free['Fname'].' '.$free['MidelName'].' '.$free['LName'].'</td>
                                                        <td>'.$free['Posstion'].'</td>
                                                        <td>'.number_format($amountFree,2).' $</td>
                                                        <td>'.$free['Note'].'</td>
                                                    </tr>
                                                ';
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="longtextdata">
                                <label for="">Note:</label>
                                <p><?php echo nl2br($info['note']) ?></p>
                            </div>
                            <div class="btncontroler">
                                <button class="btn btn-secondary btnbacktomanage">Back</button>
                            </div>
                        </div>
                    <?php
                    }else{
                        echo '<script> location.href="manageProjects.php"</script>';
                    }

                }else{
                    header('location:dashbord.php');
                }
            ?>


            
        </div>
    </main>
    <?php include '../common/jslinks.php' ?>
    <script src="js/manageProjects.js"></script>
</body>