<?php
session_start();

// Handle JSON input
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($_SESSION['freelancerProject']) || !is_array($_SESSION['freelancerProject'])) {
    echo json_encode(['status' => 'error', 'message' => 'Session not initialized']);
    exit;
}

if (!isset($data['freelancers']) || !is_array($data['freelancers'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid freelancers array']);
    exit;
}

// Normalize freelancer shares in session
foreach ($data['freelancers'] as $item) {
    $index = isset($item['index']) ? intval($item['index']) : null;
    $share = isset($item['share']) ? floatval($item['share']) : null;

    if ($index !== null && $share !== null && isset($_SESSION['freelancerProject'][$index])) {
        $_SESSION['freelancerProject'][$index]['share'] = $share;
    }
}

// (Optional) You could store reserve and management elsewhere in session
// $_SESSION['freelancerReserve'] = floatval($data['reserve'] ?? 0);
// $_SESSION['freelancerManagement'] = floatval($data['management'] ?? 0);

echo json_encode(['status' => 'success', 'message' => 'Freelancer shares updated']);
?>