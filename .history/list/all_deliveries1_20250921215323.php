<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "shop_it";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// ดึงข้อมูล delivery พร้อม item
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
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>รายการส่งของทั้งหมด</title>
<link href="css/delivery.css" rel="stylesheet">
<style>
table { width: 100%; border-collapse: collapse; }
th, td { border: 1px solid #333; padding: 8px; text-align: center; }
th { background-color: #f0f0f0; }
</style>
</head>
<body>

<h2>รายการส่งของทั้งหมด</h2>
<table>
    <thead>
        <tr>
            <th>ลำดับ</th>
            <th>ชื่อลูกค้า</th>
            <th>เลขที่ / NO.</th>
            <th>รายการ</th>
            <th>จำนวน</th>
            <th>ราคา</th>
            <th>หน่วย</th>
            <th>ราคารวม</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['delivery_no']) . "</td>";
                echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                echo "<td>" . $row['qty'] . "</td>";
                echo "<td>" . number_format($row['price'],2) . "</td>";
                echo "<td>" . htmlspecialchars($row['unit']) . "</td>";
                echo "<td>" . number_format($row['total'],2) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>ยังไม่มีข้อมูล</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>

<?php $conn->close(); ?>
