<?php
    session_start();

    if(isset($_COOKIE['staff'])){
        header('location:checkstaff.php');
    }elseif(isset($_SESSION['staff'])){
        header('location:checkstaff.php');
    }

    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';

    $do= (isset($_GET['do']))?$_GET['do']:'login';

?>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <?php
        if($do=='login'){
        ?>
            <div class="frmlogin">
                <div class="logo_login">
                    <i class="fa-solid fa-clipboard-user"></i>
                </div>
                <form action="" method="post">
                    <div class="frmcateria">
                        <div class="oneline">
                            <label for="">E-mail</label>
                            <input type="email" name="emailuser" id="" class="alone">
                        </div>
                        <div class="oneline">
                            <label for="">Password</label>
                            <input type="password" name="passworduser" id="" class="alone">
                        </div>
                    </div>
                    <div class="remember">
                        <div class="rem">
                            <input type="checkbox" name="rememberme" id="txtrememberme">
                            <label for="txtrememberme">Remember Me</label>
                        </div>
                        <div class="forget">
                            <a href="forgetpass.php">Forget Password!</a>
                        </div>
                    </div> 
                    <div class="crlbutton">
                        <button type="submit" name="btnlogin">Login</button>
                        <a href="index.php?do=signup">Apply now</a>
                    </div>
                </form>
                <?php
                    if(isset($_POST['btnlogin'])){
                        $useremail  = $_POST['emailuser'];
                        $userpass   = sha1($_POST['passworduser']);
                        $rember_me  = (isset($_POST['rememberme']))?1:0;

                        $sql= $con->prepare('SELECT staffID FROM  tblstaff WHERE Staff_email = ? AND staffPassword = ?');
                        $sql->execute(array($useremail,$userpass));
                        $rowCount = $sql->rowCount();
                        $result= $sql->fetch();
                        $staffID= $result['staffID'];

                        if($rowCount == 1){

                            if($rember_me == 1){
                                setcookie('staff', $staffID, time()+3600 * 24 * 365);
                            }else{
                                $_SESSION['staff'] = $staffID;
                            }
                            header('location:checkstaff.php');
                        }else{
                            echo '
                                <div class="alert alert-danger" role="alert">
                                    E-mail or Password is wrong!!
                                </div>
                            ';
                        }
                    }
                ?>
            </div>
        <?php
        }elseif($do=='signup'){?>
            <div class="new_staff">
                <div class="title">
                    <img src="../images/newstaff.png" alt="">
                    <h3>Job Application Form</h3>
                    <label for="">Please fill in this form in English and not ignore any fild</label>
                </div>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="personalinformation section_new">
                        <h4>Personal Information</h4>
                        <label for="">Full Name :</label>
                        <input type="text" name="Fname" id="" placeholder=" First Name" class="trible" required>
                        <input type="text" name="MidelName" id="" placeholder=" Mitel Name" class="trible">
                        <input type="text" name="LName" id="" placeholder=" Last Name " class="trible" required><br>
                        <label for="">Date of Birth</label>
                        <input type="date" name="DOB" id="" class="trible" required>
                        <label for="">Gender</label>
                        <select name="Gender" id="" class="trible">
                            <option value="1">Male</option>
                            <option value="2">Female</option>
                        </select><br>
                        <label for="">Nationality</label>
                        <select name="Nationality" id="" class="full">
                            <?php
                                $sql = $con->prepare('SELECT CountryID,CountryName FROM tblcountrys ORDER BY CountryName');
                                $sql->execute();
                                $natinalitys=$sql->fetchAll();
                                
                                foreach($natinalitys as $nat){
                                    echo '
                                        <option value="'.$nat['CountryID'].'">'.$nat['CountryName'].'</option>
                                    ';
                                }
                            ?>
                        </select><br>
                        <label for="">Phone Number</label>
                        <input type="text" name="Staff_Phone" id=""class="trible">
                        <label for="">E-mail</label>
                        <input type="email" name="Staff_email" id=""class="double" required><br>

                        <h6>Addresse</h6>
                        <label for="">Country</label>
                        <select name="Staff_Country" id="" class="double">
                            <?php
                                $sql = $con->prepare('SELECT CountryID,CountryName FROM tblcountrys ORDER BY CountryName');
                                $sql->execute();
                                $natinalitys=$sql->fetchAll();
                                
                                foreach($natinalitys as $nat){
                                    echo '
                                        <option value="'.$nat['CountryID'].'">'.$nat['CountryName'].'</option>
                                    ';
                                }
                            ?>
                        </select>
                        <label for="">Region</label>
                        <input type="text" name="Region" id="" class="trible"><br>
                        <label for="">Addresse</label>
                        <input type="text" name="Staff_address" id="" class="full" required>
                    </div>
                    <div class="section_new">
                        <h5>Professional Information</h5>
                        <label for="">Position Applied</label>
                        <select name="Posstion" id="" class="full" required>
                            <option value="0">non</option>
                            <?php 
                                $sql=$con->prepare('SELECT Possition_ID,Possition_Name FROM tblpossition_request WHERE active_postion = 1');
                                $sql->execute();
                                $posstions = $sql->fetchAll();
                                foreach($posstions as $pos){
                                    echo '
                                        <option value="'.$pos['Possition_ID'].'">'.$pos['Possition_Name'].'</option>
                                    ';
                                }
                            ?>
                        </select><br>
                        <label for="">Start Date</label>
                        <input type="date" name="Start_date" id="" class="trible" required>
                        <label for="">Expected Salary</label>
                        <input type="number" name="expected_sallary" id="" step="0.01" class="trible">/per hour <br>
                        <label for="" style="width: 200px;">Work Hours Preference</label>
                        <input type="number" name="Work_hours" id="" class="trible">
                        <label for="" style="width: 250px;">Are you currently employed?</label>
                        <select name="now_work" id="" class="trible">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select><br>
                        <label for="">Notice Period</label>
                        <input type="text" name="Notice_Period" id="" class="full">
                    </div>
                    <div class="section_new">
                        <h5>Technical Background</h5>
                        <label for="" style="width: 250px;">Years of Experience</label>
                        <input type="number" name="Experiance_years" id="" class="double" required><br>
                        <label for="" style="width: 250px;">Programming Languages ​​Known</label>
                        <input type="text" name="Programing_lang" id="" class="double"><br>
                        <label for="" style="width: 250px;">Frameworks or Libraries Used</label>
                        <input type="text" name="Framworks" id="" class="double"><br>
                        <label for="">Link Experiance</label>
                        <input type="text" name="link_experinace" id="" class="full"><br>
                        <label for="">Link githup</label>
                        <input type="text" name="link_github" id="" class="full">
                    </div>
                    <div class="section_new">
                        <h5>Other Daitails</h5>
                        <label for="" style="width: 250px;">Communication Language</label>
                        <select name="Comunication_Lang" id="" class="double">
                            <option value="Ar">Arabic</option>
                            <option value="En">English</option>
                            <option value="Ge">German</option>
                            <option value="Es">Spanish</option>
                            <option value="Fr">Fransh</option>
                        </select><br>
                        <label for="">Cv</label>
                        <input type="file" name="staff_CV" id="" class="full"><br>
                        <label for="">Presonal Photo</label>
                        <input type="file" name="Staff_Photo" id="" class="full"><br>
                        <label for="">Front ID</label>
                        <input type="file" name="Front_ID" id="" class="trible">
                        <label for="">Back ID</label>
                        <input type="file" name="Back_ID" id=""class="trible"><br>
                        <label for="">Trasfer Money</label>
                        <select name="Transfer_Money" id="" class="full">
                            <?php
                                $sql=$con->prepare('SELECT paymentmethodD,methot FROM  tblpayment_method WHERE paymentmethodD !=3');
                                $sql->execute();
                                $payments= $sql->fetchAll();
                                foreach($payments as $payment){
                                    echo '
                                        <option value="'.$payment['paymentmethodD'].'">'.$payment['methot'].'</option>
                                    ';
                                }
                            ?>
                        </select><br>
                        <label for="">Trasfer Note</label>
                        <input type="text" name="Transfer_Note" id="" placeholder="Like Iban" class="full">
                    </div>
                    <div class="section_new">
                        <label for="">Password</label>
                        <input type="password" name="Password" id="" class="full" required><br>
                        <label for="">Conform Password</label>
                        <input type="password" name="con_Password" id="" class="full" required><br>
                    </div>
                    <div class="save_form">
                        <button type="reset">Cancel</button>
                        <button type="submit" name="btnsend">Send </button>
                    </div>
                </form>
                <?php
                    if(isset($_POST['btnsend'])){
                        // Personal Information
                        $Fname             = $_POST['Fname'];
                        $MidelName         = $_POST['MidelName'];
                        $LName             = $_POST['LName'];
                        $Gender            = $_POST['Gender'];
                        $Nationality       = $_POST['Nationality'];
                        $Staff_Phone       = $_POST['Staff_Phone'];
                        $Staff_email       = $_POST['Staff_email'];
                        $Staff_Country     = $_POST['Staff_Country'];
                        $Region            = $_POST['Region'];
                        $Staff_address     = $_POST['Staff_address'];

                        // Professional Information
                        $Posstion          = $_POST['Posstion'];
                        $Start_date        = $_POST['Start_date'];
                        $expected_sallary  = $_POST['expected_sallary'];
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

                        // Files (from $_FILES)
                        $staff_CV          = $_FILES['staff_CV'];
                        $Staff_Photo       = $_FILES['Staff_Photo'];
                        $Front_ID          = $_FILES['Front_ID'];
                        $Back_ID           = $_FILES['Back_ID'];

                        // Security
                        $Password          = $_POST['Password'];
                        $con_Password      = $_POST['con_Password'];

                        //check if the email exist
                        $checkemail = checkItem('Staff_email','tblstaff', $Staff_email);

                        if($checkemail == 0){
                            //check if the password is equal 
                            if($Password == $con_Password){
                                $targetDir       = '../Documents/';
                                $newCvname       = uploadFile($_FILES['staff_CV'],$targetDir);
                                $newstaff_photo  = uploadFile($_FILES['Staff_Photo'],$targetDir);
                                $newFront_ID     = uploadFile($_FILES['Front_ID'],$targetDir);
                                $newBack_ID      = uploadFile($_FILES['Back_ID'],$targetDir);

                                
                                
                                $sql = $con->prepare('INSERT INTO tblstaff 
                                                        (Fname, MidelName, LName, Gender,DOB, Nationality, Staff_Phone, Staff_email, Staff_Country, Region, Staff_address,
                                                        Posstion, Start_date, expected_sallary, Work_hours, now_work, Notice_Period, Experiance_years, Programing_lang, 
                                                        Framworks, link_experinace, link_github, Comunication_Lang, staff_CV, Staff_Photo, Front_ID, Back_ID, 
                                                        Transfer_Money, Transfer_Note, staffPassword, block, accepted) 
                                                    VALUES 
                                                        (?, ?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

                                $sql->execute([
                                                $_POST['Fname'],
                                                $_POST['MidelName'],
                                                $_POST['LName'],
                                                $_POST['Gender'],
                                                $_POST['DOB'],
                                                $_POST['Nationality'],
                                                $_POST['Staff_Phone'],
                                                $_POST['Staff_email'],
                                                $_POST['Staff_Country'],
                                                $_POST['Region'],
                                                $_POST['Staff_address'],
                                                $_POST['Posstion'],
                                                $_POST['Start_date'],
                                                $_POST['expected_sallary'],
                                                $_POST['Work_hours'],
                                                $_POST['now_work'],
                                                $_POST['Notice_Period'],
                                                $_POST['Experiance_years'],
                                                $_POST['Programing_lang'],
                                                $_POST['Framworks'],
                                                $_POST['link_experinace'],
                                                $_POST['link_github'],
                                                $_POST['Comunication_Lang'],
                                                $newCvname,
                                                $newstaff_photo,
                                                $newFront_ID,
                                                $newBack_ID,
                                                $_POST['Transfer_Money'],
                                                $_POST['Transfer_Note'],
                                                sha1($_POST['Password']), // hashed password
                                                0,  // block
                                                0   // accepted
                                            ]);

                                            



                            }
                        }
                    }
                ?>
            </div>
        <?php
        }else{

        }
    ?> 
    <?php include '../common/jslinks.php' ?>
    <script src="js/index.js"></script>
</body>