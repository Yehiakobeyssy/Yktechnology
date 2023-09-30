<?php

    include '../../settings/connect.php';
    include '../../common/function.php';

    $searchtext=(isset($_GET['search']))?$_GET['search']:'';
    $search = str_replace('_', ' ', $searchtext);

    $sql = $con->prepare('SELECT t.ticketID, tt.TypeTicket, t.ticketSubject, st.Status, st.fontColor, MAX(d.Date) AS LastDate, c.Client_FName, c.Client_LName
                            FROM tblticket t
                            INNER JOIN tbltypeoftickets tt ON t.ticketSection = tt.TypeTicketID
                            INNER JOIN tblstatusticket st ON t.ticketStatus = st.StatusTicketID
                            LEFT JOIN tbldeiteilticket d ON t.ticketID = d.TicketID
                            LEFT JOIN tblclients c ON t.ClientID = c.ClientID
                            WHERE (tt.TypeTicket LIKE ?
                            OR t.ticketSubject LIKE ?
                            OR st.Status LIKE ?)
                            GROUP BY t.ticketID, tt.TypeTicket, t.ticketSubject, st.Status, st.fontColor, c.Client_FName, c.Client_LName
                            ORDER BY LastDate DESC');
    if(!empty($search)){
        $search="%".$search."%";
    }else{
        $search="%"." "."%";  
    }
    $sql->execute(array($search,$search,$search));
    $count=$sql->rowCount();
    $rows = $sql->fetchAll();
        
    if(isset($_GET['count'])){
        echo $count;
    }else{
        foreach($rows as $row){
            echo '<tr class="rowticket" data-index="'. $row['ticketID'] .'">
                    <td>' . $row['Client_FName'] . ' '.$row['Client_LName'].'</td>
                    <td>' . $row['TypeTicket'] . '</td>
                    <td>' . $row['ticketSubject'] . '</td>
                    <td style="color:'.$row['fontColor'].'">' . $row['Status'] . '</td>
                    <td>' . $row['LastDate'] . '</td>
                </tr>
            ';
        }
    }

?>

<script>
    jQuery('.rowticket').click(function(){
        let tickedID = jQuery(this).attr('data-index');
        location.href="ManageTickets.php?do=view&id="+tickedID;
    })
</script>