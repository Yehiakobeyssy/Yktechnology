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
    <link rel="stylesheet" href="css/ManageSetting.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Setting</h1>
            </div>
            <div class="result">
                <div class="slideshow">
                    <h4>Slide show</h4>
                    <p>double click do delete the image in slide show </p>
                    <?php
                        $doslide= (isset($_GET['doslide']))?$_GET['doslide']:'manage';
                        if($doslide == 'manage'){?>
                            <div class="cards_slide">
                                <?php
                                    $sql=$con->prepare('SELECT slideimg,slideID FROM tblslideshow WHERE slideactive=1');
                                    $sql->execute();
                                    $slids=$sql->fetchAll();
                                    foreach($slids as $slide){
                                        echo '
                                            <div class="card_slide" data-index="'.$slide['slideID'].'">
                                                <img src="../images/slideshow/'.$slide['slideimg'].'" alt="">
                                            </div>
                                        ';
                                    }
                                ?>
                                <div class="card_slide">
                                    <a href="ManageSetting.php?doslide=add" class="alert alert-success"> add new slide</a>
                                </div>
                            </div>
                        <?php
                        }elseif($doslide== 'add'){?>
                            <div class="addpicslide">
                                <form action="" method="post" enctype="multipart/form-data">
                                    <label for="">Load the picure</label>
                                    <input type="file" name="txtslideshow" id=""><br>
                                    <button type="submit" class="btn btn-success" name="btnnewslide">Save</button>
                                </form>
                                <?php
                                    if(isset($_POST['btnnewslide'])){
                                        $temp=explode(".",$_FILES['txtslideshow']['name']);
                                        $newfilename=round(microtime(true)).'.'.end($temp);
                                        move_uploaded_file($_FILES['txtslideshow']['tmp_name'],'../images/slideshow/'.$newfilename);

                                        $slideimg    = $newfilename;
                                        $slideactive = 1;

                                        $sql=$con->prepare('INSERT INTO tblslideshow (slideimg,slideactive) VALUES (:slideimg,:slideactive)');
                                        $sql->execute(array(
                                            'slideimg'      => $slideimg,
                                            'slideactive'   => $slideactive 
                                        ));

                                        echo '<script> location.href= "ManageSetting.php" </script>';
                                    }
                                ?>
                            </div>
                        <?php
                        }elseif($doslide=='delete'){        
                            $slideID = (isset($_GET['slideid']))?$_GET['slideid']:0;
                            $checkslideID = checkItem('slideID','tblslideshow',$slideID);
                            if($checkslideID == 1){
                                $sql=$con->prepare('SELECT slideimg FROM tblslideshow WHERE slideID=?');
                                $sql->execute(array($slideID));
                                $result=$sql->fetch();
                                $filename='../images/slideshow/'.$result['slideimg'];
                                unlink($filename);
                                $stat=$con->prepare('DELETE FROM tblslideshow WHERE slideID=?');
                                $stat->execute(array($slideID));
                                echo '<script> location.href= "ManageSetting.php" </script>';
                            }else{
                                echo '<script> location.href= "ManageSetting.php" </script>';
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/ManageSetting.js"></script>
    <script src="js/sidebar.js"></script>
</body>