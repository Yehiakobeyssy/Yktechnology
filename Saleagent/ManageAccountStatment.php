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
    $promocode= $result['PromoCode'];

    if($isActive == 0){
        setcookie("AgentID","",time()-3600);
        unset($_SESSION['AgentID']);
        echo '<script> location.href="index.php" </script>';
    }
?>
    <link rel="stylesheet" href="css/ManageAccountStatment.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head> 
<body>
    <?php include 'include/navbar.php' ?>
    <div class="containerbody">
        <?php include 'include/sidebar.php' ?>
        <div class="includebody">
            <div class="title">
                <h3>Accout Statment</h3>
            </div>
            <div class="formsearch">
            <form method="POST" action="">
                <label for="beginDate">Begin Date:</label>
                <input type="date" name="beginDate" required><br>
                <label for="endDate">End Date:</label>
                <input type="date" name="endDate" required><br>
                <div class="btncontrol">
                    <input type="submit" value="Search" name="btnsearch">
                </div>
            </form>
            </div>
            <?php
            ?>
            <div class="result">
                <table>
                    <thead>
                        <th>Date</th>
                        <th>Discription</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance</th>
                    </thead>
                    <tbody>
                    <?php
                        $sql = $con->prepare('SELECT Account_Date, Discription, Depit, Crieted 
                                                FROM tblaccountstatment_saleperson
                                                WHERE SaleManID = ? AND Account_Date BETWEEN ? AND ?');

                        if (isset($_POST['btnsearch'])) {
                        $begindate = $_POST['beginDate'];
                        $enddate = $_POST['endDate'];
                        } else {
                        $begindate = '2000-10-10'; // Corrected date format
                        $enddate = date('Y-m-d'); // Corrected date format
                        }

                        $sql->execute(array($agentId, $begindate, $enddate));

                        $rows = $sql->fetchAll();

                        // Calculate old balance for the period before $_POST['beginDate']
                        $stat = $con->prepare('SELECT SUM(Depit - Crieted) AS oldBalance 
                                            FROM tblaccountstatment_saleperson
                                            WHERE SaleManID = ? AND Account_Date < ?');
                        $stat->execute(array($agentId, $begindate));
                        $cech = $stat->rowCount();

                        if ($cech == 1) {
                        $result_old = $stat->fetch();
                        $oldBalance = $result_old['oldBalance']; 
                        } else {
                        $oldBalance = 0;
                        }

                        if($oldBalance !=0){
                            echo '
                                <td style="text-align:right" colspan="4">Old Balance </td>
                                <td style="font-weight:bold">'.number_format($oldBalance, 2) . ' $</td>
                            ';
                        }
                        $newBalance = 0 + $oldBalance;
                        $sumDepit  = 0;
                        $sumCritet = 0;
                        foreach ($rows as $row) {
                            $thisbalance = $row['Depit'] - $row['Crieted'];
                            $newBalance += $thisbalance;
                            $sumDepit  +=$row['Depit'];
                            $sumCritet +=$row['Crieted'];
                            echo '
                                <tr>
                                    <td>' . $row['Account_Date'] . '</td>
                                    <td>' . $row['Discription'] . '</td>
                                    <td>' . number_format($row['Depit'], 2) . ' $</td>
                                    <td>' . number_format($row['Crieted'], 2) . ' $</td>
                                    <td style="font-weight:bold">' . number_format($newBalance, 2) . ' $</td>
                                </tr>
                            ';
                        }
                        echo '
                            <td style="text-align:right" colspan="2">New Balance </td>
                            <td style="font-weight:bold">'.number_format($sumDepit, 2) . ' $</td>
                            <td style="font-weight:bold">'.number_format($sumCritet, 2) . ' $</td>
                            <td style="font-weight:bold">'.number_format($newBalance, 2) . ' $</td>
                        '

                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/ManageAccountStatment.js"></script>
    <script src="js/sidebar.js"></script>
</body>