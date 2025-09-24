<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "shop_it";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "เชื่อมต่อฐานข้อมูลล้มเหลว"]));
}

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id']);
$values = $data['data'];

// data[] = [customer_name, delivery_no, item_name, qty, price, unit, total]
$customer_name = $values[0] ?? '';
$delivery_no   = $values[1] ?? '';
$item_name     = $values[2] ?? '';
$qty           = floatval($values[3] ?? 0);
$price         = floatval($values[4] ?? 0);
$unit          = $values[5] ?? '';
$total         = floatval($values[6] ?? 0);

// อัปเดตตาราง delivery_items
$stmt1 = $conn->prepare("UPDATE delivery_items SET item_name=?, qty=?, price=?, unit=?, total=? WHERE id=?");
$stmt1->bind_param("sddsdi", $item_name, $qty, $price, $unit, $total, $id);

// อัปเดตตาราง deliveries (customer_name, delivery_no)
$stmt2 = $conn->prepare("UPDATE deliveries d
                         JOIN delivery_items i ON i.delivery_id = d.id
                         SET d.customer_name=?, d.delivery_no=?
                         WHERE i.id=?");
$stmt2->bind_param("ssi", $customer_name, $delivery_no, $id);

if ($stmt1->execute() && $stmt2->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => $conn->error]);
}

$stmt1->close();
$stmt2->close();
$conn->close();
?>
