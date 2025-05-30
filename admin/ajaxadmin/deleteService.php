<?php
session_start();

if (isset($_POST['index'])) {
    $index = (int)$_POST['index'];

    if (isset($_SESSION['ServiceProject'][$index])) {
        unset($_SESSION['ServiceProject'][$index]);
        // Reindex the array
        $_SESSION['ServiceProject'] = array_values($_SESSION['ServiceProject']);
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid index']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing index']);
}
?>
