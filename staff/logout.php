<?php
session_start();

setcookie("staff","",time()-3600);
unset($_SESSION['staff']);

header("Location: index.php");

?>
