<?php
header("Content-Type: application/json");

// 🔹 ตั้งค่าฐานข้อมูล
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "shop_it2";

// 🔹 เชื่อมต่อ
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "เชื่อมต่อฐานข้อมูลล้มเหลว"]);
    exit;
}

// 🔹 รับค่าจาก frontend
$data = json_decode(file_get_contents("php://input"), true);
$id   = $data["id"] ?? null;
$vals = $data["data"] ?? [];

if (!$id || count($vals) < 8) {
    echo json_encode(["success" => false, "message" => "ข้อมูลไม่ครบ"]);
    exit;
}

// 🔹 Map ข้อมูลจากตาราง (เรียงตาม column ที่หน้า all_receipts1.php)
$order_no        = $vals[0];
$customer_name   = $vals[1];
$customer_address= $vals[2];
$item_name       = $vals[3];
$qty             = $vals[4];
$price           = $vals[5];
$unit            = $vals[6];
$total           = $vals[7];

// 🔹 Update receipt_items
$sql_item = "UPDATE receipt_items 
             SET item_name=?, qty=?, price=?, unit=?, total=? 
             WHERE id=?";
$stmt_item = $conn->prepare($sql_item);
if (!$stmt_item) {
    echo json_encode(["success" => false, "message" => "เตรียม statement item ไม่ได้"]);
    exit;
}
$stmt_item->bind_param("sidssi", $item_name, $qty, $price, $unit, $total, $id);

// 🔹 Update receipts (ใช้ subquery หา receipt_id จาก item id)
$sql_main = "UPDATE receipts 
             SET order_no=?, customer_name=?, customer_address=? 
             WHERE id=(SELECT receipt_id FROM receipt_items WHERE id=?)";
$stmt_main = $conn->prepare($sql_main);
if (!$stmt_main) {
    echo json_encode(["success" => false, "message" => "เตรียม statement main ไม่ได้"]);
    exit;
}
$stmt_main->bind_param("sssi", $order_no, $customer_name, $customer_address, $id);

// 🔹 Execute
$success = $stmt_item->execute() && $stmt_main->execute();

if ($success) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "อัปเดตไม่สำเร็จ",
        "error_item" => $stmt_item->error,
        "error_main" => $stmt_main->error
    ]);
}

$stmt_item->close();
$stmt_main->close();
$conn->close();
?>
