<?php
    session_start();
    $uid = $_SESSION['uid'];
    $conn = mysqli_connect("localhost", "root", "", "spam");
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = intval($input['id']);
        $qty = intval($input['qty']);
        $sql = "UPDATE cart SET quantity = $qty WHERE pid = $id AND uid = $uid";
        mysqli_query($conn, $sql);
    }
    mysqli_close($conn);
?>
