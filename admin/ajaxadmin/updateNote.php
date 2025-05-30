<?php
session_start();

if (isset($_POST['index'], $_POST['note'])) {
    $index = (int)$_POST['index'];
    $note = $_POST['note'];

    if (isset($_SESSION['ServiceProject'][$index])) {
        $_SESSION['ServiceProject'][$index]['note'] = $note;
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid index']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing data']);
}
?>
