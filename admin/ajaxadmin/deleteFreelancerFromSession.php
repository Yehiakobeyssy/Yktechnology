<?php
session_start();

if (isset($_POST['index']) && isset($_SESSION['freelancerProject'][$_POST['index']])) {
    $index = (int)$_POST['index'];
    unset($_SESSION['freelancerProject'][$index]);

    // Re-index array to prevent holes
    $_SESSION['freelancerProject'] = array_values($_SESSION['freelancerProject']);

    echo json_encode([
    'status' => 'deleted',
    'array' => $_SESSION['freelancerProject'] // no need to use print_r
]);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid index']);
}
?>
