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
    <link rel="stylesheet" href="css/ManageDomein.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Mange Domeins</h1>
                <a href="ManageDomein.php?do=add" class="btn btn-success" id="btnNew"> + Add new Domein service</a>
            </div>
            <?php
                $do = (isset($_GET['do']))?$_GET['do']:'manage';
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
                                    <th>Plan (Domein)</th>
                                    <th>Date</th>
                                    <th>Renewal Date</th>
                                    <th>Renewal Price</th>
                                    <th>Note</th>
                                    <th>Status</th>
                                    <th>Control</th>
                                </tr>
                            </thead>
                            <tbody class="bodyticket">
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
                }elseif($do=='add'){
                    ?>
                    <div class="addform">
                        <h1>New  Domein Service</h1>
                        <form action="" method="post">
                            <label for="clientName">Client Name</label>
                            <select id="clientName" name="clientName" required>
                                <option value="">[Select One]</option>
                                <?php
                                    $sql=$con->prepare('SELECT ClientID,Client_FName,Client_LName,Client_email 
                                                        FROM tblclients 
                                                        WHERE client_active=1
                                                        ORDER BY Client_FName');
                                    $sql->execute();
                                    $clients=$sql->fetchAll();
                                    foreach($clients as $client){
                                        echo '<option value="'.$client['ClientID'].'">'.$client['Client_FName'].' '.$client['Client_LName'].' ('.$client['Client_email'].')</option>';
                                    }

                                ?>
                            </select>
                            <label for="service">Service</label>
                            <select id="service" name="service" required>
                                <option value="">[Select One]</option>
                                <?php
                                    $sql=$con->prepare('SELECT DomainTypeID,ServiceName FROM  tbldomaintype');
                                    $sql->execute();
                                    $types=$sql->fetchAll();
                                    foreach($types as $type){
                                        echo '<option value="'.$type['DomainTypeID'].'">'.$type['ServiceName'].'</option>';
                                    }
                                ?>
                            </select>

                            <label for="domain">Domein</label>
                            <input type="text" id="domain" name="domain">

                            <label for="dateBegin">Date Begin</label>
                            <input type="date" id="dateBegin" name="dateBegin">

                            <label for="dateRenew">Date Renew</label>
                            <input type="date" id="dateRenew" name="dateRenew">

                            <label for="price">Price</label>
                            <input type="number" id="price" name="price" step="0.01">

                            <label for="note">Note</label>
                            <textarea id="note" name="note" rows="4"></textarea>

                            <label for="relatedTo">Related To</label>
                            <select id="relatedTo" name="relatedTo">
                            </select>
                            <div class="btncontrol">
                                <button type="submit" name="btnAdd">Save</button>
                            </div>
                        </form>
                        <?php
                            if(isset($_POST['btnAdd'])){
                                $DateBegin      =$_POST['dateBegin'];
                                $Client         =$_POST['clientName'];
                                $ServiceType    =$_POST['service'];
                                $DomeinName     =$_POST['domain'];
                                $RenewDate      =$_POST['dateRenew'];
                                $Price_Renew    =$_POST['price'];
                                $Note           =$_POST['note'];
                                $Status         =1;
                                $ServiceID      =$_POST['relatedTo'];

                                $sql=$con->prepare('INSERT INTO tbldomeinclients (DateBegin,Client,ServiceType,DomeinName,RenewDate,Price_Renew,Note,Status,ServiceID)
                                                    VALUES (:DateBegin,:Client,:ServiceType,:DomeinName,:RenewDate,:Price_Renew,:Note,:Status,:ServiceID)');
                                $sql->execute(array(
                                    'DateBegin'     =>$DateBegin,
                                    'Client'        =>$Client ,
                                    'ServiceType'   =>$ServiceType,
                                    'DomeinName'    =>$DomeinName,
                                    'RenewDate'     =>$RenewDate,
                                    'Price_Renew'   =>$Price_Renew,
                                    'Note'          =>$Note,
                                    'Status'        =>$Status,
                                    'ServiceID'     =>$ServiceID
                                ));
                                echo '<script> location.href="ManageDomein.php" </script>';
                            }
                        ?>
                    </div>
                <?php
                }elseif($do=='edid'){
                    $domeinID=(isset($_GET['id']))?$_GET['id']:0;
                    $sql=$con->prepare('SELECT * FROM tbldomeinclients WHERE DomeinID=?');
                    $sql->execute(array($domeinID));
                    $domeininfo=$sql->fetch();
                ?>
                    <div class="addform">
                        <h1>Edid  Domein Service</h1>
                        <form action="" method="post">
                            <label for="clientName">Client Name</label>
                            <select id="clientName" name="clientName" required>
                                <option value="">[Select One]</option>
                                <?php
                                    $sql=$con->prepare('SELECT ClientID,Client_FName,Client_LName,Client_email 
                                                        FROM tblclients 
                                                        WHERE client_active=1
                                                        ORDER BY Client_FName');
                                    $sql->execute();
                                    $clients=$sql->fetchAll();
                                    foreach($clients as $client){
                                        if($domeininfo['Client'] == $client['ClientID']){
                                            echo '<option value="'.$client['ClientID'].'" selected>'.$client['Client_FName'].' '.$client['Client_LName'].' ('.$client['Client_email'].')</option>';
                                        }else{
                                            echo '<option value="'.$client['ClientID'].'">'.$client['Client_FName'].' '.$client['Client_LName'].' ('.$client['Client_email'].')</option>';
                                        }
                                        
                                    }
                                ?>
                            </select>
                            <label for="service">Service</label>
                            <select id="service" name="service" required>
                                <option value="">[Select One]</option>
                                <?php
                                    $sql=$con->prepare('SELECT DomainTypeID,ServiceName FROM  tbldomaintype');
                                    $sql->execute();
                                    $types=$sql->fetchAll();
                                    foreach($types as $type){
                                        if($domeininfo['ServiceType'] == $type['DomainTypeID']){
                                            echo '<option value="'.$type['DomainTypeID'].'" selected>'.$type['ServiceName'].'</option>';
                                        }else{
                                            echo '<option value="'.$type['DomainTypeID'].'">'.$type['ServiceName'].'</option>';
                                        }
                                    }
                                ?>
                            </select>

                            <label for="domain">Domein</label>
                            <input type="text" id="domain" name="domain" value="<?php echo $domeininfo['DomeinName'] ?>">

                            <label for="dateBegin">Date Begin</label>
                            <input type="date" id="dateBegin" name="dateBegin" value="<?php echo $domeininfo['DateBegin'] ?>">

                            <label for="dateRenew">Date Renew</label>
                            <input type="date" id="dateRenew" name="dateRenew" value="<?php echo $domeininfo['RenewDate'] ?>">

                            <label for="price">Price</label>
                            <input type="number" id="price" name="price" step="0.01" value="<?php echo $domeininfo['Price_Renew'] ?>">

                            <label for="note">Note</label>
                            <textarea id="note" name="note" rows="4"><?php echo $domeininfo['Note'] ?></textarea>

                            <label for="relatedTo">Related To</label>
                            <select id="relatedToedid" name="relatedTo" >
                                <?php
                                    $sql=$con->prepare('SELECT ServicesID,ServiceTitle,Service_Name 
                                        FROM tblclientservices 
                                        INNER JOIN  tblservices ON tblservices.ServiceID = tblclientservices.ServiceID
                                        WHERE serviceStatus < 4 AND ClientID =?');
                                    $sql->execute(array($domeininfo['Client']));
                                    $services = $sql->fetchAll();
                                    echo '<option value="">NOT Releted</option>';
                                    foreach ($services as $ser){
                                        if($domeininfo['ServiceID'] == $ser['ServicesID']){
                                            echo '<option value="'.$ser['ServicesID'].'" selected>'.$ser['Service_Name'].' ( '.$ser['ServiceTitle'] .' )</option>';
                                        }else{
                                            echo '<option value="'.$ser['ServicesID'].'">'.$ser['Service_Name'].' ( '.$ser['ServiceTitle'] .' )</option>';
                                        }
                                        
                                    }
                                ?>
                            </select>
                            <div class="btncontrol">
                                <button type="submit" name="btneditd">Edid</button>
                            </div>
                        </form>
                        <?php
                            if(isset($_POST['btneditd'])){
                                $DateBegin      =$_POST['dateBegin'];
                                $Client         =$_POST['clientName'];
                                $ServiceType    =$_POST['service'];
                                $DomeinName     =$_POST['domain'];
                                $RenewDate      =$_POST['dateRenew'];
                                $Price_Renew    =$_POST['price'];
                                $Note           =$_POST['note'];
                                $Status         =1;
                                $ServiceID      =$_POST['relatedTo'];

                                $sql=$con->prepare('UPDATE  tbldomeinclients SET
                                                        DateBegin = :DateBegin,
                                                        Client = :Client,
                                                        ServiceType = :ServiceType,
                                                        DomeinName = :DomeinName,
                                                        RenewDate = :RenewDate,
                                                        Price_Renew =:Price_Renew,
                                                        Note =:Note,
                                                        Status =:Status,
                                                        ServiceID = :ServiceID
                                                    WHERE 
                                                        DomeinID =:DomeinID ');
                                $sql->execute(array(
                                    'DateBegin'     =>$DateBegin,
                                    'Client'        =>$Client ,
                                    'ServiceType'   =>$ServiceType,
                                    'DomeinName'    =>$DomeinName,
                                    'RenewDate'     =>$RenewDate,
                                    'Price_Renew'   =>$Price_Renew,
                                    'Note'          =>$Note,
                                    'Status'        =>$Status,
                                    'ServiceID'     =>$ServiceID,
                                    'DomeinID'      =>$domeinID
                                ));
                                echo '<script> location.href="ManageDomein.php" </script>';
                            }
                        ?>
                    </div>
                <?php
                }elseif($do=='active'){
                    $domeinID=(isset($_GET['id']))?$_GET['id']:0;
                    
                    $sql=$con->prepare('UPDATE tbldomeinclients SET Status = 1 WHERE DomeinID=?');
                    $sql->execute(array($domeinID));

                    echo '<script> location.href="ManageDomein.php" </script>';
                }elseif($do=='transfered'){
                    $domeinID=(isset($_GET['id']))?$_GET['id']:0;
                    
                    $sql=$con->prepare('UPDATE tbldomeinclients SET Status = 4 WHERE DomeinID=?');
                    $sql->execute(array($domeinID));

                    echo '<script> location.href="ManageDomein.php" </script>';
                }elseif($do =='cancel'){
                    $domeinID=(isset($_GET['id']))?$_GET['id']:0;

                    $sql=$con->prepare('UPDATE tbldomeinclients SET Status = 5 WHERE DomeinID=?');
                    $sql->execute(array($domeinID));

                    echo '<script> location.href="ManageDomein.php" </script>';
                }else{
                    header('location:index.php');
                }
            ?>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/ManageDomein.js"></script>
    <script src="js/sidebar.js"></script>
</body>