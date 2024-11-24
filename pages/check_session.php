<?php
    session_start();
    header('Content-Type: application/json');
    if (isset($_SESSION['uid'])) {
        echo json_encode(['status' => 'logged_in']);
    } else {
        echo json_encode(['status' => 'not_logged_in']);
    }
?>