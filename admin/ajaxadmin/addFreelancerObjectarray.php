<?php
session_start();

$freelancer = isset($_GET['freelancer']) ? (int)$_GET['freelancer'] : 0;

if ($freelancer > 0) {
    $itemarray = array(
        'id' => $freelancer,
        'Service'=>'',
        'share' =>'',
        'note' => '',
    );

    if (isset($_SESSION['freelancerProject'])) {
        $exists = false;

        // Check if service already exists
        foreach ($_SESSION['freelancerProject'] as $item) {
            if ($item['id'] == $freelancer) {
                $exists = true;
                break;
            }
        }

        // Only add if it doesn't exist
        if (!$exists) {
            $_SESSION['freelancerProject'][] = $itemarray;
        }

    } else {
        // First time creation
        $_SESSION['freelancerProject'][0] = $itemarray;
    }
}

// Optional: return a JSON response for frontend feedback
echo json_encode(['status' => 'done']);
?>
