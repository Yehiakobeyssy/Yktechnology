<?php
    session_start();

    if(isset($_COOKIE['useradmin'])){
        header('location:dashboard.php');
    }elseif(isset($_SESSION['useradmin'])){
        header('location:dashboard.php');
    }
    
    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';
?>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="loginform">
        <div class="logo_login">
            <i class="fa-solid fa-user-secret"></i>
        </div>
        <form action="" method="post">
            <div class="formlogin">
                <div class="username">
                    <span><i class="fa-solid fa-user"></i></span>
                    <input type="text" name="txtuser" id="" placeholder="E-mail">
                </div>
                <div class="password">
                    <span><i class="fa-solid fa-lock"></i></span>
                    <input type="password" name="txtpassword" id="" placeholder="Password">
                </div>
                <div class="remember_pass">
                    <div class="rem">
                        <input type="checkbox" name="txtrem" id="cherem">
                        <label for="cherem">Remember Me</label>
                    </div>
                    <div class="forget_pass">
                        <a href="forgetpass.php">Forget Password? </a>
                    </div>
                </div>
                <div class="btncontrol">
                    <button type="submit" name="btnlogin">LOGIN</button>
                </div>
            </div>
        </form>
        <?php
            if(isset($_POST['btnlogin'])){
                $usename  = $_POST['txtuser'];
                $password = sha1($_POST['txtpassword']);
                $remember = (isset($_POST['txtrem']))?1:0;

                $sql=$con->prepare('SELECT admin_ID 
                                    FROM tbladmin 
                                    WHERE admin_email = ? AND admin_password = ? 
                                    LIMIT 1');
                $sql->execute(array($usename,$password));
                $check_admin=$sql->rowCount();

                if($check_admin == 1){
                    $admin_result=$sql->fetch();
                    $adminID= $admin_result['admin_ID'];
                    if($remember == 1){
                        setcookie('useradmin', $adminID, time()+3600 * 24 * 365);
                    }else{
                        $_SESSION['useradmin'] = $adminID;
                    }
                    header('location:dashboard.php');
                }else{
                    echo '
                        <div class="alert alert-danger" role="alert">
                            Error! Email or  Password incorrect!!!
                        </div>
                    ';
                }
            }
        ?>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/index.js"></script>
</body>