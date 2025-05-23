<?php
    session_start();

    if(isset($_COOKIE['staff'])){
        header('location:dashboard.php');
    }elseif(isset($_SESSION['staff'])){
        header('location:dashboard.php');
    }

    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';

    $do= (isset($_GET['do']))?$_GET['do']:'login';

    $do='signup'
?>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <?php
        if($do=='login'){

        }elseif($do=='signup'){?>
            <div class="new_staff">
                <div class="title">
                    <h3>Job Application Form</h3>
                    <label for="">Please fill in this form in English and not ignore any fild</label>
                </div>
                <form action="" method="post">
                    <div class="personalinformation section_new">
                        <h4>Personal Information</h4>
                        <label for="">Full Name :</label>
                        <input type="text" name="" id="" placeholder=" First Name" class="trible">
                        <input type="text" name="" id="" placeholder=" Mitel Name" class="trible">
                        <input type="text" name="" id="" placeholder=" Last Name " class="trible"><br>
                        <label for="">Gender</label>
                        <select name="" id="" class="trible">
                            <option value="1">Male</option>
                            <option value="2">Female</option>
                        </select>
                        <label for="">Nationality</label>
                        <select name="" id="" class="double">
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
                        <input type="text" name="" id=""class="trible">
                        <label for="">E-mail</label>
                        <input type="email" name="" id=""class="double"><br>

                        <h6>Addresse</h6>
                        <label for="">Country</label>
                        <select name="" id="" class="double">
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
                        <input type="text" name="" id="" class="trible"><br>
                        <label for="">Addresse</label>
                        <input type="text" name="" id="" class="full">
                    </div>
                    <div class="section_new">
                        <h5>Professional Information</h5>
                        <label for="">Position Applied</label>
                        <select name="" id="" class="full">
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
                        <input type="date" name="" id="" class="trible">
                        <label for="">Expected Salary</label>
                        <input type="number" name="" id="" step="0.01" class="trible">/per hour <br>
                        <label for="" style="width: 200px;">Work Hours Preference</label>
                        <input type="number" name="" id="" class="trible">
                        <label for="" style="width: 250px;">Are you currently employed?</label>
                        <select name="" id="" class="trible">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select><br>
                        <label for="">Notice Period</label>
                        <input type="text" name="" id="" class="full">
                    </div>
                    <div class="section_new">
                        <h5>Technical Background</h5>
                        <label for="" style="width: 250px;">Years of Experience</label>
                        <input type="number" name="" id="" class="double"><br>
                        <label for="" style="width: 250px;">Programming Languages ​​Known</label>
                        <input type="text" name="" id="" class="double"><br>
                        <label for="" style="width: 250px;">Frameworks or Libraries Used</label>
                        <input type="text" name="" id="" class="double"><br>
                        <label for="">Link Experiance</label>
                        <input type="text" name="" id="" class="full"><br>
                        <label for="">Link githup</label>
                        <input type="text" name="" id="" class="full">
                    </div>
                    <div class="section_new">
                        <h5>Other Daitails</h5>
                        <label for="" style="width: 250px;">Communication Language</label>
                        <select name="" id="" class="double">
                            <option value="Ar">Arabic</option>
                            <option value="En">English</option>
                            <option value="Ge">German</option>
                            <option value="Es">Spanish</option>
                            <option value="Fr">Fransh</option>
                        </select><br>
                        <label for="">Cv</label>
                        <input type="file" name="" id="" class="full"><br>
                        <label for="">Presonal Photo</label>
                        <input type="file" name="" id="" class="full"><br>
                        <label for="">Front ID</label>
                        <input type="file" name="" id="" class="trible">
                        <label for="">Back ID</label>
                        <input type="file" name="" id=""class="trible"><br>
                        <label for="">Trasfer Money</label>
                        <select name="" id="" class="full">
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
                        <input type="text" name="" id="" placeholder="Like Iban" class="full">
                    </div>
                    <div class="section_new">
                        <label for="">Password</label>
                        <input type="password" name="" id="" class="full"><br>
                        <label for="">Conform Password</label>
                        <input type="password" name="" id="" class="full"><br>
                    </div>
                    <div class="save_form">
                        <button type="reset">Cancel</button>
                        <button type="submit">Send </button>
                    </div>
                </form>
            </div>
        <?php
        }else{

        }
    ?>
    <?php include '../common/jslinks.php' ?>
    <script src="js/index.js"></script>
</body>