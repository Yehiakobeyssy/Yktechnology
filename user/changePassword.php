<?php
    session_start();
    if(!isset($_COOKIE['user'])){
        if(!isset($_SESSION['user'])){
            header('location:index.php');
        }
    }
    $clientId= (isset($_COOKIE['user']))?$_COOKIE['user']:$_SESSION['user'];
    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';

    $sql=$con->prepare('SELECT Client_FName,Client_LName,client_active FROM  tblclients WHERE ClientID=?');
    $sql->execute(array($clientId));
    $clientinfo = $sql->fetch();
    $clientName = $clientinfo['Client_FName'].' '. $clientinfo['Client_LName'];

    if($clientinfo['client_active'] == 0){
        setcookie("user","",time()-3600);
        unset($_SESSION['user']);
        echo '<script> location.href="index.php" </script>';
    }
?>
    <link rel="stylesheet" href="css/changePassword.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
    <?php include 'include/navbar.php' ?>
    <div class="container_changepass">
        <h2>Change Password</h2>
        
        <form id="passwordChangeForm" method="post">
            <div class="form-group">
                <label for="currentPassword">Current Password</label>
                <input type="password" id="currentPassword" name="currentPassword" required>
            </div>
            <div class="form-group">
                <label for="newPassword">New Password</label>
                <input type="password" id="newPassword" name="newPassword" required>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm New Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
                <div class="error-message" id="passwordError"></div>
            </div>
            <div class="controlbtn">
                <button type="submit" class="btn btn-success" name="btnchangePassword">Change Password</button>
            </div>
        </form>
        <?php
            if(isset($_POST['btnchangePassword'])){
                $oldpass = $_POST['currentPassword'];
                $newPass = $_POST['newPassword'];
                $conform = $_POST['confirmPassword'];

                if( $newPass == $conform){
                    $newpassdb = sha1($newPass);
                    $oldpass = sha1($oldpass);

                    $sql=$con->prepare('SELECT Client_Password FROM  tblclients WHERE ClientID=?');
                    $sql->execute(array($clientId));
                    $result_old= $sql->fetch();

                    if($result_old['Client_Password'] == $oldpass){

                        $sql=$con->prepare('UPDATE tblclients SET  Client_Password = ? WHERE ClientID=?');
                        $sql->execute(array($newpassdb,$clientId));
                        echo '
                            <div class="alert alert-success" role="alert">
                                Password changed successfully!
                            </div>
                        ';
                    }else{
                        echo '
                            <div class="alert alert-danger" role="alert">
                                Old password Incorect
                            </div>
                        ';
                    }
                }else{
                    echo '
                        <div class="alert alert-danger" role="alert">
                            New password and confirm password must match
                        </div>
                    ';
                }
            }
        ?>

    </div>
    <?php include '../common/jslinks.php' ?>
    <script src="js/changePassword.js"></script>
    <script src="js/navbar.js"></script>
</body>