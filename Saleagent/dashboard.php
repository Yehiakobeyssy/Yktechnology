<?php
    session_start();
    if(!isset($_COOKIE['AgentID'])){
        if(!isset($_SESSION['AgentID'])){
            header('location:index.php');
        }
    }
    $agentId= (isset($_COOKIE['AgentID']))?$_COOKIE['AgentID']:$_SESSION['AgentID'];

    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';

    $sql=$con->prepare('SELECT saleActive,Sale_FName,Sale_LName,PromoCode FROM tblsalesperson WHERE SalePersonID =?');
    $sql->execute(array($agentId));
    $result=$sql->fetch();
    $isActive=$result['saleActive'];
    $firstname= $result['Sale_FName'];
    $lastName = $result['Sale_LName'];
    $full_name = $firstname .' ' . $lastName ;

    if($isActive == 0){
        setcookie("AgentID","",time()-3600);
        unset($_SESSION['AgentID']);
        echo '<script> location.href="index.php" </script>';
    }

    $promocode = $result['PromoCode'];
    $counts = countCard($con, $promocode);
?>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head> 
<body>
    <?php include 'include/navbar.php' ?>
    <div class="containerbody">
        <?php include 'include/sidebar.php' ?>
        <div class="includebody">
            <div class="carddash">
                <div class="right-side">
                    <h1>My Dashboard</h1>
                    <h3>Promocode:</h3>
                    <p id="mycode"><?php echo $result['PromoCode']?></p>
                </div>
                <div class="left-side">
                    <h3>Balance:</h3>
                    <?php
                        $sql=$con->prepare('SELECT SUM(Depit - Crieted) AS TotalAmount
                                            FROM tblaccountstatment_saleperson
                                            WHERE SaleManID = ?');
                        $sql->execute(array($agentId));
                        $result_balance = $sql->fetch();
                    ?>
                    <h1><?php echo '$' . number_format($result_balance['TotalAmount'], 2) ?></h1>
                </div>
            </div>
            <div class="card-grids">
                <div class="card">
                    <h2 class="card-title">Clients</h2>
                    <p class="card-count">General Count: <?php echo $counts['clients']; ?></p>
                </div>
                <div class="card">
                    <h2 class="card-title">Services</h2>
                    <p class="card-count">General Count: <?php echo $counts['services']; ?></p>
                    <div class="card-subtitles">
                        <div class="subtitle">
                            <h3>In Process</h3>
                            <p><?php echo $counts['in_process_services']; ?></p>
                        </div>
                        <div class="subtitle">
                            <h3>Expire Soon</h3>
                            <p><?php echo $counts['expire_soon_services']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <h2 class="card-title">Domein</h2>
                    <p class="card-count">General Count: <?php echo $counts['domein']; ?></p>
                    <div class="card-subtitles">
                        <div class="subtitle">
                            <h3>In Process</h3>
                            <p><?php echo $counts['in_process_domein']; ?></p>
                        </div>
                        <div class="subtitle">
                            <h3>Expire Soon</h3>
                            <p><?php echo $counts['expire_soon_domein']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <h2 class="card-title">Tickets</h2>
                    <p class="card-count">General Count: <?php echo $counts['tickets']; ?></p>
                    <div class="card-subtitles">
                        <div class="subtitle">
                            <h3>Open</h3>
                            <p><?php echo $counts['open_tickets']; ?></p>
                        </div>
                        <div class="subtitle">
                            <h3>Client Respond</h3>
                            <p><?php echo $counts['client_respond_tickets']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="last_transaction">
                <h4>Recent Transaction</h4>
                <table>
                    <?php
                        $sql=$con->prepare('SELECT 
                                                p.Payment_Date,
                                                c.Client_FName,
                                                c.Client_LName,
                                                p.Payment_Amount,
                                                p.invoiceID
                                            FROM
                                                tblclients AS c
                                            INNER JOIN
                                                tblpayments AS p ON c.ClientID = p.ClientID
                                            WHERE
                                                c.promo_Code = ? 
                                                AND p.paymentMethod != 2
                                            ORDER BY
                                                p.Payment_Date DESC
                                            LIMIT 3;');
                        $sql->execute(array($result['PromoCode']));
                        $lastTransaction = $sql->fetchAll();
                        foreach($lastTransaction as $transaction){
                            $commtion = calculateCommission($transaction['invoiceID'],$transaction['Payment_Amount'], $con);
                            echo '
                            <tr>
                                <td style="width: 10%;">'.$transaction['Payment_Date'].'</td>
                                <td style="width: 80%;">'.$transaction['Client_FName'].' '.$transaction['Client_LName'].' <strong>('.number_format($transaction['Payment_Amount'], 2).' $)</strong></td>
                                <td style="width: 10%;"><h5>'.number_format($commtion['commission'], 2).' $ </h5></td>
                            </tr>
                            ';
                        }
                    ?>
                </table>
            </div>

            <div class="services">
                <h4>Services</h4>
                <table>
                    <thead>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Service</th>
                        <th>price</th>
                        <th>Status</th>
                        <th>Done</th>
                    </thead>
                    <body>
                        <?php
                            $sql=$con->prepare("SELECT
                                                    CONCAT(c.Client_FName, ' ', c.Client_LName) AS ClientName,
                                                    cs.Date_service AS Date,
                                                    CONCAT(s.Service_Name, ' (', cs.ServiceTitle, ')') AS Service,
                                                    cs.Price,
                                                    sts.Status,
                                                    sts.Status_Color,
                                                CASE
                                                    WHEN cs.ServiceDone = 0 THEN 'In Progress'
                                                    WHEN cs.ServiceDone = 1 THEN 'Finish'
                                                END AS ServiceDone
                                                FROM
                                                    tblclientservices AS cs
                                                    INNER JOIN tblclients AS c ON cs.ClientID = c.ClientID
                                                    INNER JOIN tblservices AS s ON cs.ServiceID = s.ServiceID
                                                    INNER JOIN tblstatusservices AS sts ON cs.serviceStatus = sts.StatusSerID
                                                WHERE
                                                    c.promo_Code = ? -- Replace ? with the actual promo code
                                                ORDER BY
                                                    cs.ServicesID DESC;
                                                ");
                            $sql->execute(array($promocode));
                            $services = $sql->fetchAll();
                            foreach($services as $servic){
                                echo '
                                    <tr>
                                        <td>'.$servic['ClientName'].'</td>
                                        <td>'.date("d/m/Y", strtotime($servic['Date'])).'</td>
                                        <td>'.$servic['Service'].'</td>
                                        <td>'.number_format($servic['Price'],2).' $</td>
                                        <td style="color:'.$servic['Status_Color'].'">'.$servic['Status'].'</td>
                                        <td>'.$servic['ServiceDone'].'</td>
                                    </tr>
                                ';
                            }
                        ?>
                    </body>
                </table>
            </div>
            <div class="tickets">
                <h4>Tickets</h4>
                <table>
                    <thead>
                        <th>Client</th>
                        <th>Section</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Last Update</th>
                    </thead>
                    <tbody>
                        <?php
                            $sql=$con->prepare("SELECT
                                                    CONCAT(c.Client_FName, ' ', c.Client_LName) AS Client,
                                                    tt.TypeTicket AS Section,
                                                    t.ticketSubject AS Subject,
                                                    st.Status AS Status,
                                                    st.fontColor AS FontColor,
                                                    MAX(dt.Date) AS LastUpdate
                                                FROM
                                                    tblclients AS c
                                                INNER JOIN tblticket AS t ON c.ClientID = t.ClientID
                                                INNER JOIN tbltypeoftickets AS tt ON t.ticketSection = tt.TypeTicketID
                                                INNER JOIN tblstatusticket AS st ON t.ticketStatus = st.StatusTicketID
                                                LEFT JOIN tbldeiteilticket AS dt ON t.ticketID = dt.TicketID
                                                WHERE
                                                    c.promo_Code = ? -- Replace ? with the actual promo code
                                                GROUP BY
                                                    c.Client_FName, c.Client_LName, tt.TypeTicket, t.ticketSubject, st.Status, st.fontColor
                                                ORDER BY
                                                    MAX(dt.Date) DESC;
                                                ");
                            $sql->execute(array($promocode));
                            $tickets = $sql->fetchAll();
                            foreach($tickets as $ticket){
                                echo '
                                    <tr>
                                        <td>'.$ticket['Client'].'</td>
                                        <td>'.$ticket['Section'].'</td>
                                        <td>'.$ticket['Subject'].'</td>
                                        <td style="color:'.$ticket['FontColor'].'">'.$ticket['Status'].'</td>
                                        <td>'.$ticket['LastUpdate'].'</td>
                                    </tr>
                                ';
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/dashboard.js"></script>
    <script src="js/sidebar.js"></script>
</body>