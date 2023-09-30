<?php
    session_start();

    setcookie("user","",time()-3600);
    unset($_SESSION['user']);
    echo '<script> location.href="../../index.php" </script>';
?>