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

// ตรวจสอบว่ามีการส่ง POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("กรุณาส่งข้อมูลด้วย POST");
}

// รับข้อมูลจากฟอร์ม (ตั้งค่า default ป้องกัน Warning)
$order_no = $_POST['order_no'] ?? '';
$customer_name = $_POST['customer_name'] ?? '';
$customer_address = $_POST['customer_address'] ?? '';
$grand_total = $_POST['grand_total'] ?? 0;

$item_names = $_POST['item_name'] ?? [];
$qtys = $_POST['qty'] ?? [];
$prices = $_POST['price'] ?? [];
$units = $_POST['unit'] ?? [];
$totals = $_POST['total'] ?? [];

// บันทึกใบเสร็จ
$stmt = $conn->prepare("INSERT INTO receipts (order_no, customer_name, customer_address, grand_total) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("sssd", $order_no, $customer_name, $customer_address, $grand_total);
$stmt->execute();
$receipt_id = $stmt->insert_id;
$stmt->close();

// บันทึกรายละเอียดสินค้า
if (!empty($item_names)) {
    $stmt = $conn->prepare("INSERT INTO receipt_items (receipt_id, item_name, qty, unit, price, total) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    for ($i = 0; $i < count($item_names); $i++) {
        $name = $item_names[$i];
        $qty = $qtys[$i] ?? 0;
        $price = $prices[$i] ?? 0;
        $unit = $units[$i] ?? '';
        $total = $totals[$i] ?? 0;

        $stmt->bind_param("isiddd", $receipt_id, $name, $qty, $unit, $price, $total);
        $stmt->execute();
    }
    $stmt->close();
}

$conn->close();

echo "บันทึกข้อมูลเรียบร้อยแล้ว <a href='index.html'>กลับหน้าหลัก</a> หรืออยาก <a haref='receipts11.html'>กรอกต่อ</a>"";
?>
