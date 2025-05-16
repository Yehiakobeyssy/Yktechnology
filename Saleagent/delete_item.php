<?php
session_start();

if (isset($_POST['serviceID'])) {
    $serviceID = $_POST['serviceID'];

    // Initialize $_SESSION['ad-dlinvoice'] if it's not set
    if (!isset($_SESSION['ad-dlinvoice'])) {
        $_SESSION['ad-dlinvoice'] = array();
    }

    // Assuming $_SESSION['ad-dlinvoice'] contains the invoice data
    foreach ($_SESSION['ad-dlinvoice'] as $key => $item) {
        if ($item['ServiceID'] == $serviceID) {
            // Remove the item from $_SESSION['ad-dlinvoice']
            unset($_SESSION['ad-dlinvoice'][$key]);
            unset($_SESSION['AD_Service'][$key]);
            unset($_SESSION['AD_Domein'][$key]);


            echo 'success';
            exit;
        }
    }

    // If the item was not found, return an error message
    echo 'Item not found in the invoice.';
} else {
    // If 'serviceID' is not set in the POST data, return an error message
    echo 'Invalid request.';
}

?>