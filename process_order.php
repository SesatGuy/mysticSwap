<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $jsonData = file_get_contents("php://input");
    $order = json_decode($jsonData, true);

    if (!$order || !isset($order["from"], $order["to"], $order["amount"], $order["receive"], $order["wallet"], $order["gmail"])) {
        http_response_code(400);
        echo "Invalid order data!";
        exit;
    }

    // Load 
    $ordersFile = "json/orders.json";
    if (!file_exists($ordersFile)) {
        file_put_contents($ordersFile, json_encode([]));
    }
    $orders = json_decode(file_get_contents($ordersFile), true);

    $order["id"] = uniqid("order_");
    $order["status"] = "pending";
    $order["created_at"] = gmdate("Y-m-d H:i:s");  // Generates timestamp in UTC


    $orders[] = $order;
    file_put_contents($ordersFile, json_encode($orders, JSON_PRETTY_PRINT));

    echo "Order placed successfully!";
} else {
    http_response_code(405);
    echo "Method Not Allowed";
}
