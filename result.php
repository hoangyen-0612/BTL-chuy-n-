<?php
header('Content-type: text/html; charset=utf-8');
$secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa'; 
$accessKey = 'klm05TvNBzhg7h7j';
$partnerCode  = $_GET['partnerCode']  ?? '';
$orderId      = $_GET['orderId']      ?? '';
$requestId    = $_GET['requestId']    ?? '';
$amount       = $_GET['amount']       ?? '';
$orderInfo    = $_GET['orderInfo']    ?? '';
$orderType    = $_GET['orderType']    ?? '';
$transId      = $_GET['transId']      ?? '';
$resultCode   = $_GET['resultCode']   ?? '';
$message      = $_GET['message']      ?? '';
$payType      = $_GET['payType']      ?? '';
$responseTime = $_GET['responseTime'] ?? '';
$extraData    = $_GET['extraData']    ?? '';
$m2signature  = $_GET['signature']    ?? '';
$localMessage = $_GET['localMessage'] ?? '';

$rawHash = "accessKey={$accessKey}&amount={$amount}&extraData={$extraData}&message={$message}"
         . "&orderId={$orderId}&orderInfo={$orderInfo}&orderType={$orderType}&partnerCode={$partnerCode}"
         . "&payType={$payType}&requestId={$requestId}&responseTime={$responseTime}"
         . "&resultCode={$resultCode}&transId={$transId}";

$partnerSignature = hash_hmac("sha256", $rawHash, $secretKey);

// ===== Kết quả thanh toán =====
if ($m2signature === $partnerSignature && $resultCode === '0') {
    $status = '<div class="alert alert-success"><strong>Thanh toán thành công!</strong></div>';
} elseif ($m2signature === $partnerSignature) {
    $status = '<div class="alert alert-danger"><strong>Thanh toán thất bại: </strong>'
            . htmlspecialchars($message) . ' / ' . htmlspecialchars($localMessage) . '</div>';
} else {
    $status = '<div class="alert alert-danger"><strong>Lỗi chữ ký:</strong> Dữ liệu không hợp lệ.</div>';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kết quả thanh toán MoMo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css"/>
</head>
<body>
<div class="container" style="margin-top:40px;">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="panel-title">Kết quả thanh toán</h3></div>
                <div class="panel-body">
                    <?php echo $status; ?>
                    <a href="/sdbdb/quanly.php?page=hoa-don" class="btn btn-primary" style="margin-top:20px;">
                        Quay về trang chủ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
