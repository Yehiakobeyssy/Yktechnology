<?php
    include '../../settings/connect.php';
    include '../../common/function.php';

    $useremail=(isset($_GET['useremail']))?$_GET['useremail']:'';
    $checkemail = checkItem('Client_email','tblclients',$useremail);

    if($checkemail == 1){
        echo '
            <div class="alert alert-danger" role="alert" style="width:80%;margin: 0 auto">
                This email is used before Please click forget password to reset it !
            </div>
        ';
    }elseif($checkemail == 0){
        echo '
            <div class="alert alert-success" role="alert" style="width:80%;margin: 0 auto">
                The email can be use 
            </div>
        ';
    }
?>