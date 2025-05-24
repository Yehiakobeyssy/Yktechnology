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

    if(isset($_POST['btnnewpostion'])){
        $newpostion = $_POST['newpostion'];

        $sql=$con->prepare('INSERT INTO  tblpossition_request (Possition_Name,active_postion) VALUES (?,?)');
        $sql->execute(array($newpostion,1));
    }
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
                                <th>Accepted Date</th>
                                <th>Status</th>
                                <th>Control</th>
                            </thead>
                            <tbody class="viewFrelancer">

                            </tbody>
                        </table>
                    </div>
                <?php
                }elseif($do=='view'){
                    $freelanceerid = isset($_GET['id'])?$_GET['id']:0;
                    $check_freelancer = checkItem('staffID','tblstaff',$freelanceerid);
                    if($check_freelancer == 1){
                        ?>
                            <div class="btncontrol">
                                <?php
                                    $sql=$con->prepare('SELECT block,accepted FROM tblstaff WHERE staffID=?');
                                    $sql->execute(array($freelanceerid));
                                    $fetch = $sql->fetch();

                                    if($fetch['accepted']== 0){
                                        $styleaccepted= 'inline';
                                    }else{
                                        $styleaccepted= 'none';
                                    }

                                    if($fetch['block']==0){
                                        $textblock = 'block';
                                        $classblock = 'danger';
                                    }else{
                                        $textblock = 'UNblock';
                                        $classblock = 'success';
                                    }
                                ?>
                                <button class="btn btn-success btnaccepted" style="display:<?php echo $styleaccepted ?>;" data-index="<?php echo $freelanceerid ?>">Accepted</button>
                                <button class="btnblock btn btn-<?php echo $classblock ?>" data-index="<?php echo $freelanceerid ?>" ><?php echo $textblock ?></button>
                            </div>
                            <div class="personalinfo">
                                <div class="data_free">
                                    <?php
                                        $sql= $con->prepare('SELECT Fname,MidelName,LName,DOB,Staff_Phone,Staff_email,Staff_address,Region,staff_CV,Front_ID,link_github,link_experinace FROM tblstaff WHERE staffID= ?');
                                        $sql->execute(array($freelanceerid));
                                        $result = $sql->fetch();
                                        $fullname= $result['Fname'].' '.$result['MidelName'].' '.$result['LName'];
                                        $birthday = $result['DOB'];
                                        $phonenumber = $result['Staff_Phone'];
                                        $staff_mail = $result['Staff_email'];
                                        $staff_add = $result['Staff_address'].' ( ' . $result['Region'] .' ) ';
                                        $staff_cv= $result['staff_CV'];
                                        $staff_ID = $result['Front_ID'];
                                        $staff_github = $result['link_github'];
                                        $staff_protfolio= $result['link_experinace'];

                                        function ensure_url_protocol($url) {
                                            if (!preg_match('~^(http|https)://~i', $url)) {
                                                return 'https://' . $url;
                                            }
                                            return $url;
                                        }

                                        $staff_github = ensure_url_protocol($staff_github);
                                        $staff_protfolio = ensure_url_protocol($staff_protfolio);

                                        $birthDate = new DateTime($birthday);
                                        $today = new DateTime();
                                        $age = $today->diff($birthDate);

                                        $ageText = $age->y . ' y, ' . $age->m . ' m';
                                    ?>
                                    <table>
                                        <tr>
                                            <td><label for="">Name :</label></td>
                                            <td><span><?php echo $fullname ?></span></td>
                                        </tr>
                                        <tr>
                                            <td><label for="">Date of Birth</label></td>
                                            <td><span><?php echo $result['DOB'] .' ('.$ageText.' old)' ?></span></td>
                                        </tr>
                                        <tr>
                                            <td><label for="">Phone Number</label></td>
                                            <td><span><?php echo  $phonenumber ?></span></td>
                                        </tr>
                                        <tr>
                                            <td><label for="">E-mail</label></td>
                                            <td><span><?php echo $staff_mail ?></span></td>
                                        </tr>
                                        <tr>
                                            <td><label for="">Address</label></td>
                                            <td><span><?php echo  $staff_add ?></span></td>
                                        </tr>
                                    </table>
                                    <a href="../Documents/<?php echo $staff_cv  ?>" target="_blank"> link CV</a>
                                    <a href="../Documents/<?php echo $staff_ID  ?>" target="_blank"> link ID</a>
                                    <a href="<?php echo $staff_github  ?>" target="_blank"> link github</a>
                                    <a href="<?php echo $staff_protfolio  ?>" target="_blank"> link Protfolio</a>


                                </div>
                                <div class="photofree">
                                    <?php
                                        $sql=$con->prepare('SELECT Staff_Photo,Possition_Name FROM tblstaff INNER JOIN  tblpossition_request ON Possition_ID= Posstion WHERE staffID= ?');
                                        $sql->execute(array($freelanceerid));
                                        $result = $sql->fetch();
                                        $personal_Photo  =  $result['Staff_Photo'];
                                        $postition = $result['Possition_Name'];

                                        $photoPath = "../Documents/" . $personal_Photo;
                                        if (!file_exists($photoPath) || empty($personal_Photo)) {
                                            $photoPath = "../Documents/nophoto.png";
                                        }
                                    ?>
                                    <img src="<?php echo $photoPath ?>" alt="">
                                    <span><?php  echo $postition?></span>
                                </div>
                            </div>
                            <div class="technicalinfo">
                                <h4>Technical Info</h4>
                                <?php
                                    $sql = $con->prepare('SELECT Experiance_years, expected_sallary, Start_date, Work_hours, now_work, Notice_Period, Programing_lang, Framworks FROM tblstaff WHERE staffID=?');
                                    $sql->execute([$freelanceerid]);
                                    $result = $sql->fetch();

                                    $experiance_Year = $result['Experiance_years'];
                                    $expected_sallary = $result['expected_sallary'];
                                    $can_start = $result['Start_date'];
                                    $work_hours = $result['Work_hours'];
                                    $now_work = ($result['now_work'] == 1) ? 'Yes' : 'No';
                                    $note_Work = $result['Notice_Period'];
                                    $lang_pro = $result['Programing_lang'];
                                    $framwork_pro = $result['Framworks'];
                                ?>
                                <div class="onepair">
                                    <div class="pair">
                                        <label>Years of Experience:</label>
                                        <span><?php echo $experiance_Year ?></span>
                                    </div>
                                    <div class="pair">
                                        <label>Expected Salary (per Hour):</label>
                                        <span><?php echo $expected_sallary ?></span>
                                    </div>
                                </div>
                                <div class="onepair">
                                    <div class="pair">
                                        <label>Can Work At:</label>
                                        <span><?php echo $can_start ?></span>
                                    </div>
                                    <div class="pair">
                                        <label>Work Hours:</label>
                                        <span><?php echo $work_hours ?></span>
                                    </div>
                                </div>
                                <div class="onepair">
                                    <div class="pair">
                                        <label>Currently Working:</label>
                                        <span><?php echo $now_work ?></span>
                                    </div>
                                </div>
                                <div class="other_info">
                                    <label>Work Note:</label>
                                    <span><?php echo $note_Work ?></span>
                                    <label>Programming Languages:</label>
                                    <span><?php echo $lang_pro ?></span>
                                    <label>Frameworks Used:</label>
                                    <span><?php echo $framwork_pro ?></span>
                                </div>
                            </div>
                            <div class="money_transfer">
                                <h4>Trasnfer Info</h4>
                                <?php
                                    $sql=$con->prepare('SELECT Transfer_Note,methot FROM tblstaff INNER JOIN  tblpayment_method ON paymentmethodD = Transfer_Money WHERE staffID = ?');
                                    $sql->execute([$freelanceerid]);
                                    $result = $sql->fetch();
                                    $trasfer_method = $result['methot'];
                                    $transfer_note = $result['Transfer_Note'];
                                ?>
                                <label for="">Trasfer Method:</label>
                                <span><?php echo  $trasfer_method ?></span><br>
                                <label for="">Transfer Note</label><br>
                                <span><?php echo  $transfer_note ?></span>
                            </div>
                            <div class="addnote">
                                <?php 
                                    $sql=$con->prepare('SELECT Abouthim FROM tblstaff WHERE staffID = ?');
                                    $sql->execute([$freelanceerid]);
                                    $result = $sql->fetch();
                                    $note = $result['Abouthim']
                                ?>
                                <form action="" method="post">
                                    <label for="">Note</label>
                                    <textarea name="Abouthim" id="" rows="5"><?php echo $note ?></textarea>
                                    <div class="btncotrl">
                                        <button type="submit" name="btnaddnote">Save</button>
                                    </div>
                                    
                                </form>
                                <?php 
                                    if(isset($_POST['btnaddnote'])){
                                        $newnote = $_POST['Abouthim'];
                                        $sql=$con->prepare('UPDATE tblstaff SET Abouthim = ? WHERE staffID = ?');
                                        $sql->execute(array($newnote,$freelanceerid));
                                        echo '<script> location.href="ManageFreelancer.php?do=view&id='.$freelanceerid.'"</script>';
                                    }
                                ?>
                            </div>
                        <?php
                    }else{
                        echo '<script> location.href="ManageFreelancer.php"</script>';
                    }
                }elseif($do =='accepted'){
                    $freelanceerid = isset($_GET['id'])?$_GET['id']:0;
                    $check_freelancer = checkItem('staffID','tblstaff',$freelanceerid);
                    if($check_freelancer == 1){
                        $AcceptedDate    = date('Y-m-d');
                        $sql=$con->prepare('UPDATE tblstaff SET accepted= 1 , DatewillBegin =? WHERE staffID = ?');
                        $sql->execute(array($AcceptedDate,$freelanceerid));
                        echo '<script> location.href="ManageFreelancer.php"</script>';
                    }else{
                        echo '<script> location.href="ManageFreelancer.php"</script>';
                    }

                }elseif($do=='blocked'){
                    $freelanceerid = isset($_GET['id'])?$_GET['id']:0;
                    $check_freelancer = checkItem('staffID','tblstaff',$freelanceerid);
                    if($check_freelancer == 1){
                        
                        $sql=$con->prepare('SELECT Fname,LName,block FROM tblstaff WHERE staffID = ?');
                        $sql->execute(array($freelanceerid));
                        $result=$sql->fetch();
                        $name= $result['Fname'].' '. $result['LName'];

                        if($result['block']==0 ){
                            ?>
                            <div class="blocksection alert alert-danger">
                                <h3>Are You shure to Block <?php echo $name ?></h3>
                                <form action="" method="post">
                                    <button class="btn btn-danger btncalcelblock">No</button>
                                    <button class="btn btn-success" type="submit" name="btnblockyes">yes</button>
                                </form>
                            </div>
                            <?php
                        }elseif($result['block']==1){
                            ?>
                            <div class="blocksection alert alert-success">
                                <h3>Are You shure to unBlock <?php echo $name ?></h3>
                                <form action="" method="post">
                                    <button class="btn btn-danger btncalcelblock">No</button>
                                    <button class="btn btn-success" type="submit" name="btnblockno">yes</button>
                                </form>
                            </div>
                            <?php
                        }
                        
                        if(isset($_POST['btnblockyes'])){
                            $sql=$con->prepare('UPDATE tblstaff SET block = 1 WHERE staffID = ?');
                            $sql->execute(array($freelanceerid));
                            echo '<script> location.href="ManageFreelancer.php"</script>';
                        }
                        if(isset($_POST['btnblockno'])){
                            $sql=$con->prepare('UPDATE tblstaff SET block = 0 WHERE staffID = ?');
                            $sql->execute(array($freelanceerid));
                            echo '<script> location.href="ManageFreelancer.php"</script>';
                        }
                    }else{
                        echo '<script> location.href="ManageFreelancer.php"</script>';
                    }
                }
            ?>
            
        </div>
    </div>
    <div class="popup">
        <div class="containerpopup">
            <div class="closepopup">+</div>
            <h4>Postions request</h4>
            <form action="" method="post">
                <input type="text" name="newpostion" id="" placeholder="New Posstion" required>
                <button type="submit" class="btn btn-success" name="btnnewpostion"><i class="fa-solid fa-check"></i></button>
            </form>
            <div class="fetchposstion">
                <table>
                    <thead>
                        <th>Postion</th>
                        <th>Active</th>
                    </thead>
                    <tbody class="datafech">

                    </tbody>
                    
                </table>
            </div>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/ManageFreelancer.js"></script>
    <script src="js/sidebar.js"></script>
</body>