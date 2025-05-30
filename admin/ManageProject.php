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
                <button class="btn btn-primary btnPosstion btnnewproject">New Project</button>
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
                    ?>
                    <div class="newForm">
                        <div class="formtitle">
                            <h3>Project Form</h3>
                        </div>
                        <form action="" method="post">
                            <div class="long">
                                <label for="">Project Name : </label>
                                <input type="text" name="project_Name" id="" required>
                            </div>
                            <div class="long">
                                <label for="">Project Manager :</label>
                                <select name="Project_Manager" id="">
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
                            </div>
                            <div class="colmdata">
                                <div class="left">
                                    <div class="double">
                                        <label for="">Client Name :</label>
                                        <select name="ClientID" id="clientSelect" required>
                                            <option value="">[Selct Client]</option>
                                            <?php
                                                $sql= $con->prepare('SELECT ClientID,Client_FName,Client_LName FROM tblclients WHERE  client_active = 1 ORDER BY Client_FName');
                                                $sql->execute();
                                                $clients = $sql->fetchAll();
                                                foreach($clients  as $client){
                                                    echo '<option value="'.$client['ClientID'].'">'.$client['Client_FName'].' '.$client['Client_LName'].'</option>';
                                                };
                                            ?>
                                        </select>
                                    </div>
                                    <div class="double">
                                        <span id="lblAddress"></span>
                                    </div>
                                    <div class="double">
                                        <span id="lblphonenUmber"></span>
                                    </div>
                                </div>
                                <div class="right">
                                    <div class="double">
                                        <label for="">Start Date</label>
                                        <input type="date" name="StartTime" id="" required>
                                    </div>
                                    <div class="double">
                                        <label for="">Expected Delvery</label>
                                        <input type="date" name="ExpectedDate" id="" required>
                                    </div>
                                    <div class="double">
                                        <label for="">Actual End Date</label>
                                        <input type="date" name="EndDate" id="">
                                    </div>
                                </div>
                            </div>
                            <div class="longtextdata">
                                <label for="">Discription</label>
                                <textarea name="Discription" id="" rows="5"></textarea>
                            </div>
                            <div class="projectServices">
                                <h4>Project Services (<span class="totalbudgut"></span>)</h4>
                                <div class="selectService">
                                    <input type="text" name="txtClientService" id="txtClient" hidden>
                                    <select name="" id="selService"></select>
                                    <table>
                                        <thead>
                                            <th>Service ID</th>
                                            <th>Service Name</th>
                                            <th>Budjet</th>
                                            <th>Notes</th>
                                            <th>Control</th>
                                        </thead>
                                        <tbody id="viewServiceProject">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="Developers">
                                <h4>Developers & Shares</h4>
                                <div class="input_schare ">
                                    <div class="double left">
                                        <label for="">% Managment</label>
                                        <input type="number" name="shareManagement" id="txtsharemanagment" value="10" step="0.01">
                                    </div>
                                    <div class="double right">
                                        <label for="">% Reserve</label>
                                        <input type="number" name="shareReserve" id="txtsharereserve" value="20" step="0.01">
                                    </div>
                                </div>
                                <div class="long">
                                    <label for="">New Freelancer</label>
                                    <select name="" id="selFreelancer">
                                        <option value="">[Select Freelancer]</option>
                                        <?php
                                            $sql= $con->prepare('SELECT staffID,Fname,MidelName,LName FROM  tblstaff WHERE accepted = 1 AND block=0 ORDER BY Fname');
                                            $sql->execute();
                                            $freelancers = $sql->fetchAll();
                                            foreach($freelancers as $freelancer){
                                                echo '<option value="'.$freelancer['staffID'].'">'.$freelancer['Fname'].' '.$freelancer['MidelName'].' '.$freelancer['LName'].'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                <table>
                                    <thead>
                                        <th>Name</th>
                                        <th>Assigned Service</th>
                                        <th>% Share</th>
                                        <th>Expected Amount</th>
                                        <th>Notes</th>
                                        <th>Control</th>
                                    </thead>
                                    <tbody class="viewfreelancers">

                                    </tbody>
                                </table>
                            </div>
                            <div class="longtextdata">
                                <label for="">Note:</label>
                                <textarea name="note" id="" rows="5"></textarea>
                            </div>
                            <div class="btncontroler">
                                <button type="reset" class="btn btn-danger">Clear</button>
                                <button type="submit" class="btn btn-success" name="btnnewproject">Save Project</button>
                            </div>
                        </form>
                        <?php
                            if(isset($_POST['btnnewproject'])){
                                $project_Name       = $_POST['project_Name'];
                                $Project_Manager    = $_POST['Project_Manager'];
                                $ClientID           = $_POST['ClientID'];
                                $StartTime          = $_POST['StartTime'];
                                $ExpectedDate	    = $_POST['ExpectedDate'];
                                $EndDate            = $_POST['EndDate'];
                                $Discription        = $_POST['Discription'];
                                $shareManagement    = $_POST['shareManagement'];
                                $shareReserve       = $_POST['shareReserve'];
                                $Status             = 1;
                                $note               = $_POST['note'];

                                $sql=$con->prepare('INSERT INTO tblprojects 
                                                        (ClientID,Project_Manager,Discription,project_Name,StartTime,ExpectedDate,EndDate,shareManagement,shareReserve,Status,note) 
                                                    VALUES 
                                                        (:ClientID,:Project_Manager,:Discription,:project_Name,:StartTime,:ExpectedDate,:EndDate,:shareManagement,:shareReserve,:Status,:note)');
                                $sql->execute(array(
                                    'ClientID'          => $ClientID ,
                                    'Project_Manager'   => $Project_Manager,
                                    'Discription'       => $Discription,
                                    'project_Name'      => $project_Name,
                                    'StartTime'         => $StartTime,
                                    'ExpectedDate'      => $ExpectedDate,
                                    'EndDate'           => $EndDate,
                                    'shareManagement'   => $shareManagement,
                                    'shareReserve'      => $shareReserve ,
                                    'Status'            => $Status,
                                    'note'              => $note
                                ));

                                $ProjectID = get_last_ID('ProjectID','tblprojects');

                                if (isset($_SESSION['ServiceProject'])) {

                                    foreach ($_SESSION['ServiceProject'] as $index => $item) {
                                        $ServiceID = $item['id'];
                                        $note = $item['note'];

                                        $sql=$con->prepare('INSERT INTO tblserviceproject 
                                                                (ProjectID,ServiceID,Note)
                                                            VALUES
                                                                (:ProjectID,:ServiceID,:Note)');
                                        $sql->execute(array(
                                            'ProjectID'     => $ProjectID,
                                            'ServiceID'     => $ServiceID,
                                            'Note'          => $note
                                        ));
                                    }

                                    unset($_SESSION['ServiceProject']);
                                }

                                if (isset($_SESSION['freelancerProject'])){
                                    foreach ($_SESSION['freelancerProject'] as $index => $item) {
                                        $freelancerId = $item['id'];
                                        $Service      = $item['Service'];
                                        $share        = $item['share'];
                                        $frenote         = $item['note'];

                                        $sql=$con->prepare('INSERT INTO tbldevelopers_project
                                                                (projectID,FreelancerID,Posstion,PersentageShare,Note)
                                                            VALUES
                                                                (:projectID,:FreelancerID,:Posstion,:PersentageShare,:Note)');
                                        $sql->execute(array(
                                            'projectID'         => $ProjectID,
                                            'FreelancerID'      => $freelancerId,
                                            'Posstion'          => $Service,
                                            'PersentageShare'   => $share,
                                            'Note'              => $frenote
                                        ));

                                    }

                                    unset($_SESSION['freelancerProject']);
                                }
                            }
                        ?>
                    </div>
                <?php
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