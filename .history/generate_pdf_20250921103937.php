<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

// รับค่าจากฟอร์ม
$order_no = $_POST['order_no'];
$customer = $_POST['customer_name'];
$address = $_POST['customer_address'];
$item_names = $_POST['item_name'];
$qtys = $_POST['qty'];
$units = $_POST['unit'];
$prices = $_POST['price'];
$thaiText = $_POST['thaiText'];

// เริ่มสร้าง HTML
$html = "
<style>
    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 14px;
        line-height: 1.5;
    }
    h2 {
        margin: 0;
        padding: 0;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 10px;
    }
    th, td {
        border: 1px solid #000;
        padding: 6px;
        font-size: 14px;
        line-height: 1.4;
    }
    th {
        text-align: center;
        background: #f0f0f0;
    }
    td {
        vertical-align: middle;
    }
    .right { text-align: right; }
    .center { text-align: center; }
    .bold { font-weight: bold; }
</style>

<h2 style='text-align:center;'>ใบเสร็จรับเงิน</h2>
<p><b>เลขที่ใบสั่งซื้อ (Order No):</b> $order_no</p>
<p><b>ชื่อลูกค้า:</b> $customer</p>
<p><b>ที่อยู่:</b> $address</p>

<table>
    <tr>
        <th>ลำดับ</th>
        <th>รายการ</th>
        <th>จำนวน</th>
        <th>หน่วย</th>
        <th>ราคา/หน่วย</th>
        <th>ราคารวม</th>
    </tr>
";

$total = 0;
for($i=0; $i<count($item_names); $i++){
    $sum = $qtys[$i] * $prices[$i];
    $total += $sum;
    $html .= "
    <tr>
        <td class='center'>".($i+1)."</td>
        <td>{$item_names[$i]}</td>
        <td class='center'>{$qtys[$i]}</td>
        <td class='center'>{$units[$i]}</td>
        <td class='right'>".number_format($prices[$i],2)."</td>
        <td class='right'>".number_format($sum,2)."</td>
    </tr>";
}

// แถวรวม
$html .= "
<tr>
    <td colspan='4' class='center bold'>รวมเป็นเงินทั้งสิ้น</td>
    <td colspan='2' class='right bold'>".number_format($total,2)."</td>
</tr>
<tr>
    <td colspan='6' style='text-align:center; font-weight:bold; padding-top:10px;'>(ตัวอักษร: <b>$thaiText</b>)</td>
</tr>
</table>

<p style='margin-top:50px;'>ลงชื่อผู้รับเงิน ...........................................</p>
";

// สร้าง PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// ดาวน์โหลด PDF
$filename = "receipt_" . $order_no . "_" . time() . ".pdf";
$dompdf->stream($filename, ["Attachment" => false]);
