<?php
session_start();

// التحقق من تسجيل الدخول
if(!isset($_COOKIE['staff'])){
    if(!isset($_SESSION['staff'])){
        header('location:index.php');
        exit();
    }
}

$staff_Id= (isset($_COOKIE['staff'])) ? $_COOKIE['staff'] : $_SESSION['staff'];

include '../settings/connect.php';
include '../common/function.php';
include '../common/head.php';

// جلب بيانات الموظف
$sql = $con->prepare('SELECT Fname, MidelName, LName, accepted, block FROM tblstaff WHERE staffID = ?');
$sql->execute(array($staff_Id));
$result = $sql->fetch(PDO::FETCH_ASSOC);

$accepted = $result['accepted'];
$block = $result['block'];
$staff_name = $result['Fname'].' '.$result['MidelName'].' '.$result['LName'];

if($block == 1 || $accepted == 0){
    header('location:index.php');
    exit();
}

$totalBalance = 0;
$newBalance = 0;

// استعلام العمليات
$query = "SELECT accountID, date_account, discription, depit, criedit
          FROM tblaccountstatment_staff
          WHERE staffID = ?";
$params = [$staff_Id];

// تحقق من فلتر البحث
if (isset($_POST['txtsearch'])) {
    $dateBegin = !empty($_POST['txtbegin']) ? $_POST['txtbegin'] : null;
    $dateEnd   = !empty($_POST['txtEnd']) ? $_POST['txtEnd'] : null;
    $limit     = !empty($_POST['txtno']) ? (int)$_POST['txtno'] : null;

    if ($dateBegin && $dateEnd) {
        $query .= " AND date_account BETWEEN ? AND ?";
        $params[] = $dateBegin;
        $params[] = $dateEnd;
    }

if (isset($limit) && $limit > 0) {
    $query .= " LIMIT " . $limit;
}
}

$query .= " ORDER BY accountID DESC";

// تنفيذ الاستعلام
$sql = $con->prepare($query);
$sql->execute($params);
$result = $sql->fetchAll(PDO::FETCH_ASSOC);

// حساب الرصيد الإجمالي
if (!empty($result) && is_array($result)) {
    foreach ($result as $row) {
        $depit = isset($row['depit']) ? floatval($row['depit']) : 0;
        $criedit = isset($row['criedit']) ? floatval($row['criedit']) : 0;
        $totalBalance += ($depit - $criedit);
    }
} else {
    $totalBalance = 0;
}

?>

<link rel="stylesheet" href="css/ManageAcountStatment.css">
</head>
<body>
<?php include 'include/headerstaff.php'; ?>
<main>
<?php include 'include/aside.php'; ?>

<div class="project_container">
    <div class="title">
        <h3>Account Statement</h3>
        <div class="controlbtns">
            <button class="btn btn-warning">Status Transfers</button>
            <button class="btn btn-primary">Order Money</button>
        </div>
    </div>

    <!-- نموذج البحث والرصيد الإجمالي -->
    <div class="statistic">
        <div class="createria">
            <form action="" method="post">
                <table>
                    <tr>
                        <td><label>Data Begin</label></td>
                        <td><input type="date" name="txtbegin"></td>
                    </tr>
                    <tr>
                        <td><label>Date End</label></td>
                        <td><input type="date" name="txtEnd"></td>
                    </tr>
                    
                </table>
                <button type="submit" class="btn btn-primary" name="txtsearch">Search</button>
            </form>
        </div>

        <div class="balance">
            <h4>Total Balance</h4>
            <h2><?php echo number_format($totalBalance, 2) . " $"; ?></h2>
        </div>
    </div>

    <!-- جدول العمليات -->
    <div class="data_fetch">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Depit</th>
                    <th>Creditor</th>
                    <th>New Balance</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $query = "SELECT accountID, date_account, discription, depit, criedit
                        FROM tblaccountstatment_staff
                        WHERE staffID = ?";
                $params = [$staff_Id];

                // تحقق من فلتر البحث
                if (isset($_POST['txtsearch'])) {
                    $dateBegin = !empty($_POST['txtbegin']) ? $_POST['txtbegin'] : null;
                    $dateEnd   = !empty($_POST['txtEnd']) ? $_POST['txtEnd'] : null;
                    $limit     = !empty($_POST['txtno']) ? (int)$_POST['txtno'] : null;

                    if ($dateBegin && $dateEnd) {
                        $query .= " AND date_account BETWEEN ? AND ?";
                        $params[] = $dateBegin;
                        $params[] = $dateEnd;
                    }

                    if ($limit) {
                        $query .= " LIMIT " . $limit;
                    }
                }

                $query .= " ORDER BY accountID DESC";

                // تنفيذ الاستعلام
                $sql = $con->prepare($query);
                $sql->execute($params);
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($result) && is_array($result)) {

                    // عكس الصفوف للحساب من الأسفل للأعلى
                    $reversedResult = array_reverse($result);

                    // حساب الرصيد من الأسفل للأعلى
                    $runningBalance = 0;
                    $balances = [];

                    foreach ($reversedResult as $row) {
                        $depit = isset($row['depit']) ? floatval($row['depit']) : 0;
                        $criedit = isset($row['criedit']) ? floatval($row['criedit']) : 0;
                        $amount = $depit - $criedit;

                        $runningBalance += $amount;
                        $balances[] = $runningBalance; // نخزن الرصيد لكل صف
                    }

                    // الآن نعرض الصفوف بالترتيب التنازلي مع الرصيد الصحيح
                    $totalRows = count($result);
                    for ($i = 0; $i < $totalRows; $i++) {
                        $row = $result[$i]; // نستخدم الصفوف الأصلية بالترتيب التنازلي
                        $depit = isset($row['depit']) ? floatval($row['depit']) : 0;
                        $criedit = isset($row['criedit']) ? floatval($row['criedit']) : 0;
                        $formattedDate = isset($row['date_account']) ? date("d/m/Y", strtotime($row['date_account'])) : '';
                        $description = isset($row['discription']) ? htmlspecialchars($row['discription']) : '';

                        // الرقم التنازلي
                        $number = $totalRows - $i;

                        // الرصيد الصحيح من الأسفل للأعلى
                        $balance = $balances[$totalRows - $i - 1];

                        echo "<tr>
                            <td>{$number}</td>
                            <td>{$formattedDate}</td>
                            <td>{$description}</td>
                            <td>".number_format($depit, 2)."</td>
                            <td>".number_format($criedit, 2)."</td>
                            <td>".number_format($balance, 2)."</td>
                        </tr>";
                    }

                } else {
                    echo "<tr><td colspan='6' style='text-align:center'>No data found</td></tr>";
                }

            ?>
            </tbody>
        </table>
    </div>
</div>

</main>
<?php include '../common/jslinks.php'; ?>
<script src="js/ManageAcountStatment.js"></script>
</body>
