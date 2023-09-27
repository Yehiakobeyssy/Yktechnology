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
?>
    <link rel="stylesheet" href="css/ManageService.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Manage Service</h1>
                <a href="ManageService.php?do=add" class="btn btn-success">New Category</a>
            </div>
            <div class="result_container">
                <?php
                    $do= (isset($_GET['do']))?$_GET['do']:'manage';

                    if($do == 'manage'){?>
                        <div class="resultmanage">
                            <?php
                                $sql=$con->prepare('SELECT * FROM tblcategory WHERE Cat_Active=1');
                                $sql->execute();
                                $cards = $sql->fetchAll();
                                foreach($cards as $card){
                                    echo '
                                    <div class="card_cat">
                                        <img src="../images/Services/'.$card['Category_Icon'].'" alt="">
                                        <div class="dis">
                                            <h3>'.$card['Category_Name'].'</h3>
                                            <p>'.$card['Cat_Discription'].'</p>
                                        </div>
                                        <div class="control_man">
                                            <a href="" class="btn btn-primary"> Services</a>
                                            <a href="ManageService.php?do=edid&catID='.$card['Cat_ID'].'" class="btn btn-warning"> Edid</a>
                                            <a href="ManageService.php?do=delete&catID='.$card['Cat_ID'].'" class="btn btn-danger"> Delete</a>
                                        </div>
                                    </div>
                                    ';
                                }
                            ?>
                        </div>
                    <?php
                    }elseif($do =='add'){?>
                        <div class="addform">
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="addtitle">
                                    <h3>New Category</h3>
                                </div>
                                <div class="container_add">
                                        <table>
                                            <tr>
                                                <td><label for="">Category Name</label></td>
                                                <td><input type="text" name="Category_Name" id="" required></td>
                                            </tr>
                                            <tr>
                                                <td><label for="">Discription</label></td>
                                                <td><textarea name="Cat_Discription" id=""  rows="10" required></textarea></td>
                                            </tr>
                                            <tr>
                                                <td><label for="">Logo Cat</label></td>
                                                <td><input type="file" name="Category_Icon" id=""></td>
                                            </tr>
                                        </table>
                                </div>
                                <div class="btncontrol">
                                    <button type="submit" class="btn btn-success" name="btnsave" >add</button>
                                </div>
                            </form>
                            <?php
                                if(isset($_POST['btnsave'])){
                                    $temp=explode(".",$_FILES['Category_Icon']['name']);
                                    $newfilename=round(microtime(true)).'.'.end($temp);
                                    move_uploaded_file($_FILES['Category_Icon']['tmp_name'],'../images/Services/'.$newfilename);

                                    $Category_Icon   = $newfilename;
                                    $Category_Name   = $_POST['Category_Name'];
                                    $Cat_Discription = $_POST['Cat_Discription'];
                                    $Cat_Active      =1;

                                    $sql=$con->prepare('INSERT INTO tblcategory (Category_Icon,Category_Name,Cat_Discription,Cat_Active)
                                                        VALUES (:Category_Icon,:Category_Name,:Cat_Discription,:Cat_Active)');
                                    $sql->execute(array(
                                        'Category_Icon'     => $Category_Icon,
                                        'Category_Name'     => $Category_Name,
                                        'Cat_Discription'   => $Cat_Discription,
                                        'Cat_Active'        => $Cat_Active
                                    ));

                                    echo '
                                    <div class="alert alert-success" role="alert">
                                        Category added 
                                    </div>
                                ';
                                }
                            ?>
                        </div>
                    <?php
                    }elseif($do =='edid'){
                        $catID=(isset($_GET['catID']))?$_GET['catID']:0;
                        $checkcatID= checkItem('Cat_ID','tblcategory',$catID);
                        if($checkcatID == 1){
                            $sql=$con->prepare('SELECT * FROM tblcategory WHERE Cat_ID=?');
                            $sql->execute(array($catID));
                            $cat_info=$sql->fetch();
                            ?>

                        <div class="addform">
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="addtitle">
                                    <h3>EDID Category</h3>
                                </div>
                                <div class="container_add">
                                        <table>
                                            <tr>
                                                <td><label for="">Category Name</label></td>
                                                <td><input type="text" name="Category_Name" id="" value="<?php echo $cat_info['Category_Name'] ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><label for="">Discription</label></td>
                                                <td><textarea name="Cat_Discription" id=""  rows="10"><?php echo $cat_info['Cat_Discription'] ?></textarea></td>
                                            </tr>
                                            <tr>
                                                <td><label for="">Logo Cat</label></td>
                                                <td><input type="file" name="Category_Icon" id=""></td>
                                            </tr>
                                        </table>
                                </div>
                                <div class="btncontrol">
                                    <button type="submit" class="btn btn-warning" name="btnEdid" >Edid</button>
                                </div>
                            </form>
                            <?php
                                if(isset($_POST['btnEdid'])){
                                    if(!empty($_FILES['Category_Icon']['name'])){
                                        $filename='../images/Services/'.$cat_info['Category_Icon'];
                                        unlink($filename);
                                        $temp=explode(".",$_FILES['Category_Icon']['name']);
                                        $newfilename=round(microtime(true)).'.'.end($temp);
                                        move_uploaded_file($_FILES['Category_Icon']['tmp_name'],'../images/Services/'.$newfilename);
                                    }else{
                                        $newfilename = $cat_info['Category_Icon'];
                                    }

                                    $Category_Icon   = $newfilename;
                                    $Category_Name   = $_POST['Category_Name'];
                                    $Cat_Discription = $_POST['Cat_Discription'];
                                    $Cat_Active      = 1;

                                    $sql=$con->prepare('UPDATE  tblcategory 
                                                        SET     Category_Icon   = :Category_Icon,
                                                                Category_Name   = :Category_Name,
                                                                Cat_Discription = :Cat_Discription,
                                                                Cat_Active      = :Cat_Active
                                                        WHERE   Cat_ID          = :Cat_ID');
                                    $sql->execute(array(
                                        'Category_Icon'     => $Category_Icon,
                                        'Category_Name'     => $Category_Name,
                                        'Cat_Discription'   => $Cat_Discription,
                                        'Cat_Active'        => $Cat_Active,
                                        'Cat_ID'            => $catID
                                    ));

                                    echo '<script> location.href="ManageService.php"</script>';
                                }
                            ?>
                        </div>

                        <?php
                        }else{
                            echo '<script> location.href="ManageService.php"</script>';;
                        }
                    }elseif($do== 'delete'){
                        $catID=(isset($_GET['catID']))?$_GET['catID']:0;
                        $checkcatID= checkItem('Cat_ID','tblcategory',$catID);
                        if($checkcatID == 1){
                            $sql=$con->prepare('UPDATE tblcategory SET Cat_Active =  0 WHERE Cat_ID = ?');
                            $sql->execute(array($catID));
                            echo '<script> location.href="ManageService.php"</script>';;
                        }else{  
                            echo '<script> location.href="ManageService.php"</script>';;
                        }
                    }else{
                        header('location:index.php');
                    }
                ?>
            </div>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/ManageService.js"></script>
    <script src="js/sidebar.js"></script>
</body>