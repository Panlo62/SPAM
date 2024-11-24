<?php
    session_start();
    header('Content-Type: application/json');
    $uid = $_SESSION['uid'];
    $pid = $_GET['pid'];
    $conn = mysqli_connect("localhost", "root", "", "spam");
    $sql = "SELECT quantity FROM cart WHERE pid = $pid AND uid=$uid";
    $result = mysqli_query($conn, $sql);    
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $quantity = $row['quantity'];
        }
    }
    else {
        $quantity = 1;
    }
    $sql = "SELECT inventory FROM product WHERE pid = $pid";
    $result = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_assoc($result)) {
        $inventory = $row['inventory'];
    }
    echo json_encode(['quantity' => $quantity, 'inventory' => $inventory]);
    mysqli_close($conn);
?>
