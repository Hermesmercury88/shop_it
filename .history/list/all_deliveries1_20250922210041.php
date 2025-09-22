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

// ดึงข้อมูลจากตาราง deliveries + delivery_items
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบส่งของ</title>
      <style>
    body { margin:0; font-family: Tahoma, sans-serif; background:#f9f9ef; }
    .sidebar { width:220px; height:100vh; background:#b89172; float:left; color:#fff; display:flex; flex-direction:column; align-items:center; padding-top:30px; }
    .sidebar a { color:#fff; text-decoration:none; margin:15px 0; display:block; width:100%; text-align:center; }
    .content { margin-left:220px; padding:20px; }
    .topbar {
        background: #d4b295;
        padding: 10px 0;       /* padding บน-ล่าง 10px, ซ้าย-ขวา 0 */
        text-align: center;
        margin-bottom: 20px;   /* เว้นระยะห่างกับ h2 / ตาราง */
        box-sizing: border-box; /* ให้ padding ไม่ทำให้ขนาดเกิน */
}

    h2 { margin:20px 0; }
    table { width:100%; border-collapse:collapse; }
    th, td { border:1px solid #ccc; padding:8px; text-align:center; }
    th { background:#e5c3a6; }
    tr:nth-child(even){background:#fdf5ef;}
    .search-box { margin:10px 0; }
    input, select { padding:6px; }
    .btn-pdf { margin-top:20px; background:#f77c7c; color:#000; padding:10px 20px; border:none; border-radius:6px; cursor:pointer; font-weight:bold; }
  </style>
</head>
<body>
    <div class="sidebar"> 
        <a href="#">ใบเสนอราคา</a> 
        <a href="#">ใบสั่งของ</a> 
        <a href="#">ใบเสร็จรับเงิน</a> 
    </div> <div class="content"> 
        <div class="topbar"> 
            <div class="logo"><img src="../pic/logo.png" alt="LOGO" height="60"> 
        </div> 

<h2>รายการใบส่งของ</h2>

<div class="search-box">
    <label for="searchColumn">ค้นหาโดย: </label>
    <select id="searchColumn">
        <option value="1">ชื่อลูกค้า</option>
        <option value="2">เลขที่ / NO.</option>
        <option value="3">รายการสินค้า</option>
    </select>
    <input type="text" id="searchInput" placeholder="พิมพ์คำค้นหา...">
</div>

<table id="deliveryTable">
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

<script>
// ฟังก์ชันค้นหา (client side)
document.getElementById("searchInput").addEventListener("keyup", function() {
    let input = this.value.toLowerCase();
    let table = document.getElementById("deliveryTable");
    let rows = table.getElementsByTagName("tr");
    let column = document.getElementById("searchColumn").value;

    for (let i = 1; i < rows.length; i++) {
        let cell = rows[i].getElementsByTagName("td")[column];
        if (cell) {
            let text = cell.textContent.toLowerCase();
            rows[i].style.display = text.includes(input) ? "" : "none";
        }
    }
});
</script>

</body>
</html>
<?php $conn->close(); ?>
