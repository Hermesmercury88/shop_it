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

// ดึงข้อมูล receipts + receipt_items
$sql = "
    SELECT 
        i.id AS item_id,
        r.order_no,
        r.customer_name,
        r.customer_address,
        i.item_name,
        i.qty,
        i.price,
        i.unit,
        i.total
    FROM receipts r
    JOIN receipt_items i ON i.receipt_id = r.id
    ORDER BY r.id, i.id
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ใบเสร็จรับเงิน</title>
<style>
/* --- CSS เหมือนตัวก่อนหน้า --- */
body{margin:0;font-family:Tahoma,sans-serif;background:#f9f9ef;}
.sidebar{width:220px;height:100vh;background:#b89172;float:left;color:#fff;display:flex;flex-direction:column;align-items:center;padding-top:30px;}
.sidebar a{color:#fff;text-decoration:none;margin:15px 0;display:block;width:100%;text-align:center;transition:background 0.3s;}
.sidebar a:hover{background: rgba(255,255,255,0.2);border-radius:6px;}
.content{margin-left:220px;padding:20px;}
.topbar{background:#d4b295;padding:15px 0;text-align:center;margin-bottom:25px;box-sizing:border-box;border-radius:6px 6px 0 0;box-shadow:0 2px 5px rgba(0,0,0,0.1);}
.topbar img{max-height:60px;display:block;margin:0 auto;}
h2{margin:20px 0;font-weight:600;color:#4d3a2c;}
table{width:100%;border-collapse:collapse;border-radius:6px;overflow:hidden;box-shadow:0 2px 5px rgba(0,0,0,0.05);}
th,td{border:1px solid #333;padding:10px;text-align:center;}
th{background:#e5c3a6;font-weight:600;}
tr:nth-child(even){background:#fdf5ef;}
tr:hover{background:#ffe8d0;}
.search-box{margin:10px 0 15px;}
input,select{padding:8px;border:1px solid #ccc;border-radius:4px;outline:none;}
input:focus,select:focus{border-color:#b89172;}
.action-btn{padding:5px 10px;border:none;border-radius:4px;cursor:pointer;margin:2px;}
.btn-edit{background:#4caf50;color:#fff;}
.btn-save{background:#2196f3;color:#fff;}
.btn-cancel{background:#777;color:#fff;}
.btn-delete{background:#f44336;color:#fff;}
.btn-pdf,.btn-upload{margin-top:20px;padding:10px 25px;border:none;border-radius:6px;cursor:pointer;font-weight:bold;transition:background 0.3s;}
.btn-pdf{background:#f77c7c;color:#000;}
.btn-pdf:hover{background:#f55c5c;}
.btn-upload{background:#7cf7b3ff;color:#000;}
.btn-upload:hover{background:#3ad334ff;}
@media print{@page{size:A4 portrait;margin:5mm;}body{background:#fff;margin:0;font-size:12pt;}.sidebar,.search-box,.btn-pdf,.btn-upload,.action-btn,.topbar{display:none !important;}.content{margin:0;padding:0;}table{border-collapse:collapse;width:auto;margin:0;border:3px solid #000;}th,td{border:1px solid #000;padding:6px;text-align:center;}table th:last-child,table td:last-child{display:none !important;}}
</style>
</head>
<body>

<div class="sidebar">
    <a href="all_quotations1.php">ใบเสนอราคา</a>
    <a href="all_deliveries1.php">ใบส่งของ</a>
    <a href="all_receipts1.php">ใบเสร็จรับเงิน</a>
</div>

<div class="content">
<div class="topbar"><img src="../pic/logo.png" alt="LOGO"></div>
<h2>รายการใบเสร็จรับเงิน</h2>

<div class="search-box">
<label for="searchColumn">ค้นหาโดย: </label>
<select id="searchColumn">
<option value="1">เลขที่ใบเสร็จ</option>
<option value="2">ชื่อลูกค้า</option>
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

<table id="receiptTable">
<thead>
<tr>
<th>ลำดับ</th>
<th>เลขที่ใบเสร็จ</th>
<th>ชื่อลูกค้า</th>
<th>ที่อยู่ลูกค้า</th>
<th>รายการ</th>
<th>จำนวน</th>
<th>ราคา</th>
<th>หน่วย</th>
<th>ราคารวม</th>
<th>จัดการ</th>
</tr>
</thead>
<tbody>
<?php
$no = 1;
if($result->num_rows>0){
    while($row=$result->fetch_assoc()){
        echo "<tr data-id='".$row['item_id']."'>";
        echo "<td>".$no++."</td>";
        echo "<td class='editable'>".htmlspecialchars($row['order_no'])."</td>";
        echo "<td class='editable'>".htmlspecialchars($row['customer_name'])."</td>";
        echo "<td class='editable'>".htmlspecialchars($row['customer_address'])."</td>";
        echo "<td class='editable'>".htmlspecialchars($row['item_name'])."</td>";
        echo "<td class='editable col-qty'>".$row['qty']."</td>";
        echo "<td class='editable col-price'>".number_format($row['price'],2)."</td>";
        echo "<td class='editable'>".htmlspecialchars($row['unit'])."</td>";
        echo "<td class='editable col-total'>".number_format($row['total'],2)."</td>";
        echo "<td>
        <button class='action-btn btn-edit'>แก้ไข</button>
        <button class='action-btn btn-delete'>ลบ</button>
        </td>";
        echo "</tr>";
    }
}else{
    echo "<tr><td colspan='10'>ยังไม่มีข้อมูล</td></tr>";
}
?>
</tbody>
</table>

<button class="btn-pdf" onclick="window.location.href='../index.html'">กลับหน้าหลัก</button>
<button class="btn-pdf" onclick="window.print()">พิมพ์ PDF</button>
<button class="btn-upload" onclick="window.open('../receipts_add.html','_blank')">เพิ่มใบเสร็จ</button>
</div>

<script>
// ค้นหา
document.getElementById("searchInput").addEventListener("keyup",function(){
    let input=this.value.toLowerCase();
    let table=document.getElementById("receiptTable");
    let rows=table.getElementsByTagName("tr");
    let column=parseInt(document.getElementById("searchColumn").value);
    for(let i=1;i<rows.length;i++){
        let cell=rows[i].getElementsByTagName("td")[column];
        rows[i].style.display = cell && cell.textContent.toLowerCase().includes(input) ? "" : "none";
    }
});
// แสดงจำนวนแถว
function updateTableRows(){
    let rowsPerPage=parseInt(document.getElementById("rowsPerPage").value);
    let table=document.getElementById("receiptTable");
    let rows=table.getElementsByTagName("tr");
    let count=0;
    for(let i=1;i<rows.length;i++){
        if(rows[i].style.display!=="none"){
            count++;
            rows[i].style.display=(count<=rowsPerPage)?"":"none";
        }
    }
}
document.getElementById("rowsPerPage").addEventListener("change",updateTableRows);
updateTableRows();

// แก้ไข/บันทึก/ยกเลิก
document.querySelectorAll('.btn-edit').forEach(btn=>{
    btn.addEventListener('click',function(){
        let tr=this.closest('tr');
        let inputs=tr.querySelectorAll('.editable');
        if(this.textContent==='แก้ไข'){
            inputs.forEach(td=>{
                let val=td.textContent;
                td.innerHTML="<input value='"+val+"' style='width:100px'>";
            });
            this.textContent='บันทึก';
            let cancelBtn=document.createElement('button');
            cancelBtn.textContent='ยกเลิก';
            cancelBtn.className='action-btn btn-cancel';
            this.after(cancelBtn);
            cancelBtn.addEventListener('click',()=>{
                location.reload(); // คืนค่าเดิม
            });
        } else {
            let data={};
            inputs.forEach(td=>{
                let key=td.className.includes('col-qty')?'qty':
                        td.className.includes('col-price')?'price':
                        td.className.includes('col-total')?'total':
                        td.previousElementSibling ? td.previousElementSibling.textContent : td.textContent;
                data[key]=td.querySelector('input')?td.querySelector('input').value:td.textContent;
            });
            let id=tr.dataset.id;
            fetch('update_receipts_item.php',{
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body:JSON.stringify({id,data})
            }).then(r=>r.json()).then(res=>{
                if(res.success) location.reload();
                else alert('บันทึกไม่สำเร็จ');
            });
        }
    });
});

// ลบ
document.querySelectorAll('.btn-delete').forEach(btn=>{
    btn.addEventListener('click',function(){
        if(!confirm('คุณต้องการลบรายการนี้หรือไม่?')) return;
        let tr=this.closest('tr');
        let id=tr.dataset.id;
        fetch('delete_receipts_item.php',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({id})
        }).then(r=>r.json()).then(res=>{
            if(res.success) tr.remove();
            else alert('ลบไม่สำเร็จ');
        });
    });
});
</script>

</body>
</html>
<?php $conn->close(); ?>
