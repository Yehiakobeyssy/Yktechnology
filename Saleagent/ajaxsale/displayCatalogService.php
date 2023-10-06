<?php
    session_start();
    include '../../settings/connect.php';
    include '../../common/function.php';
    
    $searchtext = (isset($_GET['search'])) ? $_GET['search'] : '';
    $search = str_replace('_', ' ', $searchtext);
    
    
    $searchParam = "%" . $search . "%";

    $sql=$con->prepare('SELECT * FROM  tbllibrary 
                        WHERE Subject LIKE ? OR discription LIKE ?');
    $sql->execute(array($searchParam,$searchParam));
    $cards=$sql->fetchAll();

    foreach($cards as $card){
            echo '
                <div class="cardserivce" >
                    <div class="placeimage" data-index="'.$card['file'].'">
                        <img src="../images/libary/'.$card['image'].'" alt="">
                    </div>
                    <div class="discription">
                        <h3>'.$card['Subject'].'</h3>
                        <label for="">'.$card['discription'].'</label>
                    </div>
                </div>
            ';
    }
?>
<script>
    jQuery('.placeimage').click(function(){
        let id = jQuery(this).attr('data-index')
        window.open('../images/libary/'+id, '_blank');
    })
</script>