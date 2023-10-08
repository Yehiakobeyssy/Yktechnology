<?php
    session_start();
    include '../../settings/connect.php';
    include '../../common/function.php';

    $searchtext = (isset($_GET['search'])) ? $_GET['search'] : '';
    $search = str_replace('_', ' ', $searchtext);

    $catID = isset($_GET['catid'])?$_GET['catid']:0;
    $searchParam = "%" . $search . "%";


    $sql=$con->prepare('SELECT ServiceID,Service_Name,old_Price,Service_Price,Service_show,DurationName 
                                                    FROM tblservices
                                                    INNER JOIN  tblduration ON tblservices.Duration = tblduration.DurationID
                                                    WHERE Active =1 AND CategoryID=? AND 
                                                    (Service_Name LIKE ? )
                                                    ORDER BY ServiceID , Service_show DESC');
                                $sql->execute(array($catID,$searchParam));
                                $cards= $sql->fetchAll();
                                foreach($cards as $card){
                                    if($card['Service_show'] == 1){
                                        $textshow = 'Hide';
                                    }else{
                                        $textshow = 'Show';
                                    }
                                    echo '
                                    <div class="card_service">
                                        <div class="card_header">
                                            <h2>'.$card['Service_Name'].'</h2>
                                            <div class="price_service">
                                                <table>
                                                    <tr>
                                                        <td><label for="">Old Price</label></td>
                                                        <td><label for="">New Price</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td>'.number_format($card['old_Price'],2,'.','').'</td>
                                                        <td>'.number_format($card['Service_Price'],2,'.','').'</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <p>'.$card['DurationName'].'</p>
                                        </div>
                                        <div class="add_speafications">
                                            <input type="text" name="" id="txt'.$card['ServiceID'].'">
                                            <button class="btnaddspe" data-index="'.$card['ServiceID'].'"><i class="fa-solid fa-check"></i></button>
                                        </div>
                                        <div class="allspeafication">
                                            <ul>';
                                                $stat=$con->prepare('SELECT SpeaficationsID,Speafications FROM  tblspeafications
                                                                    WHERE ServiceID=?');
                                                $stat->execute(array($card['ServiceID']));
                                                $speaficatios = $stat->fetchAll();
                                                foreach($speaficatios as $spea){
                                                    echo '
                                                        <li><span>'.$spea['Speafications'].'</span> <button class="btndeletespe" data-index="'.$spea['SpeaficationsID'].'"><i class="fa-solid fa-trash-can"></i></button> </li>
                                                    ';
                                                }
                                            echo '</ul>
                                        </div>
                                        <div class="control_servis">
                                            <a href="ManageItems.php?cat='.$catID.'&do=show&id='.$card['ServiceID'].'" class="btn btn-success">'.$textshow.'</a>
                                            <a href="ManageItems.php?cat='.$catID.'&do=edid&id='.$card['ServiceID'].'" class="btn btn-warning">edit</a>
                                            <a href="ManageItems.php?cat='.$catID.'&do=delete&id='.$card['ServiceID'].'" class="btn btn-danger">Delete</a>
                                        </div>
                                    </div>
                                    ';
                            }
?>
<div class="runajax"></div>
<script>
jQuery('.btnaddspe').click(function(){
    let serviceID = jQuery(this).attr('data-index');
    let txttext =jQuery('#txt'+serviceID).val();
    let newtext = txttext.replace(/ /g, '_');
    jQuery('.runajax').load('ajaxadmin/insertspeafications.php?serviceID='+serviceID+'&text='+newtext);
    location.href="ManageItems.php?cat="+<?php echo  $catID?>;
})
jQuery('.btndeletespe').click(function(){
    let speaficID = jQuery(this).attr('data-index');
    jQuery('.runajax').load('ajaxadmin/deleteonespafication.php?spaID='+speaficID);
    location.href="ManageItems.php?cat="+<?php echo  $catID?>;
})
</script>