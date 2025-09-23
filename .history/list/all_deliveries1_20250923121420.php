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
        i.id AS item_id,   /* ✅ ใช้ id ของ delivery_items */
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
/* ... style เดิมทั้งหมด ... */

.action-btn {
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin: 2px;
}
.btn-edit { background: #4caf50; color: #fff; }
.btn-save { background: #2196f3; color: #fff; }
.btn-cancel { background: #777; color: #fff; }
.btn-delete { background: #f44336; color: #fff; }
</style>
</head>
<body>
<div class="sidebar">
    <a href="http://localhost/shop/list/all_quotations1.php">ใบเสนอราคา</a>
    <a href="http://localhost/shop/list/all_deliveries1.php">ใบส่งของ</a>
    <a href="http://localhost/shop/list/all_receipts1.php">ใบเสร็จรับเงิน</a>
</div>

<div class="content">
    <div class="topbar">
        <img src="../pic/logo.png" alt="LOGO">
    </div>

    <h2>รายการใบส่งของ</h2>

    <!-- ✅ search box เดิม -->
    <div class="search-box">
        <label for="searchColumn">ค้นหาโดย: </label>
        <select id="searchColumn">
            <option value="1">ชื่อลูกค้า</option>
            <option value="2">เลขที่ / NO.</option>
            <option value="3">รายการสินค้า</option>
        </select>
        <input type="text" id="searchInput" placeholder="พิมพ์คำค้นหา...">

        <label for="rowsPerPage">แสดงแถว: </label>
        <select id="rowsPerPage">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>
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
                <th>จัดการ</th> <!-- ✅ เพิ่มคอลัมน์ -->
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr data-id='" . $row['item_id'] . "'>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td class='editable'>" . htmlspecialchars($row['customer_name']) . "</td>";
                    echo "<td class='editable'>" . htmlspecialchars($row['delivery_no']) . "</td>";
                    echo "<td class='editable'>" . htmlspecialchars($row['item_name']) . "</td>";
                    echo "<td class='editable'>" . $row['qty'] . "</td>";
                    echo "<td class='editable'>" . number_format($row['price'],2) . "</td>";
                    echo "<td class='editable'>" . htmlspecialchars($row['unit']) . "</td>";
                    echo "<td class='editable'>" . number_format($row['total'],2) . "</td>";
                    echo "<td>
                            <button class='action-btn btn-edit'>แก้ไข</button>
                            <button class='action-btn btn-delete'>ลบ</button>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>ยังไม่มีข้อมูล</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <button class="btn-pdf" onclick="window.location.href='../index.html'">กลับหน้าหลัก</button>
    <button class="btn-pdf" onclick="window.print()">พิมพ์ PDF</button>
</div>

<script>
// ✅ ฟังก์ชันค้นหา
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

// ✅ ฟังก์ชันเลือกจำนวนแถว
function updateTableRows() {
    let rowsPerPage = parseInt(document.getElementById("rowsPerPage").value);
    let table = document.getElementById("deliveryTable");
    let rows = table.getElementsByTagName("tr");

    let count = 0;
    for (let i = 1; i < rows.length; i++) {
        if (rows[i].style.display !== "none") {
            count++;
            rows[i].style.display = (count <= rowsPerPage) ? "" : "none";
        }
    }
}
document.getElementById("rowsPerPage").addEventListener("change", updateTableRows);
updateTableRows();

// ✅ ฟังก์ชันแก้ไข/ลบ
document.querySelectorAll(".btn-edit").forEach(btn => {
    btn.addEventListener("click", function() {
        let row = this.closest("tr");
        let tds = row.querySelectorAll(".editable");
        if (this.textContent === "แก้ไข") {
            tds.forEach(td => {
                let val = td.textContent;
                td.innerHTML = `<input value="${val}">`;
            });
            this.textContent = "บันทึก";
            this.className = "action-btn btn-save";
            let cancelBtn = document.createElement("button");
            cancelBtn.textContent = "ยกเลิก";
            cancelBtn.className = "action-btn btn-cancel";
            this.after(cancelBtn);
            cancelBtn.addEventListener("click", () => location.reload());
        } else {
            let data = [];
            tds.forEach(td => data.push(td.querySelector("input").value));
            let id = row.dataset.id;

            fetch("update_delivery_item.php", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify({id, data})
            }).then(r => r.json()).then(res => {
                if (res.success) location.reload();
                else alert("บันทึกไม่สำเร็จ");
            });
        }
    });
});

document.querySelectorAll(".btn-delete").forEach(btn => {
    btn.addEventListener("click", function() {
        if (!confirm("คุณต้องการลบข้อมูลนี้หรือไม่?")) return;
        let row = this.closest("tr");
        let id = row.dataset.id;

        fetch("delete_delivery_item.php", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({id})
        }).then(r => r.json()).then(res => {
            if (res.success) row.remove();
            else alert("ลบไม่สำเร็จ");
        });
    });
});
</script>

</body>
</html>
<?php $conn->close(); ?>
