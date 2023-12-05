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
    <link rel="stylesheet" href="css/ManageServiceclient.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Client Services</h1>
            </div>
            <?php
                $do= (isset($_GET['do']))?$_GET['do']:'manage';
                if($do=='manage'){?>
                <div class="mangebox">
                    <div class="search-container">
                        <input type="text" class="search-input" placeholder="Search ..." id="txtsearch">
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Client Name</th>
                                    <th>Service</th>
                                    <th>Price</th>
                                    <th>Date Begin</th>
                                    <th>Date End</th>
                                    <th>Status</th>
                                    <th>Done</th>
                                    <th>Control</th>
                                </tr>
                            </thead>
                            <tbody class="bodyticket">
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
                }elseif($do=='Deiteil'){
                    $serviseID = (isset($_GET['id']))?$_GET['id']:0;
                    $sql = $con->prepare('SELECT tblclientservices.*, Service_Name
                                            FROM tblclientservices
                                            INNER JOIN tblservices ON tblservices.ServiceID = tblclientservices.ServiceID
                                            WHERE tblclientservices.ServicesID = ?');
                    $sql->execute(array($serviseID));
                    $serviceInfo = $sql->fetch();
                ?>
                <div class="container_detail">
                    <div class="general_info">
                        <h4>General Info</h4>
                        <table>
                            <tr>
                                <td><label for="">Service</label></td>
                                <td><span><?php echo $serviceInfo['Service_Name'] ?></span></td>
                            </tr>
                            <tr>
                                <td><label for="">Title</label></td>
                                <td><span><?php echo $serviceInfo['ServiceTitle'] ?></span></td>
                            </tr>
                            <tr>
                                <td><label for="">Date service</label></td>
                                <td><span>from <span><?php echo $serviceInfo['Date_service'] ?></span> till <span><?php echo $serviceInfo['Dateend'] ?></span></span></td>
                            </tr>
                            <tr>
                                <td><label for="">Price</label></td>
                                <td><span><?php echo number_format($serviceInfo['Price'],2,'.','')  ?>$</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="domain_info">
                        <h4>Domain Info</h4>
                        <table>
                            <tr>
                                <td><label for="">Domain name</label></td>
                                <td><span><?php echo $serviceInfo['ServiceDomain'] ?></span></td>
                            </tr>
                            <tr>
                                <td><label for="">Transfer</label></td>
                                <td><span><?php echo ($serviceInfo['ServiceTransfer']==0)?'no':'yes' ?></span></td>
                            </tr>
                            <tr>
                                <td><label for="">Code transfer</label></td>
                                <td><span><?php echo $serviceInfo['CodeTransfer'] ?></span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="more_detail">
                        <h4>More Information</h4>
                        <table>
                            <tr>
                                <td><label for="">Service for</label></td>
                                <td><span><?php echo $serviceInfo['forwhat'] ?></span></td>
                            </tr>
                            <tr>
                                <td><label for="">Color use</label></td>
                                <td><span><?php echo $serviceInfo['Colors'] ?></span></td>
                            </tr>
                        </table>
                        <h4>Description</h4>
                        <p><?php echo  nl2br($serviceInfo['Discription']) ?></p>
                    </div>
                    <div class="attachment">
                        <a href="../Documents/<?php echo $serviceInfo['filename'] ?>" download=""><i class="fa-solid fa-paperclip"></i> <?php echo $serviceInfo['filename'] ?></a>
                    </div>
                    <div class="cotrollbtn">
                        <a href="ManageServiceclient.php?do=Edit&id=<?php echo $serviseID?>" class="ctlyehia" style="color:orange"><i class="fa-solid fa-pen"></i> <span>Edit<span></a>
                        <a href="ManageServiceclient.php?do=done&id=<?php echo $serviseID?>" class="ctlyehia" style="color:green"><i class="fa-solid fa-check-double"></i> <span>Finish<span></a>
                        <a href="ManageServiceclient.php?do=Reactive&id=<?php echo $serviseID?>" class="ctlyehia" style="color:#1c4e80"><i class="fa-solid fa-check-double"></i> <span>active<span></a>
                        <a href="ManageServiceclient.php?do=cancelService&id=<?php echo $serviseID ?>" class="ctlyehia" style="color:red"><i class="fa-solid fa-eraser"></i></i><span> canceld <span></a>
                    </div>
                </div>
                <?php
                }elseif($do=='Edit'){
                    $serviseID = (isset($_GET['id']))?$_GET['id']:0;
                    $sql = $con->prepare('SELECT tblclientservices.*, Service_Name
                                            FROM tblclientservices
                                            INNER JOIN tblservices ON tblservices.ServiceID = tblclientservices.ServiceID
                                            WHERE tblclientservices.ServicesID = ?');
                    $sql->execute(array($serviseID));
                    $serviceInfo = $sql->fetch();
                ?>
                <form action="" method="post" enctype="multipart/form-data">
                <div class="container_detail">
                    <div class="general_info">
                        <h4>General Info</h4>
                        <table>
                            <tr>
                                <td><label for="">Service</label></td>
                                <td><span><?php echo $serviceInfo['Service_Name'] ?></span></td>
                            </tr>
                            <tr>
                                <td><label for="">Title</label></td>
                                <td><span><input type="text" name="ServiceTitle" id="" value="<?php echo $serviceInfo['ServiceTitle'] ?>"></span></td>
                            </tr>
                            <tr>
                                <td><label for="">Date service</label></td>
                                <td><span>from <span> <input type="date" name="Date_service" id="" value="<?php echo $serviceInfo['Date_service'] ?>"></span> till <span> <input type="date" name="Dateend" id="" value="<?php echo $serviceInfo['Dateend'] ?>"></span></span></td>
                            </tr>
                            <tr>
                                <td><label for="">Price</label></td>
                                <td><span><?php echo number_format($serviceInfo['Price'],2,'.','')  ?>$</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="domain_info">
                        <h4>Domain Info</h4>
                        <table>
                            <tr>
                                <td><label for="">Domain name</label></td>
                                <td><span> <input type="text" name="ServiceDomain" id="" value="<?php echo $serviceInfo['ServiceDomain'] ?>"></span></td>
                            </tr>
                            <tr>
                                <td><label for="">Transfer</label></td>
                                <td>
                                    <span>
                                        <select name="ServiceTransfer" id="">
                                            <?php
                                                if($serviceInfo['ServiceTransfer']==0){
                                                    echo '<option value="0">No</option>';
                                                }else{
                                                    echo '<option value="1">yes</option>';
                                                }
                                            ?>
                                        </select>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="">Code transfer</label></td>
                                <td><span><input type="text" name="CodeTransfer" id="" value="<?php echo $serviceInfo['CodeTransfer'] ?>"></span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="more_detail">
                        <h4>More Information</h4>
                        <table>
                            <tr>
                                <td><label for="">Service for</label></td>
                                <td><span><input type="text" name="forwhat" id="" value="<?php echo $serviceInfo['forwhat'] ?>"></span></td>
                            </tr>
                            <tr>
                                <td><label for="">Color use</label></td>
                                <td><span><input type="text" name="Colors" id="" value="<?php echo $serviceInfo['Colors'] ?>"></span></td>
                            </tr>
                        </table>
                        <h4>Description</h4>
                        <textarea name="Discription" id=""  rows="10"><?php echo $serviceInfo['Discription'] ?></textarea>
                    </div>
                    <div class="attachment">
                        <label for="">attachment</label>
                        <input type="file" name="filename" id="">
                    </div>
                    <div class="cotrollbtn">
                        <button type="submit" class="btn btn-warning" name="btnedit">Edit </button>
                    </div>
                </div>
                    <?php
                        if(isset($_POST['btnedit'])){
                            if(!empty($_FILES['filename']['name'])){
                                $filename='../Documents/'.$serviceInfo['filename'];
                                unlink($filename);
                                $temp=explode(".",$_FILES['filename']['name']);
                                $newfilename=round(microtime(true)).'.'.end($temp);
                                move_uploaded_file($_FILES['filename']['tmp_name'],'../Documents/'.$newfilename);
                            }else{
                                $newfilename = $serviceInfo['filename'];
                            }

                            $ServiceTitle        = $_POST['ServiceTitle'];
                            $Date_service        = $_POST['Date_service'];
                            $Dateend             = $_POST['Dateend'];
                            $ServiceDomain       = $_POST['ServiceDomain'];
                            $ServiceTransfer     = $_POST['ServiceTransfer'];
                            $CodeTransfer        = $_POST['CodeTransfer'];
                            $forwhat             = $_POST['forwhat'];
                            $Colors              = $_POST['Colors'];
                            $Discription         = $_POST['Discription'];
                            $filename            = $newfilename;
                            $serviceID           = $serviseID;

                            $sql=$con->prepare('UPDATE  tblclientservices 
                                                SET     ServiceTitle    = :ServiceTitle,
                                                        Date_service    = :Date_service,
                                                        Dateend         = :Dateend,
                                                        ServiceDomain   = :ServiceDomain,
                                                        ServiceTransfer = :ServiceTransfer,
                                                        CodeTransfer    = :CodeTransfer,
                                                        forwhat         = :forwhat ,
                                                        Colors          = :Colors,
                                                        Discription     = :Discription,
                                                        filename        = :filename
                                                WHERE   ServicesID      = :ServicesID');
                            $sql->execute(array(
                                'ServiceTitle'        => $ServiceTitle , 
                                'Date_service'        => $Date_service ,
                                'Dateend'             => $Dateend ,
                                'ServiceDomain'       => $ServiceDomain,
                                'ServiceTransfer'     => $ServiceTransfer ,
                                'CodeTransfer'        => $CodeTransfer,
                                'forwhat'             => $forwhat ,
                                'Colors'              => $Colors,
                                'Discription'         => $Discription ,
                                'filename'            => $filename ,
                                'ServicesID'          => $serviceID 
                            ));
                            echo '<script> location.href="ManageServiceclient.php" </script>'; 
                        }
                    ?>
                </form>
                <?php
                }elseif($do=='done'){
                    $serviseID = (isset($_GET['id']))?$_GET['id']:0;

                    $sql=$con->prepare('UPDATE  tblclientservices SET ServiceDone =  1 WHERE ServicesID=?');
                    $sql->execute(array($serviseID));
                    echo '<script> location.href="ManageServiceclient.php" </script>'; 
                }elseif($do=='Reactive'){
                    $serviseID = (isset($_GET['id']))?$_GET['id']:0;

                    $sql=$con->prepare('UPDATE  tblclientservices SET serviceStatus =  1 WHERE ServicesID=?');
                    $sql->execute(array($serviseID));
                    echo '<script> location.href="ManageServiceclient.php" </script>'; 
                    
                }elseif($do=='cancelService'){
                    $serviseID = (isset($_GET['id']))?$_GET['id']:0;

                    $sql=$con->prepare('UPDATE  tblclientservices SET serviceStatus =  4 WHERE ServicesID=?');
                    $sql->execute(array($serviseID));
                    echo '<script> location.href="ManageServiceclient.php" </script>';

                }else{
                    echo '<script> location.href="index.php" </script>';
                }
            ?>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/ManageServiceclient.js"></script>
    <script src="js/sidebar.js"></script>
</body>