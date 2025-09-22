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

<link rel="shortcut icon" href="../pic/logo.png" type="image/x-icon" />
<link rel="icon" href="../pic/logo.png" type="image/x-icon" />
<link rel="apple-touch-icon" href="../pic/logo.png" />
<link href="" rel="stylesheet">

</head>
<body>
<div class="sidebar">
    <a href="#">ใบเสนอราคา</a>
    <a href="#">ใบสั่งของ</a>
    <a href="#">ใบเสร็จรับเงิน</a>
</div>

<div class="content">
    <div class="topbar">
        <img src="../pic/logo.png" alt="LOGO">
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

    <button class="btn-pdf" onclick="window.print()">พิมพ์ PDF</button>
</div>

<script>
// ฟังก์ชันค้นหา (client side)
document.getElementById("searchInput").addEventListener("keyup", function() {
    let input = this.value.toLowerCase();
    let table = document.getElementById("deliveryTable");
    let rows = table.getElementsByTagName("tr");
    let column = parseInt(document.getElementById("searchColumn").value);

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
