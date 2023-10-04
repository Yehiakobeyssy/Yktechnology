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
    <link rel="stylesheet" href="css/ManageCountry.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Countrys Available</h1>
            </div>
            <div class="search-container">
                <input type="text" id="searchBox" placeholder="Search for countries...">
            </div>
            <div id="addCountryForm">
                <form id="countryForm" method="post">
                    <label for="newCountryName">Country Name:</label>
                    <input type="text" id="newCountryName" name="newCountryName" required>
                    <label for="newCountryTVA">Country TVA:</label>
                    <input type="number" id="newCountryTVA" name="newCountryTVA" step="0.01" required>
                    <button type="submit" id="addCountryBtn" name="btnnewCountry">Add Country</button>
                </form>
                <?php
                    if(isset($_POST['btnnewCountry'])){
                        $CountryName = $_POST['newCountryName'];
                        $CountryTVA  = $_POST['newCountryTVA'];

                        $sql = $con->prepare("INSERT INTO tblcountrys (CountryName, CountryTVA) VALUES (?, ?)");
                        $sql->execute([$CountryName,$CountryTVA]);

                    }
                ?>
            </div>
            <div class="table_search">
                <table id="countryTable">
                    <thead>
                        <tr>
                            <th>Country ID</th>
                            <th>Country Name</th>
                            <th>Country TVA</th>
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
    <script src="js/ManageCountry.js"></script>
    <script src="js/sidebar.js"></script>
</body>