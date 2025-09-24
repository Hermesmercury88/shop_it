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
$delivery_no       = $_POST['delivery_no']       ?? '';
$customer_name     = $_POST['customer_name']     ?? '';
$customer_address  = $_POST['customer_address']  ?? '';
$order_no          = $_POST['order_no']          ?? '';

$item_names = $_POST['item_name'] ?? [];
$qtys       = $_POST['qty']       ?? [];
$prices     = $_POST['price']     ?? [];
$units      = $_POST['unit']      ?? [];
$totals     = $_POST['total']     ?? [];

// ---------------------------
// อัพโหลดไฟล์ PDF
// ---------------------------
$pdf_file_path = null;

if (!empty($_FILES['pdf_file']['name'])) {
    $targetDir = "uploads/deliveries/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true); // สร้างโฟลเดอร์ถ้ายังไม่มี
    }

    $fileName = time() . "_" . basename($_FILES["pdf_file"]["name"]);
    $targetFilePath = $targetDir . $fileName;

    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // ตรวจสอบว่าเป็น PDF
    if ($fileType === "pdf") {
        if (move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $targetFilePath)) {
            $pdf_file_path = $targetFilePath; // เก็บ path ลงฐานข้อมูล
        }
    }
}

// ---------------------------
// บันทึกใบส่งของ
// ---------------------------
$stmt = $conn->prepare("INSERT INTO deliveries (delivery_no, customer_name, customer_address, order_no, pdf_file) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("sssss", $delivery_no, $customer_name, $customer_address, $order_no, $pdf_file_path);
$stmt->execute();
$delivery_id = $stmt->insert_id;
$stmt->close();

// ---------------------------
// บันทึกรายการสินค้า
// ---------------------------
if (!empty($item_names)) {
    $stmt = $conn->prepare("INSERT INTO delivery_items (delivery_id, item_name, qty, price, unit, total) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    for ($i = 0; $i < count($item_names); $i++) {
        $name  = $item_names[$i] ?? '';
        $qty   = (int)($qtys[$i] ?? 0);
        $price = (float)($prices[$i] ?? 0.00);
        $unit  = $units[$i] ?? '';
        $total = (float)($totals[$i] ?? 0.00);

        $stmt->bind_param("isidsd", $delivery_id, $name, $qty, $price, $unit, $total);
        $stmt->execute();
    }
    $stmt->close();
}

$conn->close();

echo "บันทึกข้อมูลเรียบร้อยแล้ว <a href='deliveries_list.php'>ไปหน้ารายการใบส่งของ</a>";
?>
