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
                if($do=='manage'){

                }elseif($do=='view'){

                }elseif($do=='add'){?>
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
                                            echo '<option value="' . $free['staffID'] . '" >'
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
                }elseif($do=='edid'){

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