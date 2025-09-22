<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "shop_it";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// ดึงข้อมูล deliveries + items
$sql = "
    SELECT 
        d.customer_name,
        d.delivery_no,
        i.item_name,
        i.qty,
        i.price,
        i.unit,
        i.total
    FROM deliveries d
    JOIN delivery_items i ON i.delivery_id = d.id
    ORDER BY d.id, i.id
";
$result = $conn->query($sql);

$data = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// ถ้าเรียกจาก Ajax (XMLHttpRequest) → ส่ง JSON
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// ถ้าเข้าหน้า PHP ตรงๆ → แสดงตาราง HTML (สำหรับ debug)
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>รายการส่งของ</title>
<style>
body { font-family: Tahoma, sans-serif; padding: 20px; }
table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
th { background: #f2f2f2; }
</style>
</head>
<body>
<h2>รายการส่งของ</h2>
<?php if(count($data) > 0): ?>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>ลูกค้า</th>
            <th>เลขที่ส่งของ</th>
            <th>สินค้า</th>
            <th>จำนวน</th>
            <th>ราคา/หน่วย</th>
            <th>หน่วย</th>
            <th>รวม</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($data as $idx => $row): ?>
        <tr>
            <td><?= $idx+1 ?></td>
            <td><?= $row['customer_name'] ?></td>
            <td><?= $row['delivery_no'] ?></td>
            <td><?= $row['item_name'] ?></td>
            <td><?= $row['qty'] ?></td>
            <td><?= number_format($row['price'],2) ?></td>
            <td><?= $row['unit'] ?></td>
            <td><?= number_format($row['total'],2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p>ไม่มีข้อมูล</p>
<?php endif; ?>
</body>
</html>
<?php $conn->close(); ?>
