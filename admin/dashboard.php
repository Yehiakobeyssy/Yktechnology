<?php
    session_start();

    if(!isset($_COOKIE['useradmin'])){
        if(!isset($_SESSION['useradmin'])){
            header('location:index.php');
        }
    }
    $adminId= (isset($_COOKIE['useradmin']))?$_COOKIE['useradmin']:$_SESSION['useradmin'];

    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';

    $sql=$con->prepare('SELECT admin_active,admin_FName,admin_LName FROM  tbladmin WHERE admin_ID=?');
    $sql->execute(array($adminId));
    $result=$sql->fetch();
    $isActive=$result['admin_active'];
    $firstname= $result['admin_FName'];
    $lastName = $result['admin_LName'];
    $full_name = $firstname .' ' . $lastName ;

    if($isActive == 0){
        setcookie("useradmin","",time()-3600);
        unset($_SESSION['useradmin']);
        echo '<script> location.href="index.php" </script>';
    }

    
    $currentDate = date('Y-m-d');
    $endDate45Days = date('Y-m-d', strtotime('+45 days'));

    $updateQuery1 = "   UPDATE tbldomeinclients 
                        SET Status = 2
                        WHERE Status = 1 
                        AND RenewDate BETWEEN '$currentDate' AND '$endDate45Days' 
                        AND Status NOT IN (4, 5)";
    
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    

    $updateQuery2 = "   UPDATE tbldomeinclients 
                        SET Status = 3
                        WHERE Status = 1 
                        AND RenewDate = '$yesterday' 
                        AND Status NOT IN (4, 5)";
    
    try {
        $stmt1 = $con->prepare($updateQuery1);
        $stmt1->execute();
    
        $stmt2 = $con->prepare($updateQuery2);
        $stmt2->execute();
    
    } catch (PDOException $e) {

    }
    

    $currentDate = date('Y-m-d');
    $endDate15Days = date('Y-m-d', strtotime('+15 days'));

    $updateQuery3 = "   UPDATE tblclientservices 
                        SET serviceStatus = 2
                        WHERE serviceStatus = 1 
                        AND Dateend BETWEEN '$currentDate' AND '$endDate15Days' 
                        AND serviceStatus != 4";


    $yesterday = date('Y-m-d', strtotime('-1 day'));

    $updateQuery4 = "   UPDATE tblclientservices 
                        SET serviceStatus = 3
                        WHERE serviceStatus = 1 
                        AND Dateend = '$yesterday' 
                        AND serviceStatus != 4";

    try {
        $stmt3 = $con->prepare($updateQuery3);
        $stmt3->execute();
        $stmt4= $con->prepare($updateQuery4);
        $stmt4->execute();

    } catch (PDOException $e) {
        
    }
?>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Dashboard</h1>
            </div>
            <?php
                $clientsCount = getCount($con, 'tblclients', 'WHERE client_active = 1');
                $servicesCount = getCount($con, 'tblclientservices');
                $inProcessCount = getCount($con, 'tblclientservices', 'WHERE ServiceDone = 0');
                $expireSoonCount = getCount($con, 'tblclientservices', 'WHERE serviceStatus = 2');
                $cancelCount = getCount($con, 'tblclientservices', 'WHERE serviceStatus = 4');
                $domainsCount = getCount($con, 'tbldomeinclients');
                $activeCount = getCount($con, 'tbldomeinclients', 'WHERE Status = 1');
                $expireSoonDomainsCount = getCount($con, 'tbldomeinclients', 'WHERE Status = 2');
                $cancelDomainsCount = getCount($con, 'tbldomeinclients', 'WHERE Status = 5');
                $invoicesCount = getCount($con, 'tblinvoice');
                $deoCount = getCount($con, 'tblinvoice', 'WHERE Invoice_Status = 1');
                $paidCount = getCount($con, 'tblinvoice', 'WHERE Invoice_Status = 2');
                $cancelInvoicesCount = getCount($con, 'tblinvoice', 'WHERE Invoice_Status = 3');
                $ticketsCount = getCount($con, 'tblticket');
                $openTicketsCount = getCount($con, 'tblticket', 'WHERE ticketStatus = 1');
                $clientRespondCount = getCount($con, 'tblticket', 'WHERE ticketStatus = 2');
                $operatorTicketsCount = getCount($con, 'tblticket', 'WHERE ticketStatus = 3');
            ?>
            <div class="card-grid">
                <div class="card card-primary">
                    <h2 class="card-title">Clients</h2>
                    <p class="card-count">General Count: <?php echo $clientsCount; ?></p>
                </div>

                <div class="card card-secondary">
                    <h2 class="card-title">Services</h2>
                    <p class="card-count">General Count: <?php echo $servicesCount; ?></p>
                    <div class="card-subtitles">
                        <div class="subtitle">
                            <h3>In Process</h3>
                            <p><?php echo $inProcessCount; ?></p>
                        </div>
                        <div class="subtitle">
                            <h3>Expire Soon</h3>
                            <p><?php echo $expireSoonCount; ?></p>
                        </div>
                        <div class="subtitle">
                            <h3>Cancel</h3>
                            <p><?php echo $cancelCount; ?></p>
                        </div>
                    </div>
                </div>

                <div class="card card-primary">
                    <h2 class="card-title">Domains</h2>
                    <p class="card-count">General Count: <?php echo $domainsCount; ?></p>
                    <div class="card-subtitles">
                        <div class="subtitle">
                            <h3>Active</h3>
                            <p><?php echo $activeCount; ?></p>
                        </div>
                        <div class="subtitle">
                            <h3>Expire Soon</h3>
                            <p><?php echo $expireSoonDomainsCount; ?></p>
                        </div>
                        <div class="subtitle">
                            <h3>Cancel</h3>
                            <p><?php echo $cancelDomainsCount; ?></p>
                        </div>
                    </div>
                </div>

                <div class="card card-secondary">
                    <h2 class="card-title">Invoices</h2>
                    <p class="card-count">General Count: <?php echo $invoicesCount; ?></p>
                    <div class="card-subtitles">
                        <div class="subtitle">
                            <h3>Due</h3>
                            <p><?php echo $deoCount; ?></p>
                        </div>
                        <div class="subtitle">
                            <h3>Paid</h3>
                            <p><?php echo $paidCount; ?></p>
                        </div>
                        <div class="subtitle">
                            <h3>Cancel</h3>
                            <p><?php echo $cancelInvoicesCount; ?></p>
                        </div>
                    </div>
                </div>

                <div class="card card-primary">
                    <h2 class="card-title">Tickets</h2>
                    <p class="card-count">General Count: <?php echo $ticketsCount; ?></p>
                    <div class="card-subtitles">
                        <div class="subtitle">
                            <h3>Open</h3>
                            <p><?php echo $openTicketsCount; ?></p>
                        </div>
                        <div class="subtitle">
                            <h3>Client Respond</h3>
                            <p><?php echo $clientRespondCount; ?></p>
                        </div>
                        <div class="subtitle">
                            <h3>Operator</h3>
                            <p><?php echo $operatorTicketsCount; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-grids">
                <div class="card card-special">
                    <h2 class="card-title">Amount Invoices</h2>
                    <p class="card-count">
                        <?php
                        $sql = $con->prepare('SELECT SUM(TotalAmount + TotalTax) AS totalAmount FROM tblinvoice WHERE Invoice_Status < 3');
                        $sql->execute();
                        $result = $sql->fetch();
                        $formattedAmount = number_format($result['totalAmount'], 2);
                        echo '$' . $formattedAmount;
                        ?>
                    </p>
                </div>

                <div class="card card-special">
                    <h2 class="card-title">Payments</h2>
                    <p class="card-count">
                        <?php
                        $sql = $con->prepare('SELECT SUM(Payment_Amount) AS totalPayments FROM tblpayments 
                                            WHERE invoiceID IN (SELECT InvoiceID FROM tblinvoice WHERE Invoice_Status < 3)');
                        $sql->execute();
                        $result = $sql->fetch();
                        $formattedPayments = number_format($result['totalPayments'], 2);
                        echo '$' . $formattedPayments;
                        ?>
                    </p>
                </div>

                <div class="card card-special">
                    <h2 class="card-title">Expenses</h2>
                    <p class="card-count">
                        <?php
                        $sql = $con->prepare('SELECT SUM(Expensis_Amount) AS totalExpenses FROM tblexpensis');
                        $sql->execute();
                        $result = $sql->fetch();
                        $formattedExpenses = number_format($result['totalExpenses'], 2);
                        echo '$' . $formattedExpenses;
                        ?>
                    </p>
                </div>
            </div>

            <div class="table-container">
                <h2>Services</h2>
                <table class="services-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Service</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Done</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = $con->prepare('SELECT CONCAT(c.Client_FName, " ", c.Client_LName) AS FullName,
                                                s.Service_Name,
                                                FORMAT(cs.Price, 2) AS FormattedPrice,
                                                ss.Status,
                                                CASE
                                                    WHEN cs.ServiceDone = 0 THEN "In Progress"
                                                    WHEN cs.ServiceDone = 1 THEN "Completed"
                                                END AS ServiceStatus,
                                                cs.Date_service
                                                FROM tblclientservices cs
                                                INNER JOIN tblclients c ON cs.ClientID = c.ClientID
                                                INNER JOIN tblservices s ON cs.ServiceID = s.ServiceID
                                                INNER JOIN tblstatusservices ss ON cs.serviceStatus = ss.StatusSerID
                                                ORDER BY cs.ServiceDone ASC');

                        $sql->execute();
                        $services = $sql->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($services as $service) {
                            echo '<tr>';
                            echo '<td>' . $service['FullName'] . '</td>';
                            echo '<td>' . $service['Date_service'] . '</td>';
                            echo '<td>' . $service['Service_Name'] . '</td>';
                            echo '<td>' . '$' . $service['FormattedPrice'] . '</td>';
                            echo '<td>' . $service['Status'] . '</td>';
                            echo '<td>' . $service['ServiceStatus'] . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <?php
            $sql = $con->prepare('SELECT CONCAT(c.Client_FName, " ", c.Client_LName) AS Client,
                                    tt.TypeTicket AS Section,
                                    t.ticketSubject AS Subject,
                                    st.Status AS Status,
                                    DATE_FORMAT(MAX(dt.Date), "%d/%m/%Y (%H:%i:%s)") AS LastUpdate
                                    FROM tblticket t
                                    INNER JOIN tblclients c ON t.ClientID = c.ClientID
                                    INNER JOIN tbltypeoftickets tt ON t.ticketSection = tt.TypeTicketID
                                    INNER JOIN tblstatusticket st ON t.ticketStatus = st.StatusTicketID
                                    LEFT JOIN tbldeiteilticket dt ON t.ticketID = dt.TicketID
                                    GROUP BY t.ticketID
                                    ORDER BY MAX(dt.Date) DESC');
            $sql->execute();
            echo '<div class="table-container">';
            echo '<h2>Tickets</h2>';
            echo '<table class="tickets-table">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Client</th>';
            echo '<th>Section</th>';
            echo '<th>Subject</th>';
            echo '<th>Status</th>';
            echo '<th>Last Update</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                echo '<tr>';
                echo '<td>' . $row['Client'] . '</td>';
                echo '<td>' . $row['Section'] . '</td>';
                echo '<td>' . $row['Subject'] . '</td>';
                echo '<td>' . $row['Status'] . '</td>';
                echo '<td>' . $row['LastUpdate'] . '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';

            ?>

            <div class="container_div">
                <?php
                    $currentDate = date('Y-m-d');
                    $dateAfter45Days = date('Y-m-d', strtotime('+45 days'));
                    $sql = $con->prepare('SELECT CONCAT(dt.ServiceName, " (", dc.DomeinName, ")") AS Plan,
                                            CONCAT(c.Client_FName, " ", c.Client_LName) AS Client,
                                            CONCAT("$", FORMAT(dc.Price_Renew, 2)) AS Price,
                                            dc.RenewDate AS RenewDate
                                            FROM tbldomeinclients dc
                                            INNER JOIN tblclients c ON dc.Client = c.ClientID
                                            INNER JOIN tbldomaintype dt ON dc.ServiceType = dt.DomainTypeID
                                            WHERE dc.RenewDate BETWEEN :currentDate AND :dateAfter45Days
                                            AND dc.Status < 4
                                            ORDER BY dc.RenewDate DESC');
                    $sql->bindParam(':currentDate', $currentDate);
                    $sql->bindParam(':dateAfter45Days', $dateAfter45Days);
                    $sql->execute();


                    echo '<div class="left-div">';
                    echo '<h2>Domains End After 45 Days</h2>';
                    echo '<table class="domains-table">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Plan</th>';
                    echo '<th>Client</th>';
                    echo '<th>Price</th>';
                    echo '<th>Date Renew</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                        echo '<tr>';
                        echo '<td>' . $row['Plan'] . '</td>';
                        echo '<td>' . $row['Client'] . '</td>';
                        echo '<td>' . $row['Price'] . '</td>';
                        echo '<td>' . $row['RenewDate'] . '</td>';
                        echo '</tr>';
                    }

                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';

                ?>
                <div class="right-div">
                    <h2>To-Do List</h2>
                    <table class="todo-table">
                        <thead>
                            <tr>
                                <th>Done</th>
                                <th>Task</th>
                                <th>Date to Finish</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="container_expenis">
                <?php
                    $sql = $con->prepare('SELECT ExpensisDate, Discription, CONCAT("$", FORMAT(Expensis_Amount, 2)) AS Amount
                                            FROM tblexpensis
                                            ORDER BY ExpensisDate DESC');

                    $sql->execute();

                    echo '<div class="left-div">';
                    echo '<h2>Expenses</h2>';
                    echo '<table class="expenses-table">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Date</th>';
                    echo '<th>Description</th>';
                    echo '<th>Amount</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                        echo '<tr>';
                        echo '<td>' . $row['ExpensisDate'] . '</td>';
                        echo '<td>' . $row['Discription'] . '</td>';
                        echo '<td>' . $row['Amount'] . '</td>';
                        echo '</tr>';
                    }

                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';

                ?>
                <?php

                    $sql = $con->prepare('SELECT CONCAT(tblclients.Client_FName, " ", tblclients.Client_LName) AS Client,
                    tblpayment_method.methot AS Method,
                    CONCAT("$", FORMAT(tblpayments.Payment_Amount, 2)) AS Amount
                    FROM tblpayments
                    INNER JOIN tblclients ON tblpayments.ClientID = tblclients.ClientID
                    INNER JOIN tblpayment_method ON tblpayments.paymentMethod = tblpayment_method.paymentmethodD
                    ORDER BY tblpayments.Payment_Date DESC');

                    $sql->execute();

                    echo '<div class="right-div">';
                    echo '<h2>Payments</h2>';
                    echo '<table class="payments-table">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Client</th>';
                    echo '<th>Method</th>';
                    echo '<th>Amount</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . $row['Client'] . '</td>';
                    echo '<td>' . $row['Method'] . '</td>';
                    echo '<td>' . $row['Amount'] . '</td>';
                    echo '</tr>';
                    }

                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';

                ?>
            </div>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/dashboard.js"></script>
    <script src="js/sidebar.js"></script>
</body>