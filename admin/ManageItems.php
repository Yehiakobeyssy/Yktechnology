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

    $catID = (isset($_GET['cat']))?$_GET['cat']:0;
    $checkcatID= checkItem('Cat_ID','tblcategory',$catID);
    if($checkcatID == 0){
        header('location:ManageService.php');
    }else{
        $sql=$con->prepare('SELECT Category_Name FROM tblcategory WHERE Cat_ID=?');
        $sql->execute(array($catID));
        $result_cat= $sql->fetch();
        $catecory_Name= $result_cat['Category_Name'];
    }
?>
    <link rel="stylesheet" href="css/ManageItems.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Manage Services for <?php echo  $catecory_Name ?></h1>
                <a href="ManageItems.php?cat=<?php echo $catID ?>&do=add" class="btn btn-success">New Service</a>
            </div>
            <div class="reusult_services">
                <?php
                    $do=(isset($_GET['do']))?$_GET['do']:'manage';
                    if($do == 'manage'){?>
                        <div class="manage_services">
                            <?php
                                $sql=$con->prepare('SELECT ServiceID,Service_Name,old_Price,Service_Price,Service_show,DurationName 
                                                    FROM tblservices
                                                    INNER JOIN  tblduration ON tblservices.Duration = tblduration.DurationID
                                                    WHERE Active =1 AND CategoryID=?');
                                $sql->execute(array($catID));
                                $cards= $sql->fetchAll();
                                foreach($cards as $card){
                                    if($card['Service_show'] == 1){
                                        $textshow = 'Hide';
                                    }else{
                                        $textshow = 'Show';
                                    }
                                    echo '
                                    <div class="card_service">
                                        <div class="card_header">
                                            <h2>'.$card['Service_Name'].'</h2>
                                            <div class="price_service">
                                                <table>
                                                    <tr>
                                                        <td><label for="">Old Price</label></td>
                                                        <td><label for="">New Price</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td>'.number_format($card['old_Price'],2,'.','').'</td>
                                                        <td>'.number_format($card['Service_Price'],2,'.','').'</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <p>'.$card['DurationName'].'</p>
                                        </div>
                                        <div class="add_speafications">
                                            <input type="text" name="" id="txt'.$card['ServiceID'].'">
                                            <button class="btnaddspe" data-index="'.$card['ServiceID'].'"><i class="fa-solid fa-check"></i></button>
                                        </div>
                                        <div class="allspeafication">
                                            <ul>';
                                                $stat=$con->prepare('SELECT SpeaficationsID,Speafications FROM  tblspeafications
                                                                    WHERE ServiceID=?');
                                                $stat->execute(array($card['ServiceID']));
                                                $speaficatios = $stat->fetchAll();
                                                foreach($speaficatios as $spea){
                                                    echo '
                                                        <li><span>'.$spea['Speafications'].'</span> <button class="btndeletespe" data-index="'.$spea['SpeaficationsID'].'"><i class="fa-solid fa-trash-can"></i></button> </li>
                                                    ';
                                                }
                                            echo '</ul>
                                        </div>
                                        <div class="control_servis">
                                            <a href="ManageItems.php?cat='.$catID.'&do=show&id='.$card['ServiceID'].'" class="btn btn-success">'.$textshow.'</a>
                                            <a href="ManageItems.php?cat='.$catID.'&do=edid&id='.$card['ServiceID'].'" class="btn btn-warning">edit</a>
                                            <a href="ManageItems.php?cat='.$catID.'&do=delete&id='.$card['ServiceID'].'" class="btn btn-danger">Delete</a>
                                        </div>
                                    </div>
                                    ';
                                }
                            ?>
                        </div>
                        <div class="runajax"></div>
                    <?php
                    }elseif($do == 'add'){?>
                    <div class="addform">
                            <form action="" method="post">
                                <div class="addtitle">
                                    <h3>New Service</h3>
                                </div>
                                <div class="container_add">
                                        <table>
                                            <tr>
                                                <td><label for="">Service Name</label></td>
                                                <td><input type="text" name="Service_Name" id="" required></td>
                                            </tr>
                                            <tr>
                                                <td><label for="">Duration</label></td>
                                                <td>
                                                    <select name="Duration" id="" required>
                                                        <option value="">[Select One]</option>
                                                        <?php
                                                            $sql=$con->prepare('SELECT DurationID,DurationName FROM tblduration');
                                                            $sql->execute();
                                                            $rows =$sql->fetchAll();
                                                            foreach($rows as $row){
                                                                echo '<option value="'.$row['DurationID'].'">'.$row['DurationName'].'</option>';
                                                            }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label for="">Old Price</label></td>
                                                <td><input type="number" name="old_Price" id="" step="0.01"></td>
                                            </tr>
                                            <tr>
                                                <td><label for="">Service Price</label></td>
                                                <td><input type="number" name="Service_Price" id="" step="0.01" required></td>
                                            </tr>
                                            <tr>
                                                <td><label for="">Form use</label></td>
                                                <td>
                                                    <select name="Form_use" id="" >
                                                        <option value="">[Select One]</option>
                                                        <?php
                                                            $sql=$con->prepare('SELECT formID,formName FROM tblforms');
                                                            $sql->execute();
                                                            $rows =$sql->fetchAll();
                                                            foreach($rows as $row){
                                                                echo '<option value="'.$row['formID'].'">'.$row['formName'].'</option>';
                                                            }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label for="">With Commission</label></td>
                                                <td>
                                                    <select name="Service_Commission" id="" >
                                                        <option value="0">No</option>
                                                        <option value="1">Yes</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label for="">Service Show</label></td>
                                                <td>
                                                    <select name="Service_show" id="" >
                                                        <option value="1">Yes</option>
                                                        <option value="0">no</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                </div>
                                <div class="btncontrol">
                                    <button type="submit" class="btn btn-success" name="btnsave" >add</button>
                                </div>
                            </form>
                            <?php
                                if(isset($_POST['btnsave'])){
                                    $CategoryID     = $catID;
                                    $Service_Name   = $_POST['Service_Name']; 
                                    $Duration       = $_POST['Duration'];
                                    $Service_Price  = $_POST['Service_Price'];
                                    $old_Price      = $_POST['old_Price'];
                                    $Form_use       = $_POST['Form_use'];
                                    $Get_commission = $_POST['Service_Commission'];
                                    $Service_show   = $_POST['Service_show']; 
                                    $Active         = 1;

                                    $sql=$con->prepare('INSERT INTO tblservices (CategoryID,Service_Name,Duration,Service_Price,old_Price,Form_use,Get_commission,Service_show,Active)
                                                        VALUES (:CategoryID,:Service_Name,:Duration,:Service_Price,:old_Price,:Form_use,:Get_commission,:Service_show,:Active)');
                                    $sql->execute(array(
                                        'CategoryID'    => $CategoryID,
                                        'Service_Name'  => $Service_Name,
                                        'Duration'      => $Duration,
                                        'Service_Price' => $Service_Price,
                                        'old_Price'     => $old_Price,
                                        'Form_use'      => $Form_use,
                                        'Get_commission'=> $Get_commission,
                                        'Service_show'  => $Service_show,
                                        'Active'        => $Active
                                    ));
                                }
                            ?>
                        </div>
                    <?php
                    }elseif($do=='edid'){
                        $serviceID = (isset($_GET['id']))?$_GET['id']:0;
                        $checkServiceID =checkItem('ServiceID','tblservices',$serviceID); 
                        if($checkServiceID > 0){
                            $sql=$con->prepare('SELECT * FROM tblservices WHERE ServiceID =?');
                            $sql->execute(array($serviceID));
                            $service_info = $sql->fetch();?>
                            <div class="addform">
                                <form action="" method="post">
                                    <div class="addtitle">
                                        <h3>Edit Service</h3>
                                    </div>
                                    <div class="container_add">
                                            <table>
                                                <tr>
                                                    <td><label for="">Service Name</label></td>
                                                    <td><input type="text" name="Service_Name" id="" required value="<?php echo $service_info['Service_Name']?>"></td>
                                                </tr>
                                                <tr>
                                                    <td><label for="">Duration</label></td>
                                                    <td>
                                                        <select name="Duration" id="" required>
                                                            <option value="">[Select One]</option>
                                                            <?php
                                                                $sql=$con->prepare('SELECT DurationID,DurationName FROM tblduration');
                                                                $sql->execute();
                                                                $rows =$sql->fetchAll();
                                                                foreach($rows as $row){
                                                                    if($service_info['Duration'] == $row['DurationID']){
                                                                        echo '<option value="'.$row['DurationID'].'" selected>'.$row['DurationName'].'</option>';
                                                                    }else{
                                                                        echo '<option value="'.$row['DurationID'].'">'.$row['DurationName'].'</option>';
                                                                    }
                                                                
                                                                }
                                                            ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><label for="">Old Price</label></td>
                                                    <td><input type="number" name="old_Price" id="" step="0.01" value="<?php echo $service_info['old_Price']?>"></td>
                                                </tr>
                                                <tr>
                                                    <td><label for="">Service Price</label></td>
                                                    <td><input type="number" name="Service_Price" id="" step="0.01" required value="<?php echo $service_info['Service_Price']?>"></td>
                                                </tr>
                                                <tr>
                                                    <td><label for="">Form use</label></td>
                                                    <td>
                                                        <select name="Form_use" id="" >
                                                            <option value="">[Select One]</option>
                                                            <?php
                                                                $sql=$con->prepare('SELECT formID,formName FROM tblforms');
                                                                $sql->execute();
                                                                $rows =$sql->fetchAll();
                                                                foreach($rows as $row){
                                                                    if($service_info['Form_use'] == $row['formID']){
                                                                        echo '<option value="'.$row['formID'].'" selected>'.$row['formName'].'</option>';
                                                                    }else{
                                                                        echo '<option value="'.$row['formID'].'">'.$row['formName'].'</option>';
                                                                    }
                                                                    
                                                                }
                                                            ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><label for="">With Commission</label></td>
                                                    <td>
                                                        <select name="Service_Commission" id="" >
                                                            <?php
                                                                if($service_info['Get_commission'] ==1){
                                                                    echo '<option value="1" selected>Yes</option>';
                                                                    echo '<option value="0">no</option>';
                                                                }else{
                                                                    echo '<option value="0" selected>no</option>';
                                                                    echo '<option value="1">Yes</option>';
                                                                }
                                                            ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><label for="">Service Show</label></td>
                                                    <td>
                                                        <select name="Service_show" id="" >
                                                            <?php
                                                                if($service_info['Service_show'] ==1){
                                                                    echo '<option value="1" selected>Yes</option>';
                                                                    echo '<option value="0">no</option>';
                                                                }else{
                                                                    echo '<option value="0" selected>no</option>';
                                                                    echo '<option value="1">Yes</option>';
                                                                }
                                                            ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                            </table>
                                    </div>
                                    <div class="btncontrol">
                                        <button type="submit" class="btn btn-warning" name="btnedid" >Edid</button>
                                    </div>
                                </form>
                                <?php
                                    if(isset($_POST['btnedid'])){
                                        $CategoryID     = $catID;
                                        $Service_Name   = $_POST['Service_Name']; 
                                        $Duration       = $_POST['Duration'];
                                        $Service_Price  = $_POST['Service_Price'];
                                        $old_Price      = $_POST['old_Price'];
                                        $Form_use       = $_POST['Form_use'];
                                        $Get_commission = $_POST['Service_Commission'];
                                        $Service_show   = $_POST['Service_show']; 
                                        $Active         = 1;

                                        $sql=$con->prepare('UPDATE  tblservices 
                                                            SET CategoryID      = :CategoryID,
                                                                Service_Name    = :Service_Name,
                                                                Duration        = :Duration,
                                                                Service_Price   = :Service_Price,
                                                                old_Price       = :old_Price,
                                                                Form_use        = :Form_use,
                                                                Get_commission  = :Get_commission,
                                                                Service_show    = :Service_show,
                                                                Active          =  :Active
                                                            WHERE ServiceID     = :ServiceID');
                                        $sql->execute(array(
                                            'CategoryID'    => $CategoryID,
                                            'Service_Name'  => $Service_Name,
                                            'Duration'      => $Duration,
                                            'Service_Price' => $Service_Price,
                                            'old_Price'     => $old_Price,
                                            'Form_use'      => $Form_use,
                                            'Get_commission'=> $Get_commission,
                                            'Service_show'  => $Service_show,
                                            'Active'        => $Active,
                                            'ServiceID'     => $serviceID
                                        ));
                                        echo '<script> location.href="ManageItems.php?cat='.$catID.'"</script>';
                                    }
                                ?>
                            </div>

                        <?php
                        }else{
                            echo '<script> location.href="ManageItems.php?cat='.$catID.'"</script>';
                        }
                    }elseif($do=='show'){
                        $serviceID = (isset($_GET['id']))?$_GET['id']:0;
                        $checkServiceID =checkItem('ServiceID','tblservices',$serviceID); 
                        if($checkServiceID > 0){
                            $sql=$con->prepare('SELECT Service_show FROM tblservices WHERE ServiceID =?');
                            $sql->execute(array($serviceID));
                            $result=$sql->fetch();
                            if($result['Service_show'] == 1){
                                $stat=$con->prepare('UPDATE tblservices SET Service_show = 0 WHERE ServiceID = ?');
                                $stat->execute(array($serviceID));
                            }else{
                                $stat=$con->prepare('UPDATE tblservices SET Service_show = 1 WHERE ServiceID = ?');
                                $stat->execute(array($serviceID));
                            }
                            echo '<script> location.href="ManageItems.php?cat='.$catID.'"</script>';
                        }else{
                            echo '<script> location.href="ManageItems.php?cat='.$catID.'"</script>';
                        }
                    }elseif($do=='delete'){
                        $serviceID = (isset($_GET['id']))?$_GET['id']:0;
                        $checkServiceID =checkItem('ServiceID','tblservices',$serviceID); 
                        if($checkServiceID > 0){
                            $sql=$con->prepare('UPDATE tblservices SET Active = 0 WHERE ServiceID = ?');
                            $sql->execute(array($serviceID));
                            echo '<script> location.href="ManageItems.php?cat='.$catID.'"</script>';
                        }else{
                            echo '<script> location.href="ManageItems.php?cat='.$catID.'"</script>';
                        }
                    }else{
                        header('location:index.php');
                    }
                ?>
            </div>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/ManageItems.js"></script>
    <script src="js/sidebar.js"></script>
</body>