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
    <link rel="stylesheet" href="css/ServiceCatalog.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1> Service Catalog</h1>
                <a href="ServiceCatalog.php?do=add" class="btn btn-success btnmewservice">Add New</a>
            </div>
            <?php
            $do= (isset($_GET['do']))?$_GET['do']:'manage';

            if($do=='manage'){?>
                <div class="searchbox">
                    <input type="text" name="" id="txtsearch" placeholder="Search ...">
                </div>
                <div class="result_cards"></div>
            <?php
            }elseif($do=='add'){?>
            <div class="addnewdiscription">
                <h2>New Catalog</h2>
                <form method="post" enctype="multipart/form-data">
                    <label for="subject">Subject <span>*</span></label>
                    <input type="text" id="subject" name="subject" required>
                    <label for="description">Description</label>
                    <textarea id="description" name="description"></textarea>
                    <label for="profileimage">Profile Image</label>
                    <input type="file" id="profileimage" name="profileimage" accept="image/*">
                    <label for="filepath">File Path</label>
                    <input type="file" id="filepath" name="filepath">
                    <div class="btnsubmit">
                    <button type="submit" name="btnnewservice">Save</button>
                    </div>
                </form>
                <?php
                    if(isset($_POST['btnnewservice'])){
                        $temp=explode(".",$_FILES['filepath']['name']);
                        $newfilename=round(microtime(true)).'.'.end($temp);
                        move_uploaded_file($_FILES['filepath']['tmp_name'],'../images/libary/'.$newfilename); 
                        
                        $temp1=explode(".",$_FILES['profileimage']['name']);
                        $newfilename1=round(microtime(true)).'.'.end($temp1);
                        move_uploaded_file($_FILES['profileimage']['tmp_name'],'../images/libary/'.$newfilename1);

                        $image       = $newfilename1;
                        $file        = $newfilename;
                        $Subject     = $_POST['subject'];
                        $discription = $_POST['description'];

                        $sql=$con->prepare('INSERT INTO tbllibrary (image,file,Subject,discription) 
                                            VALUES (:image,:file,:Subject,:discription)');
                        $sql->execute(array(
                            'image'        => $image,
                            'file'         => $file,
                            'Subject'      => $Subject,
                            'discription'  => $discription
                        ));
                    }
                ?>
            </div>
            <?php
            }elseif($do=='edid'){
                $catalogID = (isset($_GET['id']))?$_GET['id']:0;

                $sql=$con->prepare('SELECT * FROM  tbllibrary WHERE imageID= ?');
                $sql->execute(array($catalogID));
                $info=$sql->fetch();

            ?>
            <div class="addnewdiscription">
                <h2>EDID Catalog</h2>
                <form method="post" enctype="multipart/form-data">
                    <label for="subject">Subject <span>*</span></label>
                    <input type="text" id="subject" name="subject" required value="<?php echo $info['Subject']?>">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"><?php echo $info['discription']?></textarea>
                    <label for="profileimage">Profile Image</label>
                    <input type="file" id="profileimage" name="profileimage" accept="image/*">
                    <label for="filepath">File Path</label>
                    <input type="file" id="filepath" name="filepath">
                    <div class="btnsubmit">
                    <button type="submit" name="btnEditservice">Edit</button>
                    </div>
                </form>
                <?php
                    if(isset($_POST['btnEditservice'])){
                        if(!empty($_FILES['profileimage']['name'])){
                            $filename='../images/libary/'.$info['image'];
                            unlink($filename);
                            $temp=explode(".",$_FILES['profileimage']['name']);
                            $newfilename=round(microtime(true)).'.'.end($temp);
                            move_uploaded_file($_FILES['profileimage']['tmp_name'],'../images/libary/'.$newfilename);
                        }else{
                            $newfilename = $info['image'];
                        }
                        if(!empty($_FILES['filepath']['name'])){
                            $filename1='../images/libary/'.$info['file'];
                            unlink($filename1);
                            $temp1=explode(".",$_FILES['filepath']['name']);
                            $newfilename1=round(microtime(true)).'.'.end($temp1);
                            move_uploaded_file($_FILES['filepath']['tmp_name'],'../images/libary/'.$newfilename1);
                        }else{
                            $newfilename1 = $info['file'];
                        }

                        $image       = $newfilename;
                        $file        = $newfilename1;
                        $Subject     = $_POST['subject'];
                        $discription = $_POST['description'];

                        $sql=$con->prepare('UPDATE tbllibrary 
                                            SET     image        = :image,
                                                    file         = :file,
                                                    Subject      = :Subject,
                                                    discription  = :discription
                                            WHERE   imageID      = :imageID ');
                        $sql->execute(array(
                            'image'        => $image,
                            'file'         => $file,
                            'Subject'      => $Subject,
                            'discription'  => $discription,
                            'imageID'      => $catalogID
                        ));

                        echo '<script> location.href="ServiceCatalog.php" </script>';
                    }
                ?>
            </div>
            <?php
            }elseif($do=='delete'){
                $catalogID = (isset($_GET['id']))?$_GET['id']:0;

                $sql=$con->prepare('SELECT * FROM  tbllibrary WHERE imageID= ?');
                $sql->execute(array($catalogID));
                $info=$sql->fetch();

                $filename='../images/libary/'.$info['image'];
                    unlink($filename);
                $filename1='../images/libary/'.$info['file'];
                    unlink($filename1);
                
                $sql=$con->prepare('DELETE FROM tbllibrary WHERE imageID=?');
                $sql->execute(array($catalogID));

                echo '<script> location.href="ServiceCatalog.php" </script>';

            }else{
                echo '<script> location.href="index.php" </script>';
            }
            ?>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/ServiceCatalog.js"></script>
    <script src="js/sidebar.js"></script>
</body>