<?php
session_start();

if (!isset($_COOKIE['useradmin'])) {
    if (!isset($_SESSION['useradmin'])) {
        header('location:index.php');
    }
}
$adminId = (isset($_COOKIE['useradmin'])) ? $_COOKIE['useradmin'] : $_SESSION['useradmin'];

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

if ($isActive == 0) {
    setcookie("useradmin", "", time() - 3600);
    unset($_SESSION['useradmin']);
    echo '<script> location.href="index.php" </script>';
}

$do = (isset($_GET['do'])) ? $_GET['do'] : '';
?>
<link rel="stylesheet" href="css/makeainvoice.css">
<link rel="stylesheet" href="css/navbar.css">
<link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Make the Invoice</h1>
            </div>
            <div class="form-group">
                <label for="client-select">Select Client:</label>
                <select id="client-select" name="client">
                    <option value="">[SELECT ONE]</option>
                    <?php
                    $sql = $con->prepare('SELECT ClientID, Client_FName, Client_LName FROM tblclients');
                    $sql->execute();
                    $clients = $sql->fetchAll();
                    foreach ($clients as $client) {
                        $fullName = $client['Client_FName'] . ' ' . $client['Client_LName'];
                        echo '<option value="' . $client['ClientID'] . '">' . $fullName . '</option>';
                    }
                    ?>
                </select>
            </div>
            <?php
            if ($do == 'ser') {
                if (isset($_SESSION['AD_Service'])) {
                    // Loop through each service in $_SESSION['AD_Service']
                    foreach ($_SESSION['AD_Service'] as $serviceData) {
                        // Check if the service is already in the invoice
                        $isAlreadyAdded = false;

                        foreach ($_SESSION['ad-dlinvoice'] as $invoiceData) {
                            if ($invoiceData['ServiceID'] == $serviceData['service']) {
                                $isAlreadyAdded = true;
                                break;
                            }
                        }

                        // If it's not already added, add it to the invoice
                        if (!$isAlreadyAdded) {
                            $invoiceData = array(
                                'ServiceID' => $serviceData['service'],
                                'Description' => $serviceData['title'],
                                'Price' => $serviceData['price']
                            );

                            $_SESSION['ad-dlinvoice'][] = $invoiceData;
                        }
                    }
                }
            } elseif ($do == 'domein') {
                if (isset($_SESSION['AD_Domein'])) {
                    // Loop through each domain in $_SESSION['AD_Domein']
                    foreach ($_SESSION['AD_Domein'] as $domainData) {
                        // Check if the domain is already in the invoice
                        $isAlreadyAdded = false;

                        foreach ($_SESSION['ad-dlinvoice'] as $invoiceData) {
                            if ($invoiceData['ServiceID'] == $domainData['service']) {
                                $isAlreadyAdded = true;
                                break;
                            }
                        }

                        // If it's not already added, add it to the invoice
                        if (!$isAlreadyAdded) {
                            $invoiceData = array(
                                'ServiceID' => $domainData['service'],
                                'Description' => $domainData['domainName'],
                                'Price' => $domainData['renewalPrice']
                            );

                            $_SESSION['ad-dlinvoice'][] = $invoiceData;
                        }
                    }
                }
            } else {
                echo '<script> location.href="index.php" </script>';
            } ?>
            <?php
            // Assuming $_SESSION['ad-dlinvoice'] contains the desired data structure
            if (isset($_SESSION['ad-dlinvoice']) && !empty($_SESSION['ad-dlinvoice'])) {
                echo '<table>';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Service ID</th>';
                echo '<th>Description</th>';
                echo '<th>Price</th>';
                echo '<th>Action</th>'; // This column is for delete action
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                foreach ($_SESSION['ad-dlinvoice'] as $item) {
                    echo '<tr>';
                    echo '<td>' . $item['ServiceID'] . '</td>';
                    echo '<td>' . $item['Description'] . '</td>';
                    echo '<td>' . $item['Price'] . '</td>';
                    echo '<td><button onclick="deleteItem(' . $item['ServiceID'] . ')" class="delete-button">Delete</button></td>';
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
            } else {
                echo 'No items in the invoice.';
            }
            ?>
            <?php
                if (isset($_SESSION['AD_Domein'])) {
                    print_r($_SESSION['AD_Domein']);
                }
                if (isset($_SESSION['AD_Service'])) {
                    print_r($_SESSION['AD_Service']);
                }
            ?>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/makeainvoice.js"></script>
    <script src="js/delete_item.js"></script>
    <script src="js/sidebar.js"></script>
</body>
