<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "shop_it";

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// ตรวจสอบ method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("กรุณาส่งข้อมูลด้วย POST");
}

// รับค่าจากฟอร์ม
$delivery_no    = $_POST['delivery_no']    ?? '';
$customer_name  = $_POST['customer_name']  ?? '';
$customer_address = $_POST['customer_address'] ?? '';
$order_no       = $_POST['order_no']       ?? '';

$item_names = $_POST['item_name'] ?? [];
$qtys       = $_POST['qty']       ?? [];
$units      = $_POST['unit']      ?? [];

// บันทึกใบส่งของ
$stmt = $conn->prepare("INSERT INTO deliveries (delivery_no, customer_name, customer_address, order_no) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("ssss", $delivery_no, $customer_name, $customer_address, $order_no);
$stmt->execute();
$delivery_id = $stmt->insert_id;
$stmt->close();

// บันทึกรายการสินค้า
if (!empty($item_names)) {
    $stmt = $conn->prepare("INSERT INTO delivery_items (delivery_id, item_name, qty, unit) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    for ($i = 0; $i < count($item_names); $i++) {
        $name  = $item_names[$i];
        $qty   = $qtys[$i]   ?? 0;
        $unit  = $units[$i]  ?? '';

        $stmt->bind_param("isis", $delivery_id, $name, $qty, $unit);
        $stmt->execute();
    }
    $stmt->close();
}

$conn->close();

// ✅ เสร็จแล้วเด้งกลับไปหน้า index.html
header("Location: index.html");
exit;
?>
