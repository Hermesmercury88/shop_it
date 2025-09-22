<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "shop_it2";

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
$quotation_no   = $_POST['quotation_no']   ?? '';
$subject        = $_POST['subject']        ?? '';
$recipient_name = $_POST['recipient_name'] ?? '';

$item_names = $_POST['item_name'] ?? [];
$qtys       = $_POST['qty']       ?? [];
$prices     = $_POST['price']     ?? [];
$units      = $_POST['unit']      ?? [];
$totals     = $_POST['total']     ?? [];

// บันทึกใบเสนอราคา
$stmt = $conn->prepare("INSERT INTO quotations (quotation_no, subject, recipient_name) VALUES (?, ?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("sss", $quotation_no, $subject, $recipient_name);
$stmt->execute();
$quotation_id = $stmt->insert_id;
$stmt->close();

// บันทึกรายการสินค้า
if (!empty($item_names)) {
    $stmt = $conn->prepare("INSERT INTO quotation_items (quotation_id, item_name, qty, price, unit, total) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    for ($i = 0; $i < count($item_names); $i++) {
        $name  = $item_names[$i];
        $qty   = $qtys[$i]   ?? 0;
        $price = $prices[$i] ?? 0;
        $unit  = $units[$i]  ?? '';
        $total = $totals[$i] ?? 0;

        $stmt->bind_param("isiddd", $quotation_id, $name, $qty, $price, $unit, $total);
        $stmt->execute();
    }
    $stmt->close();
}

$conn->close();

echo "บันทึกข้อมูลเรียบร้อยแล้ว <a href='index.html'>กลับหน้าหลัก</a> หรืออยาก <a haref='quotations2.html'>กรอกต่อ</a>";
?>
