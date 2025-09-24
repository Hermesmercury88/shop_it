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

// ---------------------------
// อัพโหลดไฟล์ PDF
// ---------------------------
$pdf_file_path = null;

if (!empty($_FILES['pdf_file']['name'])) {
    // ตรวจสอบว่ามีโฟลเดอร์ uploads/quotations หรือยัง ถ้าไม่มีสร้างใหม่
    if (!is_dir('uploads/quotations')) {
        mkdir('uploads/quotations', 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES["pdf_file"]["name"]);
    $targetFilePath = 'uploads/quotations/' . $fileName;

    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // ตรวจสอบว่าเป็น PDF
    if ($fileType === "pdf") {
        if (move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $targetFilePath)) {
            $pdf_file_path = $targetFilePath; // เก็บ path ลงฐานข้อมูล
        } else {
            die("ไม่สามารถอัพโหลดไฟล์ได้");
        }
    } else {
        die("ไฟล์ต้องเป็น PDF เท่านั้น");
    }
}

// ---------------------------
// บันทึกใบเสนอราคา
// ---------------------------
$stmt = $conn->prepare("INSERT INTO quotations (quotation_no, subject, recipient_name, pdf_file) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("ssss", $quotation_no, $subject, $recipient_name, $pdf_file_path);
$stmt->execute();
$quotation_id = $stmt->insert_id;
$stmt->close();

// ---------------------------
// บันทึกรายการสินค้า
// ---------------------------
if (!empty($item_names)) {
    $stmt = $conn->prepare("INSERT INTO quotation_items (quotation_id, item_name, qty, price, unit, total) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    for ($i = 0; $i < count($item_names); $i++) {
        $name  = $item_names[$i];
        $qty   = (int)($qtys[$i] ?? 0);
        $price = (float)($prices[$i] ?? 0);
        $unit  = $units[$i]  ?? '';
        $total = (float)($totals[$i] ?? 0);

        $stmt->bind_param("isidss", $quotation_id, $name, $qty, $price, $unit, $total);
        $stmt->execute();
    }
    $stmt->close();
}

$conn->close();

echo "บันทึกข้อมูลเรียบร้อยแล้ว <a href='index.html'>กลับหน้าหลัก</a> หรืออยาก <a href='quotations1.html'>กรอกต่อ</a>";
?>
