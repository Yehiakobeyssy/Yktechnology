<?php
    session_start();
    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';
?>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form class="login-form" action="" method="post">
            <label for="username">Email:</label>
            <input type="text" id="username" name="username" placeholder="Your email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Your password" required>

            <div class="remember-forgot">
                <div>
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember Me:</label>
                </div>
                <div>
                    <a href="forgot-password.html">Forgot Password?</a>
                </div>
            </div>
            <button type="submit" name="btnlogin">Login</button>
        </form>
        <?php
            if(isset($_POST['btnlogin'])){
                $email    = $_POST["username"];
                $password = sha1($_POST["password"]);

                $query = "SELECT SalePersonID FROM tblsalesperson WHERE email_Sale = :email AND password_sale = :password";
                $stmt = $con->prepare($query);
                $stmt->bindParam(":email", $email);
                $stmt->bindParam(":password", $password);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if ($result) {
                    if (isset($_POST["remember"])) {
                        setcookie("AgentID", $result["SalePersonID"], time() + 3600 * 24 * 30, "/");
                    } else {
                        $_SESSION["AgentID"] = $result["SalePersonID"];
                    }
                    //header("Location: dashboard.php");
                    exit();
                } else {
                    $error_message = "Invalid email or password.";
                }
            }
        ?>
            <?php
                if (isset($error_message)) {
                    echo '<div class="alert alert-danger" role="alert"><p>'.$error_message.'</p></div>';
                }
                ?>
    </div>
    <?php include '../common/jslinks.php' ?>
    <script src="js/index.js"></script>
</body>