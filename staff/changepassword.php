<?php
    session_start();

    if(!isset($_COOKIE['staff'])){
        if(!isset($_SESSION['staff'])){
            header('location:index.php');
        }
    }
    $staff_Id= (isset($_COOKIE['staff']))?$_COOKIE['staff']:$_SESSION['staff'];

    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';

    $sql=$con->prepare('SELECT Fname,MidelName,LName,accepted , block FROM tblstaff WHERE staffID  = ?');
    $sql->execute(array($staff_Id));
    $result= $sql->fetch();
    $accepted = $result['accepted'];
    $block = $result['block'];
    $staff_name = $result['Fname'].' '.$result['MidelName'].' '.$result['LName'];

    if($block == 1){
        header('location:index.php');
    }else{
        if($accepted == 0){
            header('location:index.php');
        }
    }

    $do=(isset($_GET['do']))?$_GET['do']:'manage';

?>
    <link rel="stylesheet" href="css/changepassword.css">
</head>
<body>
    <?php include 'include/headerstaff.php' ?>
    <main>
        <?php include 'include/aside.php' ?>
        <div class="project_container">
            <div class="change-password">
                <h2>Change Password</h2>
                <div id="message"></div>
                <form id="passwordForm" method="post" action="">
                    <label for="current_password">Current Password</label>
                    <input type="password" name="current_password" id="current_password" required>

                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" required>

                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>

                    <button type="submit" name="change_password">Change Password</button>
                </form>
            </div>
        </div>
        <?php
            if(isset($_POST['change_password']) && $staff_Id) {
                $current_password = $_POST['current_password'];
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];

                // Validate passwords match
                if($new_password !== $confirm_password){
                    echo "<p class='error'>New password and confirmation do not match.</p>";
                    exit;
                }

                // Fetch current hashed password from DB
                $stmt = $con->prepare("SELECT staffPassword FROM tblstaff WHERE staffID  = ?");
                $stmt->execute([$staff_Id]);
                $staff = $stmt->fetch(PDO::FETCH_ASSOC);

                if(!$staff || !password_verify($current_password, $staff['staffPassword'])) {
                    echo "<p class='error'>Current password is incorrect.</p>";
                    exit;
                }

                // Hash new password
                $new_hashed = sha1($new_password, PASSWORD_DEFAULT);

                // Update password
                $update = $con->prepare("UPDATE tblstaff SET staffPassword = ? WHERE staffID  = ?");
                if($update->execute([$new_hashed, $staff_Id])){
                    echo "<p class='success'>Password changed successfully!</p>";
                } else {
                    echo "<p class='error'>Failed to update password. Try again.</p>";
                }
            }
        ?>
        
    </main>
    <?php include '../common/jslinks.php' ?>
    <script src="js/changepassword.js"></script>
</body>