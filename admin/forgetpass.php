<?php
    session_start();
    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';

    $do = (isset($_GET['do']))?$_GET['do']:'forget';
?>
    <link rel="stylesheet" href="css/forgetpass.css">
</head>
<body>
    <?php
        if($do=='forget'){?>
            <div class="forget_pass">
                <h2>Forget Password !</h2>
                <p>No problem please insert your email to reset your PASSWORD</p>
                <form action="" method="post">
                    <input type="email" name="txtemail" id="" required>
                    <div class="btn_control">
                        <button type="submit" name="btnreset">Reset My Password</button>
                    </div>
                </form>
                <?php
                    if(isset($_POST['btnreset'])){
                        $admin_email = $_POST['txtemail'];
                        $checkemail = checkItem('admin_email', 'tbladmin', $admin_email);
                        $timechanged = time();
                        if($checkemail == 1){
                            $sql=$con->prepare('SELECT admin_password,admin_ID FROM  tbladmin WHERE admin_email =?');
                            $sql->execute(array($admin_email));
                            $result=$sql->fetch();
                            $oldpass = $result['admin_password'];
                            $adid=$result['admin_ID'];
                            require_once '../mail.php';
                            $mail->setFrom($applicationemail, 'Reset Password');
                            $mail->addAddress($admin_email);
                            $mail->Subject = 'Reset Admin Password';
                            $mail->Body    = 'Please click the following link to reset your password <br> <a href="'.$websiteaddresse.'admin/forgetpass.php?do=resetpass&code='.$oldpass.'&duration='.$timechanged.'&adId='.$adid.'">RESET PASSWORD</a>';
                            $mail->send();
                            echo '
                                <div class="alert alert-success" role="alert">
                                    Please Check your email inbox to reset your password
                                </div>
                            ';
                        }else{
                            echo '
                                <div class="alert alert-danger" role="alert">
                                    Error! This e-mail dont exist in our database 
                                </div>
                            ';
                        }
                    }
                ?>
            </div>
        <?php
        }elseif($do=='resetpass'){
            $timeget = (isset($_GET['duration']))?$_GET['duration']:0;
            $code = (isset($_GET['code']))?$_GET['code']:0;
            $timenow=time();

            $difference = $timenow - $timeget;
            ?>
            <div class="container_resetpass">
                <?php
                    if($difference <= 500){
                        $checkpass= checkItem('admin_password','tbladmin',$code);
                        if($checkpass ==1){
                            $admin_ID = isset($_GET['adId'])?$_GET['adId']:0;
                            $check_admin= checkItem('admin_ID','tbladmin',$admin_ID);
                            if($check_admin ==1){?>
                                <h2>Reset Password</h2>
                                <div class="form_reset">
                                    <form action="" method="post">
                                        <table>
                                            <tr>
                                                <td><label for="">New Password</label></td>
                                                <td><input type="password" name="txtnew" id="" required></td>
                                            </tr>
                                            <tr>
                                                <td><label for="">Conform Password</label></td>
                                                <td><input type="password" name="txtcon" id="" required></td>
                                            </tr>
                                        </table>
                                        <div class="btn_control">
                                            <button type="submit" name="btnchange">Reset My Password</button>
                                        </div>
                                    </form>
                                    <?php
                                        if(isset($_POST['btnchange'])){
                                            $newpass = $_POST['txtnew'];
                                            $conform = $_POST['txtcon'];
                                            if($newpass == $conform){
                                                $changepass = sha1($newpass);
                                                $sql=$con->prepare('UPDATE tbladmin SET admin_password= ? WHERE admin_ID=?');
                                                $sql->execute(array($changepass,$admin_ID));
                                                echo '
                                                    <div class="alert alert-success" role="alert">
                                                        The Password Changed successfully 
                                                    </div>
                                                ';
                                                
                                                echo '<script>
                                                            setTimeout(function() {
                                                                window.location.href = "index.php";
                                                            }, 1500);
                                                        </script>';
                                            }else{
                                                echo '
                                                    <div class="alert alert-danger" role="alert">
                                                        Error !!! The password is not the same
                                                    </div>
                                                ';
                                            }
                                        }
                                    ?>
                                </div>
                            <?php
                            }else{
                                echo '
                                    <div class="alert alert-danger" role="alert">
                                        Error !!! This Link is not for you 
                                    </div>
                                ';
                            }
                        }else{
                            echo '
                                <div class="alert alert-danger" role="alert">
                                    Error !!! This Link is not for you 
                                </div>
                            ';
                        }
                    }else{
                        echo '
                            <div class="alert alert-danger" role="alert">
                                Too late !!! this link is expired please reset again 
                            </div>
                        ';
                    }
                ?>
            </div>
        <?php
        }else{
            header('location:../index.php');
        }
    ?>
    <?php include '../common/jslinks.php'?>
    <script src="js/forgetpass.js"></script>
</body>