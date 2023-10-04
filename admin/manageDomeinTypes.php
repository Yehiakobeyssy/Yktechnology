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
<link rel="stylesheet" href="css/manageDomeinTypes.css">
<link rel="stylesheet" href="css/navbar.css">
<link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Domain Services Available</h1>
            </div>
            <div class="search-container">
                <input type="text" id="searchBox" placeholder="Search for domain services...">
            </div>
            <div id="addDomainServiceForm">
                <div id="addCountryForm">
                    <form id="domainServiceForm" method="post">
                        <label for="newServiceName">Service Name:</label>
                        <input type="text" id="newServiceName" name="newServiceName" required>
                        <button type="submit" id="addServiceBtn" name="btnnewService">Add Service</button>
                    </form>
                </div>
                <?php
                    if(isset($_POST['btnnewService'])){
                        $ServiceName = $_POST['newServiceName'];
                        $sql = $con->prepare("INSERT INTO tbldomaintype (ServiceName) VALUES (?)");
                        $sql->execute([$ServiceName]);
                    }
                ?>
            </div>
            <div class="table_search">
                <table id="domainServiceTable">
                    <thead>
                        <tr>
                            <th>Service ID</th>
                            <th>Service Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/ManageDomeinTypes.js"></script>
    <script src="js/sidebar.js"></script>
</body>
