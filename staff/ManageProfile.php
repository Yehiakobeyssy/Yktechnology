<?php
    session_start();

    if(!isset($_COOKIE['staff'])){
        if(!isset($_SESSION['staff'])){
            header('location:index.php');
        }
    }
    $staff_Id= 1;

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
?>
    <link rel="stylesheet" href="css/ManageProfile.css">
</head>
<body>
    <?php include 'include/headerstaff.php' ?>
    <main>
        <?php include 'include/aside.php' ;
        $sql = $con->prepare("SELECT * FROM tblstaff WHERE StaffID = ?");
        $sql->execute([$staff_Id]);
        $staff = $sql->fetch(PDO::FETCH_ASSOC);
        ?>
        <div class="project_container">
            <div class="new_staff">
                <div class="title">
                    <h3>My Profile</h3>
                </div>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="personalinformation section_new">
                        <h4>Personal Information</h4>
                        <label>Full Name :</label>
                        <input type="text" name="Fname" value="<?= $staff['Fname'] ?>" placeholder="First Name" class="trible" required disabled>
                        <input type="text" name="MidelName" value="<?= $staff['MidelName'] ?>" placeholder="Middle Name" class="trible" disabled>
                        <input type="text" name="LName" value="<?= $staff['LName'] ?>" placeholder="Last Name" class="trible" required disabled><br>

                        <label>Date of Birth</label>
                        <input type="date" name="DOB" value="<?= $staff['DOB'] ?>" class="trible" required disabled>

                        <label>Gender</label>
                        <select name="Gender" class="trible" disabled>
                            <option value="1" <?= $staff['Gender']==1?'selected':'' ?>>Male</option>
                            <option value="2" <?= $staff['Gender']==2?'selected':'' ?>>Female</option>
                        </select><br>

                        <label>Nationality</label>
                        <select name="Nationality" class="full" disabled>
                            <?php
                            $sql = $con->prepare('SELECT CountryID, CountryName FROM tblcountrys ORDER BY CountryName');
                            $sql->execute();
                            $natinalitys=$sql->fetchAll();
                            foreach($natinalitys as $nat){
                                $selected = ($staff['Nationality'] == $nat['CountryID']) ? 'selected' : '';
                                echo "<option value='{$nat['CountryID']}' $selected>{$nat['CountryName']}</option>";
                            }
                            ?>
                        </select><br>

                        <label>Phone Number</label>
                        <input type="text" name="Staff_Phone" value="<?= htmlspecialchars($staff['Staff_Phone']) ?>" class="trible">

                        <label>E-mail</label>
                        <input type="email" name="Staff_email" value="<?= htmlspecialchars($staff['Staff_email']) ?>" class="double" required disabled><br>

                        <h6>Address</h6>
                        <label>Country</label>
                        <select name="Staff_Country" class="double">
                            <?php
                            $sql = $con->prepare('SELECT CountryID,CountryName FROM tblcountrys ORDER BY CountryName');
                            $sql->execute();
                            $countries=$sql->fetchAll();
                            foreach($countries as $nat){
                                $selected = ($staff['Staff_Country'] == $nat['CountryID']) ? 'selected' : '';
                                echo "<option value='{$nat['CountryID']}' $selected>{$nat['CountryName']}</option>";
                            }
                            ?>
                        </select>

                        <label>Region</label>
                        <input type="text" name="Region" value="<?= htmlspecialchars($staff['Region']) ?>" class="trible"><br>

                        <label>Address</label>
                        <input type="text" name="Staff_address" value="<?= htmlspecialchars($staff['Staff_address']) ?>" class="full" required>
                    </div>

                    <div class="section_new">
                        <h5>Professional Information</h5>
                        <label>Position Applied</label>
                        <select name="Posstion" class="full" required disabled>
                            <option value="0">none</option>
                            <?php 
                            $sql=$con->prepare('SELECT Possition_ID,Possition_Name FROM tblpossition_request WHERE active_postion = 1');
                            $sql->execute();
                            $posstions = $sql->fetchAll();
                            foreach($posstions as $pos){
                                $selected = ($staff['Posstion'] == $pos['Possition_ID']) ? 'selected' : '';
                                echo "<option value='{$pos['Possition_ID']}' $selected>{$pos['Possition_Name']}</option>";
                            }
                            ?>
                        </select><br>

                        <label>Start Date</label>
                        <input type="date" name="Start_date" value="<?= $staff['Start_date'] ?>" class="trible" required disabled>

                        <label>Expected Salary</label>
                        <input type="number" name="expected_sallary" value="<?= $staff['expected_sallary'] ?>" step="0.01" class="trible" disabled> /per hour<br>

                        <label style="width: 200px;">Work Hours Preference</label>
                        <input type="number" name="Work_hours" value="<?= $staff['Work_hours'] ?>" class="trible">

                        <label style="width: 250px;">Are you currently employed?</label>
                        <select name="now_work" class="trible">
                            <option value="1" <?= $staff['now_work']==1?'selected':'' ?>>Yes</option>
                            <option value="0" <?= $staff['now_work']==0?'selected':'' ?>>No</option>
                        </select><br>

                        <label>Notice Period</label>
                        <input type="text" name="Notice_Period" value="<?= htmlspecialchars($staff['Notice_Period']) ?>" class="full">
                    </div>

                    <div class="section_new">
                        <h5>Technical Background</h5>
                        <label style="width: 250px;">Years of Experience</label>
                        <input type="number" name="Experiance_years" value="<?= $staff['Experiance_years'] ?>" class="double" required><br>

                        <label style="width: 250px;">Programming Languages Known</label>
                        <input type="text" name="Programing_lang" value="<?= htmlspecialchars($staff['Programing_lang']) ?>" class="double"><br>

                        <label style="width: 250px;">Frameworks or Libraries Used</label>
                        <input type="text" name="Framworks" value="<?= htmlspecialchars($staff['Framworks']) ?>" class="double"><br>

                        <label>Link Experience</label>
                        <input type="text" name="link_experinace" value="<?= htmlspecialchars($staff['link_experinace']) ?>" class="full"><br>

                        <label>Link Github</label>
                        <input type="text" name="link_github" value="<?= htmlspecialchars($staff['link_github']) ?>" class="full">
                    </div>

                    <div class="section_new">
                        <h5>Other Details</h5>
                        <label style="width: 250px;">Communication Language</label>
                        <select name="Comunication_Lang" class="double">
                            <option value="Ar" <?= $staff['Comunication_Lang']=='Ar'?'selected':'' ?>>Arabic</option>
                            <option value="En" <?= $staff['Comunication_Lang']=='En'?'selected':'' ?>>English</option>
                            <option value="Ge" <?= $staff['Comunication_Lang']=='Ge'?'selected':'' ?>>German</option>
                            <option value="Es" <?= $staff['Comunication_Lang']=='Es'?'selected':'' ?>>Spanish</option>
                            <option value="Fr" <?= $staff['Comunication_Lang']=='Fr'?'selected':'' ?>>French</option>
                        </select><br>

                        <label>Cv</label>
                        <input type="file" name="staff_CV" class="full"><br>

                        <label>Personal Photo</label>
                        <input type="file" name="Staff_Photo" class="full"><br>

                        <label>Front ID</label>
                        <input type="file" name="Front_ID" class="trible">

                        <label>Back ID</label>
                        <input type="file" name="Back_ID" class="trible"><br>

                        <label>Transfer Money</label>
                        <select name="Transfer_Money" class="full">
                            <?php
                            $sql=$con->prepare('SELECT paymentmethodD,methot FROM  tblpayment_method WHERE paymentmethodD !=3');
                            $sql->execute();
                            $payments= $sql->fetchAll();
                            foreach($payments as $payment){
                                $selected = ($staff['Transfer_Money'] == $payment['paymentmethodD']) ? 'selected' : '';
                                echo "<option value='{$payment['paymentmethodD']}' $selected>{$payment['methot']}</option>";
                            }
                            ?>
                        </select><br>

                        <label>Transfer Note</label>
                        <input type="text" name="Transfer_Note" value="<?= htmlspecialchars($staff['Transfer_Note']) ?>" placeholder="Like Iban" class="full">
                    </div>
                    <div class="save_form">
                        <button type="reset">Cancel</button>
                        <button type="submit" name="btnsend">update </button>
                    </div>
                </form>
                <?php
                    if(isset($_POST['btnsend'])){
                        // Staff ID
                        

                        // Personal Information
                        $Staff_Phone       = $_POST['Staff_Phone'];
                        $Staff_Country     = $_POST['Staff_Country'];
                        $Region            = $_POST['Region'];
                        $Staff_address     = $_POST['Staff_address'];

                        // Professional Information
                        $Work_hours        = $_POST['Work_hours'];
                        $now_work          = $_POST['now_work'];
                        $Notice_Period     = $_POST['Notice_Period'];

                        // Technical Background
                        $Experiance_years  = $_POST['Experiance_years'];
                        $Programing_lang   = $_POST['Programing_lang'];
                        $Framworks         = $_POST['Framworks'];
                        $link_experinace   = $_POST['link_experinace'];
                        $link_github       = $_POST['link_github'];

                        // Other Details
                        $Comunication_Lang = $_POST['Comunication_Lang'];
                        $Transfer_Money    = $_POST['Transfer_Money'];
                        $Transfer_Note     = $_POST['Transfer_Note'];

                        // Security

                        // Get old files from DB
                        $stmt = $con->prepare("SELECT staff_CV, Staff_Photo, Front_ID, Back_ID, staffPassword FROM tblstaff WHERE staffID=?");
                        $stmt->execute([$staff_Id]);
                        $oldData = $stmt->fetch(PDO::FETCH_ASSOC);

                        $targetDir = "../Documents/";

                        // Function to handle file upload
                        function handleFileUpload($file, $oldFile, $targetDir){
                            if(isset($file) && $file['error'] == UPLOAD_ERR_OK){ 
                                // delete old file if exists
                                if(!empty($oldFile) && file_exists($targetDir.$oldFile)){
                                    unlink($targetDir.$oldFile);
                                }
                                // upload new file
                                return uploadFile($file, $targetDir);
                            }else{
                                // keep old file if no new upload
                                return $oldFile;
                            }
                        }

                        // Files handling
                        $newCvname      = handleFileUpload($_FILES['staff_CV'], $oldData['staff_CV'], $targetDir);
                        $newstaff_photo = handleFileUpload($_FILES['Staff_Photo'], $oldData['Staff_Photo'], $targetDir);
                        $newFront_ID    = handleFileUpload($_FILES['Front_ID'], $oldData['Front_ID'], $targetDir);
                        $newBack_ID     = handleFileUpload($_FILES['Back_ID'], $oldData['Back_ID'], $targetDir);

                        

                        // Update query
                        $sql = $con->prepare('UPDATE tblstaff SET 
                                                    
                                                    Staff_Phone=?,Staff_Country=?, Region=?, Staff_address=?, 
                                                    Work_hours=?, now_work=?, Notice_Period=?, 
                                                    Experiance_years=?, Programing_lang=?, Framworks=?, link_experinace=?, link_github=?, 
                                                    Comunication_Lang=?, staff_CV=?, Staff_Photo=?, Front_ID=?, Back_ID=?, 
                                                    Transfer_Money=?, Transfer_Note=?,block=?, accepted=? 
                                            WHERE staffID=?');

                        $sql->execute([
                            $Staff_Phone,
                            $Staff_Country,
                            $Region,
                            $Staff_address,
                            $Work_hours,
                            $now_work,
                            $Notice_Period,
                            $Experiance_years,
                            $Programing_lang,
                            $Framworks,
                            $link_experinace,
                            $link_github,
                            $Comunication_Lang,
                            $newCvname,
                            $newstaff_photo,
                            $newFront_ID,
                            $newBack_ID,
                            $Transfer_Money,
                            $Transfer_Note,
                            0,
                            1,
                            $staff_Id
                        ]);

                    }
                    ?>

                                </div>
        </div>
    </main>
    <?php include '../common/jslinks.php' ?>
    <script src="js/ManageProfile.js"></script>
</body>