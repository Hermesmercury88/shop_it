<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "shop_it2";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "เชื่อมต่อฐานข้อมูลล้มเหลว"]));
}

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id']);
$values = $data['data'];

// data[] = [customer_name, delivery_no, item_name, qty, price, unit, total]
$customer_name = $conn->real_escape_string($values[0]);
$delivery_no   = $conn->real_escape_string($values[1]);
$item_name     = $conn->real_escape_string($values[2]);
$qty           = floatval($values[3]);
$price         = floatval($values[4]);
$unit          = $conn->real_escape_string($values[5]);
$total         = floatval($values[6]);

// อัปเดตตาราง delivery_items
$sql = "UPDATE delivery_items 
        SET item_name='$item_name', qty=$qty, price=$price, unit='$unit', total=$total
        WHERE id=$id";

// อัปเดตชื่อลูกค้าและเลขที่ในตาราง deliveries (หากจำเป็น)
$sql2 = "UPDATE deliveries d
         JOIN delivery_items i ON i.delivery_id = d.id
         SET d.customer_name='$customer_name', d.delivery_no='$delivery_no'
         WHERE i.id=$id";

if ($conn->query($sql) && $conn->query($sql2)) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => $conn->error]);
}
$conn->close();
