<nav>
    <div class="title_nav">
        <img src="../images/logo.png" alt="">
        <h3>Welcome <span><?php echo $full_name ?></span></h3>
    </div>
    <div class="navbutton">
        <a href="updateProfile.php"><i class="fa-solid fa-id-badge"></i>Profile</a>
        <form action="" method="post">
            <button type="submit" name="btnlogout"><i class="fa-solid fa-right-from-bracket"></i>Logout</button>
        </form>
    </div>
</nav>
<?php
    if(isset($_POST['btnlogout'])){
        setcookie("AgentID","",time()-3600);
        unset($_SESSION['AgentID']);
        echo '<script> location.href="index.php" </script>';
    }
?>