<?php
    session_start();
    $uid = $_SESSION['uid'];
    $conn = mysqli_connect("localhost", "root", "", "spam");
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cartData = json_decode(file_get_contents('php://input'), true);
        $result = mysqli_query($conn, "SELECT MAX(oid) AS oid FROM orders");
        $row = mysqli_fetch_assoc($result);
        $oid = (int)$row['oid'] + 1;
        $sql = "INSERT INTO orders VALUES ($oid, $uid, TRUE, FALSE)";
        mysqli_query($conn, $sql);

        foreach ($cartData as $item) {
            $id = intval($item['id']);
            $quantity = intval($item['quantity']);
            $sql = "UPDATE product SET inventory = inventory - $quantity WHERE pid = $id";
            mysqli_query($conn, $sql);
            $sql = "INSERT INTO order_items VALUES ($oid, $id, $quantity)";
            mysqli_query($conn, $sql);
        }
    }
    mysqli_close($conn);
?>