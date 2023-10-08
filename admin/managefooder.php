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

    $sql=$con->prepare('SELECT show_profolio,show_ourteam,textaboutus,link_facebook,link_github,link_linkin FROM tblsetting WHERE SettingID = 1');
    $sql->execute();
    $result=$sql->fetch();
?>
    <link rel="stylesheet" href="css/managefooder.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Edit Footer</h1>
            </div>
            <div class="frmeditfooter">
                <form action="" method="post">
                    <div class="showsection">
                        <h4>Show Section</h4>
                        <table>
                            <tr>
                                <td><label for="">Show Protfolio</label></td>
                                <td>
                                    <select name="showpro" id="">
                                        <?php
                                            if($result['show_profolio'] == 1){
                                                echo '
                                                    <option value="1" selected>yes</option>
                                                    <option value="0">No</option>
                                                ';
                                            }else{
                                                echo '
                                                    <option value="1">yes</option>
                                                    <option value="0" selected>No</option>
                                                ';
                                            }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="">Show Our Team </label></td>
                                <td>
                                    <select name="showour" id="">
                                        <?php
                                            if($result['show_ourteam'] == 1){
                                                echo '
                                                    <option value="1" selected>yes</option>
                                                    <option value="0">No</option>
                                                ';
                                            }else{
                                                echo '
                                                    <option value="1">yes</option>
                                                    <option value="0" selected>No</option>
                                                ';
                                            }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="aboutus">
                        <h4>About us</h4>
                        <textarea name="aboutus" id=""  rows="5"><?php echo $result['textaboutus'] ?></textarea>
                    </div>
                    <div class="socialmedia">
                        <table>
                            <tr>
                                <td><label for="">Facebook</label></td>
                                <td><input type="text" name="face" id="" value="<?php echo $result['link_facebook'] ?>"></td>
                            </tr>
                            <tr>
                                <td><label for="">GitHub</label></td>
                                <td><input type="text" name="git" id="" value="<?php echo $result['link_github'] ?>"></td>
                            </tr>
                            <tr>
                                <td><label for="">LinkdIn</label></td>
                                <td><input type="text" name="linkt" id="" value="<?php echo $result['link_linkin'] ?>"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="btncontrol">
                        <button type="submit" name="btnedit">Edit</button>
                    </div>
                </form>
                <?php
                    if(isset($_POST['btnedit'])){
                        $showPro        = $_POST['showpro'];
                        $showOurworker  = $_POST['showour'];
                        $aboutus        = $_POST['aboutus'];
                        $face           = $_POST['face'];
                        $git            = $_POST['git'];
                        $linkin         = $_POST['linkt'];

                        $sql=$con->prepare('UPDATE  tblsetting 
                                            SET show_profolio   =? ,
                                                show_ourteam    =? ,
                                                textaboutus     =? ,
                                                link_facebook   =? ,
                                                link_github     =? , 
                                                link_linkin     =?  
                                            WHERE SettingID =1');
                        $sql->execute(array($showPro,$showOurworker,$aboutus,$face,$git,$linkin));
                        echo '<script> location.href="ManageSetting.php" </script>';
                    }
                ?>
                
            </div>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/sidebar.js"></script>
</body>