<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "shop_it";

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);

// รับข้อมูล
$order_no = $_POST['order_no']; // เพิ่มตรงนี้
$customer_name = $_POST['customer_name'];
$customer_address = $_POST['customer_address'];
$grand_total = $_POST['grand_total'];

$item_names = $_POST['item_name'];
$qtys = $_POST['qty'];
$units = $_POST['unit'];
$prices = $_POST['price'];
$totals = $_POST['total'];

// $order_no = $_POST['order_no'] ?? '';
// $customer_name = $_POST['customer_name'] ?? '';
// $customer_address = $_POST['customer_address'] ?? '';
// $grand_total = $_POST['grand_total'] ?? 0;

// $item_names = $_POST['item_name'] ?? [];
// $qtys = $_POST['qty'] ?? [];
// $units = $_POST['unit'] ?? [];
// $prices = $_POST['price'] ?? [];
// $totals = $_POST['total'] ?? [];


// บันทึกใบเสร็จ พร้อม order_no
$stmt = $conn->prepare("INSERT INTO receipts (order_no, customer_name, customer_address, grand_total) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssd", $order_no, $customer_name, $customer_address, $grand_total);
$stmt->execute();
$receipt_id = $stmt->insert_id;
$stmt->close();

// บันทึกรายละเอียดสินค้า
$stmt = $conn->prepare("INSERT INTO receipt_items (receipt_id, item_name, qty, unit, price, total) VALUES (?, ?, ?, ?, ?, ?)");
for($i=0; $i<count($item_names); $i++){
    $stmt->bind_param("isiddd", $receipt_id, $item_names[$i], $qtys[$i], $units[$i], $prices[$i], $totals[$i]);
    $stmt->execute();
}
$stmt->close();
$conn->close();

echo "บันทึกข้อมูลเรียบร้อยแล้ว <a href='index.html'>กลับหน้าหลัก</a>";
?>
