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
                <h4>Setting Status</h4>
                <div class="cards_Services">
                    <div class="card_staus card1">
                        <h1>Countris</h1>
                    </div>
                    <div class="card_staus card2">
                        <h1>Payment Method</h1>
                    </div>
                    <div class="card_staus card3">
                        <h1>Domein Types</h1>
                    </div>
                    <div class="card_staus card4">
                        <h1>Ticket Types</h1>
                    </div>
                    <div class="card_staus card5">
                        <h1>Status Domein</h1>
                    </div>
                    <div class="card_staus card6">
                        <h1>Status Invoice</h1>
                    </div>
                    <div class="card_staus card7">
                        <h1>Status Invoices</h1>
                    </div>
                    <div class="card_staus card8">
                        <h1>Status Tickets</h1>
                    </div>
                </div>
                <div class="ourteam">
                    <h4>Our team</h4>
                    <div class="set_team">
                    <?php
                        $doworkers= (isset($_GET['doworker']))?$_GET['doworker']:'manage';

                        if($doworkers == 'manage'){
                            $sql=$con->prepare('SELECT * FROM tblourworkers');
                            $sql->execute();
                            $workers = $sql->fetchAll();
                            foreach ($workers as $per){
                                echo '
                                <div class="card_person">
                                    <div class="img_person">
                                        <img src="../images/ourteam/'.$per['workerimg'].'" alt="">
                                    </div>
                                    <h3>'.$per['workerName'].'</h3>
                                    <p>'.$per['workerDiscription'].'</p>
                                    <a href="ManageSetting.php?doworker=delete&id='.$per['workerID'].'" class="btn btn-danger">Delete</a>
                                </div>
                                ';
                            }
                            echo '
                                <div class="card_person">
                                    <a href="ManageSetting.php?doworker=add" class="alert alert-success" id="newworker">New Worker</a>
                                </div>
                            ';
                        }elseif($doworkers == 'add'){?>
                            <div class="add_worker">
                                <h3>Add a new Worker</h3>
                                <form action="" method="post" enctype="multipart/form-data">
                                    <table>
                                        <tr>
                                            <td><label for="">Name: </label></td>
                                            <td><input type="text" name="workerName" id=""></td>
                                        </tr>
                                        <tr>
                                            <td><label for="">Discription</label></td>
                                            <td><input type="text" name="workerDiscription" id=""></td>
                                        </tr>
                                        <tr>
                                            <td><label for="">E-mail</label></td>
                                            <td><input type="email" name="Workeremail" id=""></td>
                                        </tr>
                                        <tr>
                                            <td><label for="">Image</label></td>
                                            <td><input type="file" name="workerimg" id=""></td>
                                        </tr>
                                    </table>
                                    <div class="controladd">
                                        <button type="submit" name="btnnewworker" class="btn btn-success">Add Worker</button>
                                    </div>
                                </form>
                                <?php
                                    if(isset($_POST['btnnewworker'])){
                                        $temp=explode(".",$_FILES['workerimg']['name']);
                                        $newfilename=round(microtime(true)).'.'.end($temp);
                                        move_uploaded_file($_FILES['workerimg']['tmp_name'],'../images/ourteam/'.$newfilename);
                                        $workerimg          = $newfilename;
                                        $workerName         = $_POST['workerName'];
                                        $workerDiscription  = $_POST['workerDiscription'] ;
                                        $Workeremail        = $_POST['Workeremail'] ;

                                        $sql=$con->prepare('INSERT INTO tblourworkers (workerimg,workerName,workerDiscription,Workeremail) 
                                                            VALUES (:workerimg,:workerName,:workerDiscription,:Workeremail)');
                                        $sql->execute(array(
                                            'workerimg'         => $workerimg ,
                                            'workerName'        => $workerName,
                                            'workerDiscription' => $workerDiscription ,
                                            'Workeremail'       => $Workeremail
                                        ));
                                        echo '<script> location.href= "ManageSetting.php" </script>';
                                    }
                                ?>
                            </div>
                        <?php
                        }elseif($doworkers == 'delete'){
                            $workerID = (isset($_GET['id']))?$_GET['id']:0;
                            $checkworker = checkItem('workerID','tblourworkers',$workerID);

                            if($checkworker == 1){
                                $sql=$con->prepare('SELECT workerimg FROM tblourworkers WHERE workerID=?');
                                $sql->execute(array($workerID));
                                $pic = $sql->fetch();
                                $filename='../images/ourteam/'.$pic['workerimg'];
                                unlink($filename);
                                $stat=$con->prepare('DELETE FROM tblourworkers WHERE workerID=?');
                                $stat ->execute(array($workerID));
                                echo '<script> location.href= "ManageSetting.php" </script>';
                            }else{
                                echo '<script> location.href= "ManageSetting.php" </script>';
                            }
                        }

                    ?>

                </div>
                </div>
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