<?php
    session_start();
    $uid = $_SESSION['uid'];
    $conn = mysqli_connect("localhost", "root", "", "spam");
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = intval($input['id']);
        if ($id === 0) {
            $sql = "DELETE FROM cart WHERE uid = $uid";
        } else {
            $sql = "DELETE FROM cart WHERE uid = $uid AND pid = $id";
        }
        mysqli_query($conn, $sql);
    }
    mysqli_close($conn);
?>