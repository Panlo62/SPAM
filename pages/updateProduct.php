<?php
    $conn = mysqli_connect("localhost", "root", "", "spam");
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cartData = json_decode(file_get_contents('php://input'), true);
        foreach ($cartData as $item) {
            $id = intval($item['id']);
            $quantity = intval($item['quantity']);
            $sql = "UPDATE product SET inventory = inventory - $quantity WHERE pid = $id";
            mysqli_query($conn, $sql);
        }
    }
    mysqli_close($conn);
?>
