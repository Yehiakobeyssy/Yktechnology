<?php
    include 'settings/connect.php';
    include 'common/function.php';

    $cat = (isset($_GET['cat']))?$_GET['cat']:0;
    $search = (isset($_GET['search']))?$_GET['search']:'';

    $sql=$con->prepare('SELECT ServiceID,Service_Name,Service_Price,DurationName,old_Price FROM  tblservices
                        INNER JOIN tblduration ON tblservices.Duration=tblduration.DurationID 
                        WHERE Service_show = 1
                        AND   Active =1
                        AND   CategoryID= ? 
                        AND   Service_Name LIKE ?');
    if(!empty($search)){
        $search="%".$search."%";
    }else{
        $search="%".""."%";  
    }
    $sql->execute(array($cat,$search));
    $count_services=$sql->rowCount();

    if($count_services ==0){
        echo '
            <div class="alert alert-warning" role="alert">
                Now we dont have any Services.
            </div>
        ';
    }else{
        $services = $sql->fetchAll();
        foreach($services as $ser){
            echo '
                <div class="card_service">
                    <div class="header_service">
                        <h4>'.$ser['Service_Name'].'</h4>
                        <div class="prices">
                            <span id="oldprice"><del>'.number_format($ser['old_Price'],2,'.','').'</del></span> 
                            <h2>'.number_format($ser['Service_Price'],2,'.','').' $</h2>
                        </div>
                        <p>'.$ser['DurationName'].'</p>
                    </div>
                    <div class="specfication_service">
                        <ul>';
                        $stat=$con->prepare('SELECT Speafications FROM tblspeafications WHERE ServiceID=?');
                        $stat->execute(array($ser['ServiceID']));
                        $speafications=$stat->fetchAll();
                        foreach($speafications as $spe){
                            echo '<li>'.$spe['Speafications'].'</li>';
                        }
                        echo '</ul>
                    </div>
                    <div class="fooder_service">
                        <button class="btnorder" data-index="'.$ser['ServiceID'].'">Order Now </button>
                    </div>
                </div>
            ';
        }
    }
?>
<div class="addcart"></div>
<script>
    jQuery('.btnorder').click(function(){
        let serID = jQuery(this).data('index');
        jQuery('.addcart').load('addtochart.php?serID='+serID);
        jQuery('#count_cart').load('ajaxcountcart.php');
        jQuery('#count_cart').load('ajaxcountcart.php');
        jQuery("#count_cart").show();
    })
</script>