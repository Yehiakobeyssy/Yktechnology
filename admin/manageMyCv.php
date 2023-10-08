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

    $sql=$con->prepare('SELECT Cv_text,Cv_pic FROM tblsetting WHERE SettingID= 1');
    $sql->execute();
    $result=$sql->fetch();

?>
    <link rel="stylesheet" href="css/manageMyCv.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Edit Cv Section</h1>
            </div>
            <div class="frmCV">
                <form action="" method="post" enctype="multipart/form-data">
                    <label for="">My Cv</label>
                    <textarea name="txtmyCv" id=""  rows="20"><?php echo $result['Cv_text'] ?></textarea>
                    <label for="">My Photo</label>
                    <input type="file" name="myphoto" id="myphoto">
                    <div class="btncotrol">
                        <button type="submit" name="btnEdit">Edit</button>
                    </div>
                </form>
                <?php
                    if(isset($_POST['btnEdit'])){
                        if(!empty($_FILES['myphoto']['name'])){
                            $filename='../images/synpoles/'.$result['Cv_pic'];
                            unlink($filename);
                            $temp=explode(".",$_FILES['myphoto']['name']);
                            $newfilename=round(microtime(true)).'.'.end($temp);
                            move_uploaded_file($_FILES['myphoto']['tmp_name'],'../images/synpoles/'.$newfilename);
                        }else{
                            $newfilename =$result['Cv_pic'];
                        }

                        $cvtext = $_POST['txtmyCv'];
                        $cvpic = $newfilename;

                        $sql=$con->prepare('UPDATE tblsetting SET Cv_text = ? , Cv_pic = ? WHERE SettingID = 1');
                        $sql->execute(array($cvtext,$cvpic));

                        echo '<script> location.href.reload() </script>';
                    }
                ?>
            </div>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/manageMyCv.js"></script>
    <script src="js/sidebar.js"></script>
</body>