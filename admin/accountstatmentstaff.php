<?php
session_start();

if (!isset($_COOKIE['useradmin']) && !isset($_SESSION['useradmin'])) {
    header('location:index.php');
    exit;
}

$adminId = isset($_COOKIE['useradmin']) ? $_COOKIE['useradmin'] : $_SESSION['useradmin'];

include '../settings/connect.php';
include '../common/function.php';
include '../common/head.php';

// التحقق من حالة الأدمن
$sql = $con->prepare('SELECT admin_active, admin_FName, admin_LName FROM tbladmin WHERE admin_ID = ?');
$sql->execute([$adminId]);
$result = $sql->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    header('location:index.php');
    exit;
}

$isActive  = (int)$result['admin_active'];
$firstname = $result['admin_FName'];
$lastName  = $result['admin_LName'];
$full_name = trim($firstname . ' ' . $lastName);

if ($isActive === 0) {
    setcookie("useradmin", "", time() - 3600, "/");
    unset($_SESSION['useradmin']);
    echo '<script> location.href="index.php" </script>';
    exit;
}

/**
 * الفلترة:
 * نستخدم GET لدعم الروابط المباشرة مثل ?staff=3&dateBegin=2025-08-01&dateEnd=2025-08-24
 * مع قبول POST أيضًا (لو حابب تبقي على نفس الفورم مستقبلاً).
 */
$input = array_merge($_GET, $_POST);

$dateBegin = isset($input['txtBegin']) ? trim($input['txtBegin']) : (isset($input['dateBegin']) ? trim($input['dateBegin']) : '');
$dateEnd   = isset($input['txtEnd'])   ? trim($input['txtEnd'])   : (isset($input['dateEnd'])   ? trim($input['dateEnd'])   : '');
$staff_Id  = isset($input['txtStaff']) ? trim($input['txtStaff']) : (isset($input['staff'])     ? trim($input['staff'])     : '');

$dateBegin = $dateBegin !== '' ? $dateBegin : null;
$dateEnd   = $dateEnd   !== '' ? $dateEnd   : null;
$staff_Id  = $staff_Id  !== '' ? $staff_Id  : null;

// للحسابات
$oldBalance       = 0.0; // قبل بداية التاريخ
$balanceDuration  = 0.0; // داخل الفترة
$totalBalance     = 0.0; // = old + duration

// الاستعلام الأساسي
$listSql = "
    SELECT 
        a.accountID, a.date_account, a.staffID, a.discription, a.depit, a.criedit,
        s.Fname, s.MidelName, s.LName
    FROM tblaccountstatment_staff a
    JOIN tblstaff s ON a.staffID = s.staffID
    WHERE 1=1
";
$listParams = [];

// فلترة الموظف (إن وجد)
if (!empty($staff_Id)) {
    $listSql     .= " AND a.staffID = ? ";
    $listParams[] = $staff_Id;
}

// فلترة التاريخ إن وُجدت القيمتان
$hasDateFilter = (!empty($dateBegin) && !empty($dateEnd));

if ($hasDateFilter) {
    $listSql     .= " AND a.date_account BETWEEN ? AND ? ";
    $listParams[] = $dateBegin;
    $listParams[] = $dateEnd;

    // حساب Old Balance: كل ما قبل بداية التاريخ لنفس الموظف (إن محدد)
    $oldSql = "SELECT SUM(depit - criedit) AS oldBalance
               FROM tblaccountstatment_staff
               WHERE 1=1 ";
    $oldParams = [];

    if (!empty($staff_Id)) {
        $oldSql     .= " AND staffID = ? ";
        $oldParams[] = $staff_Id;
    }

    $oldSql     .= " AND date_account < ? ";
    $oldParams[] = $dateBegin;

    $oldStmt = $con->prepare($oldSql);
    $oldStmt->execute($oldParams);
    $oldBalance = (float)$oldStmt->fetchColumn();
}

// ترتيب السجلات من الأحدث للأقدم في العرض
$listSql .= " ORDER BY a.accountID DESC";

$listStmt = $con->prepare($listSql);
$listStmt->execute($listParams);
$rows = $listStmt->fetchAll(PDO::FETCH_ASSOC);

// حساب Balance Duration (مجموع الفترة/أو كل البيانات إذا لا يوجد فلترة تاريخ)
if (!empty($rows)) {
    foreach ($rows as $r) {
        $balanceDuration += (float)$r['depit'] - (float)$r['criedit'];
    }
} else {
    // لو لا يوجد نتائج في الفترة، يظل duration = 0
    // وسنعرض صف Old Balance إن وُجد
    $balanceDuration = 0.0;
}

// Total = Old + Duration
$totalBalance = $oldBalance + $balanceDuration;

// لجلب قائمة الموظفين للقائمة المنسدلة
$staffStmt = $con->prepare('SELECT staffID, Fname, MidelName, LName FROM tblstaff WHERE accepted = 1 AND block = 0 ORDER BY Fname');
$staffStmt->execute();
$staffList = $staffStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="css/accountstatmentstaff.css">
<link rel="stylesheet" href="css/navbar.css">
<link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
<?php include 'include/navbar.php'?>
<div class="mainform">
    <?php include 'include/sidebar.php'?>

    <div class="contain">
        <div class="title">
            <h4>Account Statment Staff</h4>
            <div class="btncontrols_tilte">
                <button class="btn btn-secondary addstatment">Add Statment</button>
            </div>
        </div>

        <div class="statistic">
            <!-- Filters -->
            <div class="crediria">
                <form method="get" action="">
                    <table>
                        <tr>
                            <th>Date Begin :</th>
                            <td>
                                <input type="date" name="dateBegin" value="<?php echo htmlspecialchars($dateBegin ?? ''); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>Date End :</th>
                            <td>
                                <input type="date" name="dateEnd" value="<?php echo htmlspecialchars($dateEnd ?? ''); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>Staff Name</th>
                            <td>
                                <select name="staff">
                                    <option value="">Select Staff</option>
                                    <?php foreach ($staffList as $st): 
                                        $id   = (string)$st['staffID'];
                                        $text = trim($st['Fname'].' '.$st['MidelName'].' '.$st['LName']);
                                        $sel  = (!empty($staff_Id) && (string)$staff_Id === $id) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo htmlspecialchars($id); ?>" <?php echo $sel; ?>>
                                            <?php echo htmlspecialchars($text); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <div class="filter-buttons" style="text-align:right; margin-top:10px;">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="?" class="btn btn-secondary" style="margin-left:8px;">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Summary -->
            <div class="result">
                <table>
                    <tr>
                        <th>Old Balance</th>
                        <th><h4><?php echo number_format($oldBalance, 2); ?> $</h4></th>
                    </tr>
                    <tr>
                        <th>Balance Duration</th>
                        <th><h4><?php echo number_format($balanceDuration, 2); ?> $</h4></th>
                    </tr>
                    <tr>
                        <th>Total Balance</th>
                        <th><h4><?php echo number_format($totalBalance, 2); ?> $</h4></th>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Table -->
        <div class="result_table">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Staff Name</th>
                        <th>Discription</th>
                        <th>Depit</th>
                        <th>Criedtid</th>
                        <th>New Balance</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // سنعرض Old Balance كسطر أول إذا كان != 0
                $printedAny = false;
                if ($oldBalance != 0) {
                    echo '<tr style="font-weight:bold;background:#f7f7f7;">
                            <td colspan="6" style="text-align:right">Old Balance</td>
                            <td>'.number_format($oldBalance, 2).'</td>
                          </tr>';
                    $printedAny = true;
                }

                if (!empty($rows)) {
                    // لحساب الرصيد التراكمي من الأسفل للأعلى
                    $running = $oldBalance;
                    $counter = 1;

                    // نريد حساب New Balance لكل صف بترتيب تصاعدي، ثم نعرض تنازليًا
                    $ascending = array_reverse($rows); // أقدم -> أحدث
                    $runningPerRow = [];

                    foreach ($ascending as $r) {
                        $running += (float)$r['depit'] - (float)$r['criedit'];
                        $runningPerRow[] = $running;
                    }

                    // الآن نطابق runningPerRow مع العرض التنازلي
                    // العرض سيكون كما في $rows (أحدث -> أقدم)
                    $total = count($rows);
                    foreach ($rows as $i => $r) {
                        $depit     = (float)$r['depit'];
                        $criedit   = (float)$r['criedit'];
                        $fullName  = trim($r['Fname'].' '.$r['MidelName'].' '.$r['LName']);
                        $dateNice  = $r['date_account'] ? date("d/m/Y", strtotime($r['date_account'])) : '';
                        // الـ running المناسب للصف i في العرض التنازلي:
                        $balanceForRow = $runningPerRow[$total - $i - 1];

                        echo '<tr>
                                <td>'.($counter++).'</td>
                                <td>'.htmlspecialchars($dateNice).'</td>
                                <td>'.htmlspecialchars($fullName).'</td>
                                <td>'.htmlspecialchars($r['discription']).'</td>
                                <td>'.number_format($depit, 2).'</td>
                                <td>'.number_format($criedit, 2).'</td>
                                <td>'.number_format($balanceForRow, 2).'</td>
                              </tr>';
                        $printedAny = true;
                    }
                }

                if (!$printedAny) {
                    // لا توجد بيانات ولا Old Balance
                    echo '<tr><td colspan="7" style="text-align:center">No data found</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="popupadd">
    <div class="container_popup">
        <div class="close_popup">+</div>
        <h3>Add Statement</h3>
        <form action="" method="post" class="form_popup">
            <div class="form-group">
                <label for="txtstaff">Staff Name</label>
                <select name="txtstaff" id="txtstaff" required>
                    <option value="">Select Staff Name</option>
                    <?php foreach ($staffList as $st): 
                        $id   = (string)$st['staffID'];
                        $text = trim($st['Fname'].' '.$st['MidelName'].' '.$st['LName']);
                        $sel  = (!empty($staff_Id) && (string)$staff_Id === $id) ? 'selected' : '';
                    ?>
                        <option value="<?php echo htmlspecialchars($id); ?>" <?php echo $sel; ?>>
                            <?php echo htmlspecialchars($text); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="txtDiscription">Discription</label>
                <input type="text" name="txtDiscription" id="txtDiscription" placeholder="Enter description">
            </div>

            <div class="form-group radio-group">
                <span>Kind:</span>
                <label><input type="radio" name="kind" value="work"> Work</label>
                <label><input type="radio" name="kind" value="withdraw"> Withdraw</label>
            </div>

            <div class="form-group">
                <label for="Amount">Amount</label>
                <input type="number" name="Amount" id="Amount" step="0.01" placeholder="0.00" required>
            </div>

            <div class="ctlbtn">
                <button type="submit" class="btn btn-success" name="btnadd">Save</button>
            </div>
        </form>
        <?php
            if(isset($_POST['btnadd'])){
                $staffId      = $_POST['txtstaff'] ?? null;
                $description  = $_POST['txtDiscription'] ?? '';
                $amount       = floatval($_POST['Amount'] ?? 0);
                $kind         = $_POST['kind'] ?? '';

                if(!$staffId || !$kind || $amount <= 0){
                    echo "Please fill all required fields.";
                } else {
                    // تحديد depit وcriedit حسب النوع
                    if($kind === 'work'){
                        $depit   = $amount;
                        $criedit = 0;
                    } elseif($kind === 'withdraw'){
                        $depit   = 0;
                        $criedit = $amount;
                    } else {
                        $depit   = 0;
                        $criedit = 0;
                    }

                    // تنفيذ الإضافة
                    $stmt = $con->prepare("
                        INSERT INTO tblaccountstatment_staff 
                        (date_account,staffID, discription, depit, criedit) 
                        VALUES (NOW(), ?, ?, ?, ?)
                    ");
                    $stmt->execute([$staffId, $description, $depit, $criedit]);

                    echo "Statement added successfully!";
                }
            }
        ?>

    </div>
</div>

<?php include '../common/jslinks.php'?>
<script src="js/accountstatmentstaff.js"></script>
<script src="js/sidebar.js"></script>

</body>
</html>
 