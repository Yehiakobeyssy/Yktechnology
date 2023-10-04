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
?>
    <link rel="stylesheet" href="css/ManageExpensis.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Expensis</h1>
                <a href="ManageExpensis.php?do=add">New Expensis</a>
            </div>
            <?php
                $do=(isset($_GET['do']))?$_GET['do']:'manage';
                
                if($do=='manage'){?>
                    <div id="manageExpeniss">
                        <h2>Manage Expenses</h2>
                        <form action="" method="post">
                            <label for="filterDateBegin">Date Begin:</label>
                            <input type="date" id="filterDateBegin" name="filterDateBegin">
                            
                            <label for="filterDateEnd">Date End:</label>
                            <input type="date" id="filterDateEnd" name="filterDateEnd">
                            
                            <label for="filterDescription">Filter by Description:</label>
                            <input type="text" id="filterDescription" name="filterDescription">

                            <label for="filterType">Filter by Type:</label>
                            <select id="filterType" name="filterType">
                                <option value="">All</option>
                                <?php
                                $sql = $con->prepare('SELECT TypeexpensisID, Type_Expensis FROM tbltypeexpensis');
                                $sql->execute();
                                $types = $sql->fetchAll();
                                foreach ($types as $type) {
                                    echo '<option value="' . $type['TypeexpensisID'] . '">' . $type['Type_Expensis'] . '</option>';
                                }
                                ?>
                            </select>
                            <button type="submit" name="btnFilter">Filter</button>
                        </form>

                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Expense Type</th>
                                    <th>Discription</th>
                                    <th>Total Amount</th>
                                    <th>Attachment</th>
                                    <th>control</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $filterDateBegin = '';
                                    $filterDateEnd = '';
                                    $filterType = '';
                                    $filterDescription = '%' . ''. '%' ;

                                    // Check if the filter form has been submitted
                                    if (isset($_POST['btnFilter'])) {
                                        // Set filter values if form has been submitted
                                        $filterDateBegin = isset($_POST['filterDateBegin']) ? $_POST['filterDateBegin'] : '';
                                        $filterDateEnd = isset($_POST['filterDateEnd']) ? $_POST['filterDateEnd'] : '';
                                        $filterType = isset($_POST['filterType']) ? $_POST['filterType'] : '';
                                        $filterDescription = isset($_POST['filterDescription']) ? '%' . $_POST['filterDescription'] . '%' : '';
                                    }

                                $sql = $con->prepare('SELECT ExpenisisID, ExpensisDate, Type_Expensis, Discription, Expensis_Amount, attached FROM tblexpensis
                                                    INNER JOIN tbltypeexpensis ON tblexpensis.ExpenisType = tbltypeexpensis.TypeexpensisID
                                                    WHERE ((:filterDateBegin = "" OR ExpensisDate >= :filterDateBegin)
                                                    AND (:filterDateEnd = "" OR ExpensisDate <= :filterDateEnd))
                                                    AND (:filterType = "" OR ExpenisType = :filterType)
                                                    AND (Discription LIKE :filterDescription)');
                                $sql->bindParam(':filterDateBegin', $filterDateBegin);
                                $sql->bindParam(':filterDateEnd', $filterDateEnd);
                                $sql->bindParam(':filterType', $filterType);
                                $sql->bindParam(':filterDescription', $filterDescription);
                                $sql->execute();
                                $expenses = $sql->fetchAll();


                                $totalExpenses = 0; // Initialize total expenses

                                foreach ($expenses as $expense) {
                                    echo '<tr>';
                                    echo '<td>' . $expense['ExpensisDate'] . '</td>';
                                    echo '<td>' . $expense['Type_Expensis'] . '</td>';
                                    echo '<td>' . $expense['Discription'] . '</td>'; // Added description field
                                    echo '<td>' . number_format($expense['Expensis_Amount'], 2, '.', '') . ' $</td>';
                                    echo '<td><a href="../Documents/' . $expense['attached'] . '" download><i class="fa-solid fa-paperclip"></i></a></td>';
                                    echo '<td><a href="?do=delete&id=' . $expense['ExpenisisID'] . '" class="delete-link"><i class="fa-solid fa-trash"></i></a></td>';
                                    echo '</tr>';
                                    $totalExpenses += $expense['Expensis_Amount'];
                                }
                                ?>
                            </tbody>
                        </table>
                        <p>Total Expenses: <?php echo number_format($totalExpenses,2,'.','') . ' $'; ?></p>
                    </div>

                <?php
                }elseif($do=='add'){?>
                    <div id="addExpeniss">
                        <h2>Add Expenses</h2>
                        <form action="" method="post" enctype="multipart/form-data">
                            <label for="ExpensisDate">Expense Date:</label>
                            <input type="date" id="ExpensisDate" name="ExpensisDate" required><br><br>
                            <label for="ExpenisType">Expense Type:</label>
                            <select id="ExpenisType" name="ExpenisType" required>
                                <option value="">[Select one]</option>
                                <?php
                                    $sql=$con->prepare('SELECT TypeexpensisID,Type_Expensis FROM  tbltypeexpensis ');
                                    $sql->execute();
                                    $types = $sql->fetchAll();
                                    foreach($types as $type){
                                        echo '<option value="'.$type['TypeexpensisID'].'">'.$type['Type_Expensis'].'</option>';
                                    }
                                ?>
                            </select><br><br>
                            <label for="Discription">Description:</label>
                            <input type="text" id="Discription" name="Discription"><br><br>
                            <label for="Expensis_Amount">Expense Amount:</label>
                            <input type="number" id="Expensis_Amount" name="Expensis_Amount" step="0.01" required><br><br>
                            <label for="Expensis_Note">Expense Note:</label>
                            <textarea id="Expensis_Note" name="Expensis_Note"></textarea><br><br>
                            <label for="attached">Attachment:</label>
                            <input type="file" id="attached" name="attached"><br><br>
                            <button type="submit" name="btnaddexpenis">Add Expense</button>
                        </form>
                        <?php
                            if(isset($_POST['btnaddexpenis'])){
                                $temp=explode(".",$_FILES['attached']['name']);
                                $newfilename=round(microtime(true)).'.'.end($temp);
                                move_uploaded_file($_FILES['attached']['tmp_name'],'../Documents/'.$newfilename);

                                $ExpensisDate       =$_POST['ExpensisDate'];
                                $ExpenisType        =$_POST['ExpenisType'];
                                $Discription        =$_POST['Discription'];
                                $Expensis_Amount    =$_POST['Expensis_Amount'];
                                $Expensis_Note      =$_POST['Expensis_Note'];
                                $attached           =$newfilename;

                                $sql=$con->prepare('INSERT INTO tblexpensis (ExpensisDate,ExpenisType,Discription,Expensis_Amount,Expensis_Note,attached)
                                                    VALUES (:ExpensisDate,:ExpenisType,:Discription,:Expensis_Amount,:Expensis_Note,:attached)');
                                $sql->execute(array(
                                    'ExpensisDate'      =>$ExpensisDate,
                                    'ExpenisType'       =>$ExpenisType,
                                    'Discription'       =>$Discription,
                                    'Expensis_Amount'   =>$Expensis_Amount,
                                    'Expensis_Note'     =>$Expensis_Note,
                                    'attached'          =>$attached
                                ));

                                echo '
                                <div class="alert alert-secondary" role="alert">
                                    The expensis invoice add
                                </div>
                                ';

                            }
                        ?>
                    </div>
                <?php
                }elseif($do=='delete'){
                    if (isset($_GET['id'])) {
                        $expenseID = $_GET['id'];
                        $sql = $con->prepare('DELETE FROM tblexpensis WHERE ExpenisisID  = ?');
                        $sql->execute(array($expenseID));
                        echo '<script> location.href="ManageExpensis.php" </script>';
                        exit();
                    } else {
                        echo '<script> location.href="ManageExpensis.php" </script>';
                    }
                }else{
                    header('location:index.php');
                }
            ?>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/ManageExpensis.js"></script>
    <script src="js/sidebar.js"></script>
</body>