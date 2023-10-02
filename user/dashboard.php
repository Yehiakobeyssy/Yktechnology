<?php
    session_start();
    if(!isset($_COOKIE['user'])){
        if(!isset($_SESSION['user'])){
            header('location:index.php');
        }
    }
    $clientId= (isset($_COOKIE['user']))?$_COOKIE['user']:$_SESSION['user'];
    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';

    $sql=$con->prepare('SELECT Client_FName,Client_LName,client_active FROM  tblclients WHERE ClientID=?');
    $sql->execute(array($clientId));
    $clientinfo = $sql->fetch();
    $clientName = $clientinfo['Client_FName'].' '. $clientinfo['Client_LName'];

    if($clientinfo['client_active'] == 0){
        setcookie("user","",time()-3600);
        unset($_SESSION['user']);
        echo '<script> location.href="index.php" </script>';
    }

    //update serive 

    $sql=$con->prepare('UPDATE tblclientservices
                        SET serviceStatus = 2
                        WHERE serviceStatus < 3
                        AND Dateend BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 15 DAY)
                        AND ClientID = ?;');
    $sql->execute(array($clientId));

    $sql=$con->prepare('UPDATE tblclientservices
                        SET serviceStatus = 3
                        WHERE serviceStatus < 4
                        AND Dateend < DATE_ADD(CURDATE(), INTERVAL 5 DAY)
                        AND ClientID = ?;');
    $sql->execute(array($clientId));


    //update domein 

    $sql=$con->prepare('UPDATE tbldomeinclients
                        SET Status = 2
                        WHERE RenewDate BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 45 DAY)
                        AND Status = 1
                        AND Client = ?;
                        ');
    $sql->execute(array($clientId));
?>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
    <?php include 'include/navbar.php' ?>
    <div class="card_user">
        <div class="userinfo">
            <?php
                $sql=$con->prepare('SELECT Client_FName,Client_LName,Client_addresse,Client_city,CountryName 
                                    FROM  tblclients 
                                    INNER JOIN  tblcountrys ON tblclients.Client_country = tblcountrys.CountryID
                                    WHERE ClientID=?');
                $sql->execute(array($clientId));
                $result=$sql->fetch();

                $stat=$con->prepare('SELECT
                                        p.ClientID,
                                        SUM(p.Payment_Amount) AS TotalPayments,
                                        SUM(i.TotalAmount + i.TotalTax) AS TotalInvoiceAmount,
                                        SUM(p.Payment_Amount) - SUM(i.TotalAmount + i.TotalTax) AS TotalBalance
                                    FROM
                                        tblpayments p
                                    JOIN
                                        tblinvoice i ON p.InvoiceID = i.InvoiceID
                                    WHERE
                                        p.ClientID = ?
                                        AND i.Invoice_Status < 3
                                    GROUP BY
                                        p.ClientID;
                                    ');
                $stat->execute(array($clientId));
                $checkresult=$stat->rowCount();
                if($checkresult > 0){
                    $resultbalance=$stat->fetch();
                    $totalbalance = $resultbalance['TotalBalance'];
                    if($totalbalance  < 0){
                        $totalbalance = 0 ;
                    }
                }else{
                    $totalbalance = 0 ;
                }

            ?>
            <h3><?php echo $clientName ?> <span>| <span id="user_Balance"><?php echo number_format($totalbalance,2,'.','').' $' ?></span> </span></h3>
            <h1>Your Dashboard</h1>
            <label for=""><?php echo $result['Client_addresse'] ?></label><br>
            <label for=""><?php echo $result['CountryName'] ?> <span>| <?php echo $result['Client_city'] ?></span></label> <a href=""><i class="fa-solid fa-pen"></i></a>
        </div>
        <div class="dashboradimg">
            <img src="../images/synpoles/userDashboard.png" alt="" srcset="">
        </div>
    </div>
    <div class="statistic">
        <div class="card_satstic card1">
            <img src="../images/synpoles/Services.png" alt="" srcset="">
            <h4>Services</h4>
            <?php
                $sql=$con->prepare('SELECT ServicesID FROM tblclientservices WHERE serviceStatus<4 AND ClientID=?');
                $sql->execute(array($clientId));
                $countService= $sql->rowCount();
            ?>
            <h1><?php echo $countService ?></h1>
        </div>
        <div class="card_satstic card2">
            <img src="../images/synpoles/Domain.png" alt="" srcset="">
            <h4>Domains</h4>
            <?php
                $sql=$con->prepare('SELECT DomeinID  FROM tbldomeinclients 
                                    WHERE (Status =1 OR Status=2 ) AND Client=?');
                $sql->execute(array($clientId));
                $countDomains=$sql->rowCount();                    
            ?>
            <h1><?php echo $countDomains ?></h1>
        </div>
        <div class="card_satstic card3">
            <img src="../images/synpoles/ticket.png" alt="" srcset="">
            <h4>Tickets</h4>
            <?php
                $sql=$con->prepare('SELECT ticketID FROM  tblticket WHERE ticketStatus <> 4 AND ClientID = ?');
                $sql->execute(array($clientId));
                $countticket=$sql->rowCount();
            ?>
            <h1><?php echo $countticket ?></h1>
        </div>
        <div class="card_satstic card4">
            <img src="../images/synpoles/Invoices.png" alt="" srcset="">
            <h4>Invoices</h4>
            <?php
                $sql=$con->prepare('SELECT InvoiceID FROM tblinvoice WHERE Invoice_Status=1 AND ClientID = ?');
                $sql->execute(array($clientId));
                $countInvoice=$sql->rowCount();
            ?>
            <h1><?php echo $countInvoice ?></h1>
        </div>
    </div>
    <div class="active_services">
        <h3>Your effective products/services</h3>
        <div class="deiteil_service">
            <?php 
                $sql=$con->prepare('SELECT cs.ServicesID,ts.Service_Name,cs.ServiceTitle,cs.Dateend
                                    FROM   tblclientservices cs
                                    JOIN   tblservices ts ON cs.ServiceID = ts.ServiceID
                                    JOIN   tblduration d ON ts.Duration = d.DurationID
                                    WHERE  cs.ClientID = ? AND cs.serviceStatus = 1
                                    ORDER BY cs.Dateend DESC; ');
                $sql->execute(array($clientId));
                $rows=$sql->fetchAll();
                foreach($rows as $row){
                    echo '
                        <div class="single_service" data-index="'.$row['ServicesID'].'">
                            <h5>'.$row['Service_Name'].'</h5>
                            <h5>'.$row['ServiceTitle'].'</h5>
                            <h5>'.$row['Dateend'].'</h5>
                        </div>
                    ';
                }
            ?>

        </div>
        <div class="showall">
            <a href="ManageService.php" class="btn btn-primary">My Products and services</a>
        </div>
    </div>
    <div class="notifications">
        <div class="left_site">
            <?php
                $sql=$con->prepare('SELECT COUNT(DomeinID)
                                    FROM tbldomeinclients
                                    WHERE Client = ? 
                                    AND (Status = 1 OR Status = 2)
                                    AND RenewDate <= DATE_ADD(CURDATE(), INTERVAL 45 DAY);');
                $sql->execute(array($clientId));
                $result_domain_will_expirred= $sql->fetch();
                $countdomeins=$result_domain_will_expirred['COUNT(DomeinID)'];

                if($countdomeins > 0){
                    $dispalydomein='block';
                    $textdisplay='You have '.$countdomeins.' Domein/s expiring after 45 days.Please Renew it now';
                }else{
                    $dispalydomein='none';
                    $textdisplay='';
                }

                $sql=$con->prepare('SELECT COUNT(InvoiceID)
                                    FROM (
                                        SELECT InvoiceID, InvoiceDate, ClientID, Invoice_Status, DATE_ADD(InvoiceDate, INTERVAL 30 DAY) AS datedeo
                                        FROM tblinvoice
                                    ) AS subquery
                                    WHERE Invoice_Status = 1
                                    AND datedeo < CURDATE()
                                    AND ClientID = ?;');
                $sql->execute(array($clientId));
                $result_countInvoices = $sql->fetch();
                $countinvoices=$result_countInvoices['COUNT(InvoiceID)'];

                if($countinvoices > 0){
                    $dispalyinvoice='block';
                    $textdisplayinvoice='You have '.$countinvoices.' invoices not paid; please pay before losing your service.';
                }else{
                    $dispalyinvoice='none';
                    $textdisplayinvoice='';
                }

                $sql = $con->prepare('SELECT t.ticketID, tt.TypeTicket, t.ticketSubject, st.Status, st.fontColor, MAX(d.Date) AS LastDate
                                    FROM tblticket t
                                    INNER JOIN tbltypeoftickets tt ON t.ticketSection = tt.TypeTicketID
                                    INNER JOIN tblstatusticket st ON t.ticketStatus = st.StatusTicketID
                                    LEFT JOIN tbldeiteilticket d ON t.ticketID = d.TicketID
                                    WHERE t.ClientID = ? AND t.ticketStatus < 4
                                    GROUP BY t.ticketID, tt.TypeTicket, t.ticketSubject, st.Status, st.fontColor
                                    ORDER BY LastDate DESC');
                $sql->execute(array($clientId));
                $countticket= $sql->rowCount();

                if($countticket == 0){
                    $dispalyTicket='none';
                }else{
                    $dispalyTicket='block';
                    $ticktes = $sql->fetchAll();
                }
            ?>
            <div class="Domeins_expiring" style="display: <?php echo $dispalydomein?>;">
                <div class="title_notification">
                    <h3>Domains expiring soon</h3>
                </div>
                <?php
                echo '<h5 class="textnotification">'.$textdisplay.' </h5>';
                ?>
                <div class="btngotodeiteil">
                    <a href="ManageDomein.php" class="btn btn-primary">All Domeins</a>
                </div>
            </div>
        </div>
        <div class="right_site">
            <div class="invoices_not_paid" tyle="display: <?php echo $dispalyinvoice?>;">
                <div class="title_notification">
                    <h3>Overdue bills</h3>
                </div>
                <?php
                echo '<h5 class="textnotification">'.$textdisplayinvoice.' </h5>';
                ?>
                <div class="btngotodeiteil">
                    <a href="manageinvoice.php" class="btn btn-primary">All Invoices</a>
                </div>
            </div>
            <div class="tickets" style="display: <?php echo $dispalyTicket?>;">
                <div class="title_notification">
                    <h3>New tickets</h3>
                </div>
                <div class="all_tickets">
                    <?php
                        foreach($ticktes as $ticket){
                            echo '
                                <div class="one_ticket" data-index="'.$ticket['ticketID'].'">
                                    <div class="info_ticket">
                                        <h4># <span> '.$ticket['ticketID'].'</span> - <span>'.$ticket['ticketSubject'].'</span></h4>
                                        <label for="">Last update ( <span>'.$ticket['LastDate'].'</span> )</label>
                                    </div>
                                    <div class="status_ticket">
                                        <span style="background-color:'.$ticket['fontColor'].';">'.$ticket['Status'].'</span>
                                    </div>
                                </div>
                            ';
                        }
                    ?>
                </div>
                <div class="btngotodeiteil">
                    <a href="ManageTickets.php" class="btn btn-primary">All Tickets</a>
                </div>
            </div>
        </div>
    </div>
    <?php include '../common/jslinks.php' ?>
    <script src="js/dashboard.js"></script>
    <script src="js/navbar.js"></script>
</body>