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
    <link rel="stylesheet" href="css/managePaymentMethod.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Payment Methots</h1>
                <a href="managePaymentMethod.php?do=add" class="btn btn-primary btnadd">Add Payment Methot</a>
            </div>
            <?php
                $do=(isset($_GET['do']))?$_GET['do']:'manage';
                if($do=='manage'){?>
                <div class="mangebox">
                    <div class="search-container">
                        <input type="text" class="search-input" placeholder="Search ..." id="txtsearch">
                    </div>
                    <div class="cards_payments"></div>
                </div>
                <?php
                }elseif($do=='add'){?>
                <div class="frmadd">
                    <h1>New Methot</h1>
                    <form action="" method="post">
                        <label for="">Methot</label>
                        <input type="text" name="txtMethot" id="">
                        <label for="">Discription</label>
                        <textarea name="txtDis" id="" rows="10"></textarea>
                        <div class="btncontrol">
                            <button type="submit" name="btnSave">Save</button>
                        </div>
                    </form>
                    <?php
                        if(isset($_POST['btnSave'])){
                            $methot = $_POST['txtMethot'];
                            $note = $_POST['txtDis'];

                            $sql=$con->prepare('INSERT INTO tblpayment_method (methot,note,method_active) VALUES (:methot,:note,:method_active)');
                            $sql->execute(array(
                                'methot'        => $methot,
                                'note'          => $note, 
                                'method_active' => 1
                            ));
                            echo '<script> location.href="managePaymentMethod.php" </script>';
                        }
                    ?>
                </div>
                <?php
                }elseif($do=='edid'){
                    $payentid = (isset($_GET['id']))?$_GET['id']:0;
                    $sql=$con->prepare('SELECT * FROM tblpayment_method WHERE paymentmethodD = ? ');
                    $sql->execute(array($payentid));
                    $paymentInfo =$sql->fetch();
                ?>
                <div class="frmadd">
                    <h1>Edid Methot</h1>
                    <form action="" method="post">
                        <label for="">Methot</label>
                        <input type="text" name="txtMethot" id="" value="<?php echo $paymentInfo['methot']?>">
                        <label for="">Discription</label>
                        <textarea name="txtDis" id="" rows="10"><?php echo $paymentInfo['note']?></textarea>
                        <div class="btncontrol">
                            <button type="submit" name="btnEdt">Edit</button>
                        </div>
                    </form>
                    <?php
                        if(isset($_POST['btnEdt'])){
                            $methot = $_POST['txtMethot'];
                            $note = $_POST['txtDis'];

                            $sql=$con->prepare('UPDATE  tblpayment_method SET 
                                                methot = :methot,
                                                note   = :note,
                                                method_active = :method_active
                                                WHERE paymentmethodD =:paymentmethodD');
                            $sql->execute(array(
                                'methot'        => $methot,
                                'note'          => $note, 
                                'method_active' => $paymentInfo['method_active'],
                                'paymentmethodD'=>$payentid
                            ));
                            echo '<script> location.href="managePaymentMethod.php" </script>';
                        }
                    ?>
                </div>
                <?php
                }elseif($do=='Delete'){
                    $payentid = (isset($_GET['id']))?$_GET['id']:0;
                    $sql=$con->prepare('SELECT method_active FROM tblpayment_method WHERE paymentmethodD = ? ');
                    $sql->execute(array($payentid));
                    $paymentInfo =$sql->fetch();

                    if($paymentInfo['method_active'] == 1){
                        $sql=$con->prepare('UPDATE  tblpayment_method SET method_active = 0  WHERE paymentmethodD = ?');
                        $sql->execute(array($payentid));
                    }else{
                        $sql=$con->prepare('UPDATE  tblpayment_method SET method_active = 1  WHERE paymentmethodD = ?');
                        $sql->execute(array($payentid));
                    }
                    echo '<script> location.href="managePaymentMethod.php" </script>';
                }else{
                    echo '<script> location.href="index.php" </script>';
                }
            ?>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/managePaymentMethod.js"></script>
    <script src="js/sidebar.js"></script>
</body>