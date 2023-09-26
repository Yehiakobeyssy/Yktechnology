<header>
    <div class="title">
        <h3> Welcome <span> <?php echo $full_name ?> </span></h3>
    </div>
    <div class="nav">
        <a href="../index.php"><i class="fa-solid fa-house"></i></a>
        <form action="" method="post">
            <button type="submit" name="btnlogout"><i class="fa-solid fa-right-from-bracket"></i></button>
        </form>
    </div>
</header>
<?php
    if(isset($_POST['btnlogout'])){
        setcookie("useradmin","",time()-3600);
        unset($_SESSION['useradmin']);
        echo '<script> location.href="index.php" </script>';
    }
?>
    