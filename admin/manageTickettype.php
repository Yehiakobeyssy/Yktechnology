<?php
session_start();

if(!isset($_COOKIE['useradmin'])){
    if(!isset($_SESSION['useradmin'])){
        header('location:index.php');
    }
}
$adminId= (isset($_COOKIE['useradmin'])) ? $_COOKIE['useradmin'] : $_SESSION['useradmin'];

include '../settings/connect.php';
include '../common/function.php';
include '../common/head.php';

$sql = $con->prepare('SELECT admin_active,admin_FName,admin_LName FROM  tbladmin WHERE admin_ID=?');
$sql->execute(array($adminId));
$result = $sql->fetch();
$isActive = $result['admin_active'];
$firstname = $result['admin_FName'];
$lastName = $result['admin_LName'];
$full_name = $firstname . ' ' . $lastName;

if($isActive == 0){
    setcookie("useradmin", "", time() - 3600);
    unset($_SESSION['useradmin']);
    echo '<script> location.href="index.php" </script>';
}
?>
<link rel="stylesheet" href="css/manageTickettype.css">
<link rel="stylesheet" href="css/navbar.css">
<link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Ticket Types Available</h1>
            </div>
            <div class="search-container">
                <input type="text" id="searchBox" placeholder="Search for ticket types...">
            </div>
            <div id="addTicketTypeForm">
                <div id="addTicketTypeForm">
                    <form id="ticketTypeForm" method="post">
                        <label for="newTypeTicketName">Type Ticket Name:</label>
                        <input type="text" id="newTypeTicketName" name="newTypeTicketName" required>
                        <button type="submit" id="addTypeTicketBtn" name="btnnewTypeTicket">Add Type Ticket</button>
                    </form>
                </div>
                <?php
                    if(isset($_POST['btnnewTypeTicket'])){
                        $TypeTicketName = $_POST['newTypeTicketName'];
                        $sql = $con->prepare("INSERT INTO tbltypeoftickets (TypeTicket) VALUES (?)");
                        $sql->execute([$TypeTicketName]);
                    }
                ?>
            </div>
            <div class="table_search">
                <table id="ticketTypeTable">
                    <thead>
                        <tr>
                            <th>Type Ticket ID</th>
                            <th>Type Ticket Name</th>
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
    <script src="js/ManageticketType.js"></script>
    <script src="js/sidebar.js"></script>
</body>
