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

// สร้าง HTML
$html = "
<h2 style='text-align:center;'>ใบเสร็จรับเงิน</h2>
<p><b>เลขที่ใบสั่งซื้อ (Order No):</b> $order_no</p>
<p><b>ชื่อลูกค้า:</b> $customer</p>
<p><b>ที่อยู่:</b> $address</p>
<table border='1' width='100%' cellspacing='0' cellpadding='5'>
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
    $sum = $qtys[$i]*$prices[$i];
    $total += $sum;
    $html .= "
    <tr>
        <td>".($i+1)."</td>
        <td>{$item_names[$i]}</td>
        <td>{$qtys[$i]}</td>
        <td>{$units[$i]}</td>
        <td>".number_format($prices[$i],2)."</td>
        <td>".number_format($sum,2)."</td>
    </tr>";
}

// แถวรวม
$html .= "
<tr>
    <td colspan='4' style='text-align:center; font-weight:bold; border-top:1px solid #000; border-right:1px solid #000;'>รวมเป็นเงินทั้งสิ้น</td>
    <td colspan='2' style='text-align:right; font-weight:bold; border-top:1px solid #000;'>".number_format($total,2)."</td>
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
