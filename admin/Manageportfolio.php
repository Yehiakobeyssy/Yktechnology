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
    <link rel="stylesheet" href="css/Manageportfolio.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Manage Portfolio</h1>
                
            </div>
            <?php
                $do = (isset($_GET['do']))?$_GET['do']:'manage';
                if($do=='manage'){?>
                    <div class="result_manage">
                        <?php
                            $sql=$con->prepare('SELECT * FROM tblportfolio WHERE portfolio_Active= 1');
                            $sql->execute();
                            $cards = $sql->fetchAll();
                            foreach($cards as $card){
                                echo '
                                    <div class="card_pro">
                                        <img src="../images/Profolio/'.$card['portfolio_Pic'].'" alt="" srcset="">
                                        <h3>'.$card['portfolio_Title'].'</h3>
                                        <p>Duration : <span>'.$card['Duration_working'].'</span></p>
                                        <p>languge use : <span>'.$card['Lan_use'].'</span></p>
                                        <a href="https://'.$card['linkWebsite'].'" class="btn btn-secondary btnpro">live Demo</a>
                                        <button class="btn btn-primary btnpro btnshowdis" data-index="'.$card['portfolio_ID'].'">show disction</button>
                                        <a href="Manageportfolio.php?do=edid&proID='.$card['portfolio_ID'].'" class="btn btn-warning btnpro">Edid</a>
                                        <a href="Manageportfolio.php?do=delete&proID='.$card['portfolio_ID'].'" class="btn btn-danger btnpro">Delete</a>
                                    </div>
                                ';
                            }
                        ?>
                        <div class="card_pro">
                            <button  class="alert alert-success" id="addnew">Add new</button>
                        </div>
                    </div>
                    <div class="popupdiscription">
                        <div class="container_popup">
                            <div class="closepopup">+</div>
                            <p id="showdiscription"></p>
                        </div>
                    </div>
                <?php
                }elseif($do=='add'){?>
                    <div class="addform">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="addtitle">
                                <h3>New Portfolio</h3>
                            </div>
                            <div class="container_add">
                                    <table>
                                        <tr>
                                            <td><label for="">Title :</label></td>
                                            <td><input type="text" name="txttitle" id="" required></td>
                                        </tr>
                                        <tr>
                                            <td><label for="">Duration Work</label></td>
                                            <td><input type="text" name="txtduration" id="" required></td>
                                        </tr>
                                        <tr>
                                            <td><label for="">Langues use </label></td>
                                            <td><input type="text" name="txtlang" id=""></td>
                                        </tr>
                                        <tr>
                                            <td><label for="">Link Live</label></td>
                                            <td><input type="text" name="txtlink" id=""></td>
                                        </tr>
                                        <tr>
                                            <td><label for="">Discription</label></td>
                                            <td><textarea name="txtdisc" id=""  rows="10"></textarea></td>
                                        </tr>
                                        <tr>
                                            <td><label for="">Photo</label></td>
                                            <td><input type="file" name="portfoliopic" id="" required></td>
                                        </tr>
                                    </table>
                            </div>
                            <div class="btncontrol">
                                <button type="submit" class="btn btn-success" name="btnsave" >Save</button>
                            </div>
                        </form>
                        <?php
                            if(isset($_POST['btnsave'])){
                                $temp=explode(".",$_FILES['portfoliopic']['name']);
                                $newfilename=round(microtime(true)).'.'.end($temp);
                                move_uploaded_file($_FILES['portfoliopic']['tmp_name'],'../images/Profolio/'.$newfilename);
                                $portfolio_Title    = $_POST['txttitle'];
                                $portfolio_Pic      = $newfilename;
                                $Duration_working   = $_POST['txtduration'];
                                $Lan_use            = $_POST['txtlang'];
                                $Discription        = $_POST['txtdisc'];
                                $linkWebsite        = $_POST['txtlink'];
                                $portfolio_Active   = 1;

                                $sql=$con->prepare('INSERT INTO  tblportfolio (portfolio_Title,portfolio_Pic,Duration_working,Lan_use,Discription,linkWebsite,portfolio_Active)
                                                    VALUES (:portfolio_Title,:portfolio_Pic,:Duration_working,:Lan_use,:Discription,:linkWebsite,:portfolio_Active)');
                                $sql->execute(array(
                                    'portfolio_Title'   => $portfolio_Title,
                                    'portfolio_Pic'     => $portfolio_Pic,
                                    'Duration_working'  => $Duration_working,
                                    'Lan_use'           => $Lan_use,
                                    'Discription'       => $Discription,
                                    'linkWebsite'       => $linkWebsite,
                                    'portfolio_Active'  => $portfolio_Active
                                ));
                                
                                echo '
                                    <div class="alert alert-success" role="alert">
                                        Portfolio added 
                                    </div>
                                ';
                            }
                        ?>
                    </div>
                <?php
                }elseif($do=='edid'){
                    $proID=(isset($_GET['proID']))?$_GET['proID']:0;
                    $checkporId= checkItem('portfolio_ID','tblportfolio',$proID);
                    if($checkporId == 1){
                        $sql=$con->prepare('SELECT * FROM tblportfolio WHERE portfolio_ID = ?');
                        $sql->execute(array($proID));
                        $result_pro=$sql->fetch();?>
                        <div class="addform">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="addtitle">
                                <h3>Edit Portfolio</h3>
                            </div>
                            <div class="container_add">
                                    <table>
                                        <tr>
                                            <td><label for="">Title :</label></td>
                                            <td><input type="text" name="txttitle" id="" value="<?php echo $result_pro['portfolio_Title']  ?>" required></td>
                                        </tr>
                                        <tr>
                                            <td><label for="">Duration Work</label></td>
                                            <td><input type="text" name="txtduration" id=""  value="<?php echo $result_pro['Duration_working']  ?>" required></td>
                                        </tr>
                                        <tr>
                                            <td><label for="">Langues use </label></td>
                                            <td><input type="text" name="txtlang" id="" value="<?php echo $result_pro['Lan_use']  ?>"></td>
                                        </tr>
                                        <tr>
                                            <td><label for="">Link Live</label></td>
                                            <td><input type="text" name="txtlink" id="" value="<?php echo $result_pro['linkWebsite']  ?>"></td>
                                        </tr>
                                        <tr>
                                            <td><label for="">Discription</label></td>
                                            <td><textarea name="txtdisc" id=""  rows="10"><?php echo $result_pro['Discription']?></textarea></td>
                                        </tr>
                                        <tr>
                                            <td><label for="">Photo</label></td>
                                            <td><input type="file" name="portfoliopic" id="" ></td>
                                        </tr>
                                    </table>
                            </div>
                            <div class="btncontrol">
                                <button type="submit" class="btn btn-warning" name="btnEdid" >Edit</button>
                            </div>
                        </form>
                        <?php
                            if(isset($_POST['btnEdid'])){
                                if(!empty($_POST['portfoliopic'])){
                                    $filename='../images/Profolio/'.$result_pro['portfolio_Pic'];
                                    unlink($filename);
                                    $temp=explode(".",$_FILES['portfoliopic']['name']);
                                    $newfilename=round(microtime(true)).'.'.end($temp);
                                    move_uploaded_file($_FILES['portfoliopic']['tmp_name'],'../images/Profolio/'.$newfilename);
                                }else{
                                    $newfilename = $result_pro['portfolio_Pic'];
                                }
                                $portfolio_Title    = $_POST['txttitle'];
                                $portfolio_Pic      = $newfilename;
                                $Duration_working   = $_POST['txtduration'];
                                $Lan_use            = $_POST['txtlang'];
                                $Discription        = $_POST['txtdisc'];
                                $linkWebsite        = $_POST['txtlink'];
                                $portfolio_Active   = 1;

                                $sql=$con->prepare('UPDATE  tblportfolio  
                                                    SET     portfolio_Title  = :portfolio_Title,
                                                            portfolio_Pic    = :portfolio_Pic,
                                                            Duration_working = :Duration_working,
                                                            Lan_use          = :Lan_use,
                                                            Discription      = :Discription,
                                                            linkWebsite      = :linkWebsite,
                                                            portfolio_Active = :portfolio_Active
                                                    WHERE   portfolio_ID   = :portfolio_ID');
                                $sql->execute(array(
                                    'portfolio_Title'   => $portfolio_Title,
                                    'portfolio_Pic'     => $portfolio_Pic,
                                    'Duration_working'  => $Duration_working,
                                    'Lan_use'           => $Lan_use,
                                    'Discription'       => $Discription,
                                    'linkWebsite'       => $linkWebsite,
                                    'portfolio_Active'  => $portfolio_Active,
                                    'portfolio_ID'      => $proID
                                ));
                                
                                echo '<script> location.href="Manageportfolio.php"</script>';
                            }
                        ?>
                    </div>
                    <?php
                    }else{
                        echo '<script> location.href="Manageportfolio.php"</script>';
                    }

                }elseif($do=='delete'){
                    $proID=(isset($_GET['proID']))?$_GET['proID']:0;
                    $checkporId= checkItem('portfolio_ID','tblportfolio',$proID);
                    if($checkporId == 1){
                        $sql=$con->prepare('UPDATE tblportfolio SET portfolio_Active = 0 WHERE portfolio_ID =?');
                        $sql->execute(array($proID));
                        echo '<script> location.href="Manageportfolio.php"</script>';
                    }else{
                        echo '<script> location.href="Manageportfolio.php"</script>';
                    }
                }else{
                    header('location:index.php');
                }
            ?>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/Manageportfolio.js"></script>
    <script src="js/sidebar.js"></script>
</body>