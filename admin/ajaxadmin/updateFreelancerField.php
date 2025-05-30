<?php
session_start();

if (isset($_POST['index']) && isset($_POST['field']) && isset($_POST['value'])) {
    $index = $_POST['index'];
    $field = $_POST['field'];
    $value = $_POST['value'];

    if (isset($_SESSION['freelancerProject'][$index])) {
        $_SESSION['freelancerProject'][$index][$field] = $value;
        echo json_encode(['status' => 'success', 'message' => 'Field updated']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Freelancer not found']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing data']);
}
?>
