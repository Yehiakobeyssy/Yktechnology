<?php
    session_start();
    if(!isset($_COOKIE['AgentID'])){
        if(!isset($_SESSION['AgentID'])){
            header('location:index.php');
        }
    }
    $agentId= (isset($_COOKIE['AgentID']))?$_COOKIE['AgentID']:$_SESSION['AgentID'];

    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';

    $sql=$con->prepare('SELECT saleActive,Sale_FName,Sale_LName,PromoCode FROM tblsalesperson WHERE SalePersonID =?');
    $sql->execute(array($agentId));
    $result=$sql->fetch();
    $isActive=$result['saleActive'];
    $firstname= $result['Sale_FName'];
    $lastName = $result['Sale_LName'];
    $full_name = $firstname .' ' . $lastName ;
    $promocode= $result['PromoCode'];

    if($isActive == 0){
        setcookie("AgentID","",time()-3600);
        unset($_SESSION['AgentID']);
        echo '<script> location.href="index.php" </script>';
    }
?>
    <link rel="stylesheet" href="css/ManageDomein.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head> 
<body>
    <?php include 'include/navbar.php' ?>
    <div class="containerbody">
        <?php include 'include/sidebar.php' ?>
        <div class="includebody">
        <div class="title">
                <h1>Mange Domeins</h1>
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
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/ManageDomein.js"></script>
    <script src="js/sidebar.js"></script>
</body>