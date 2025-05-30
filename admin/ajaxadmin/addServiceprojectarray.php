<?php
session_start();

$serviceID = isset($_GET['serID']) ? (int)$_GET['serID'] : 0;

if ($serviceID > 0) {
    $itemarray = array(
        'id' => $serviceID,
        'note' => '',
    );

    if (isset($_SESSION['ServiceProject'])) {
        $exists = false;

        // Check if service already exists
        foreach ($_SESSION['ServiceProject'] as $item) {
            if ($item['id'] == $serviceID) {
                $exists = true;
                break;
            }
        }

        // Only add if it doesn't exist
        if (!$exists) {
            $_SESSION['ServiceProject'][] = $itemarray;
        }

    } else {
        // First time creation
        $_SESSION['ServiceProject'][0] = $itemarray;
    }
}

// Optional: return a JSON response for frontend feedback
echo json_encode(['status' => 'done']);
?>
 