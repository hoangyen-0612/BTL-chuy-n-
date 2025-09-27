<?php
header('Content-type: text/html; charset=utf-8');

function execPostRequest($url, $data, &$curlErr = null, $timeout = 10)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data))
    );
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $result = curl_exec($ch);
    if ($result === false) {
        $curlErr = curl_error($ch);
    }
    curl_close($ch);
    return $result;
}
$endpoint    = "https://test-payment.momo.vn/v2/gateway/api/create";
$partnerCode = 'MOMOBKUN20180529';
$accessKey   = 'klm05TvNBzhg7h7j';
$secretKey   = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
$redirectUrl = "https://unpunctilious-shanice-communionable.ngrok-free.dev/sdbdb/result.php";
$ipnUrl      = "https://unpunctilious-shanice-communionable.ngrok-free.dev/sdbdb/ipn_momo.php";

$defaultAmount = "10000";
$orderInfoDefault = "Thanh toán qua MoMo";
$debug = (isset($_GET['debug']) && $_GET['debug'] == '1');

$errors = [];
$info   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy input và sanitize (ưu tiên POST values)
    $partnerCode = isset($_POST['partnerCode']) ? trim($_POST['partnerCode']) : $partnerCode;
    $accessKey   = isset($_POST['accessKey'])   ? trim($_POST['accessKey'])   : $accessKey;
    // **Ghi chú quan trọng**: đặt đúng tên biến $secretKey
    $secretKey   = isset($_POST['secretKey'])   ? trim($_POST['secretKey'])   : $secretKey;

    $orderIdRaw  = isset($_POST['orderId']) ? trim($_POST['orderId']) : (time() . "");
    $orderInfo   = isset($_POST['orderInfo']) ? trim($_POST['orderInfo']) : $orderInfoDefault;
    $ipnUrl      = isset($_POST['ipnUrl']) ? trim($_POST['ipnUrl']) : $ipnUrl;
    $redirectUrl = isset($_POST['redirectUrl']) ? trim($_POST['redirectUrl']) : $redirectUrl;
    $extraData   = isset($_POST['extraData']) ? trim($_POST['extraData']) : "";
    $rawAmount = isset($_POST['amount']) ? trim($_POST['amount']) : $defaultAmount;
    $clean = preg_replace('/[^\d\.]/', '', $rawAmount);
    $floatAmount = ($clean === '') ? 0.0 : floatval($clean);
 
    $amount = (int)ceil($floatAmount);
    if ($amount < 1000) {
        $errors[] = "Số tiền phải >= 1.000 VND. (Đã tự thiết lập về 1000 khi gửi).";
        $amount = 1000;
    }
    if ($amount > 50000000) {
        $errors[] = "Số tiền không được lớn hơn 50.000.000 VND. (Đã giới hạn về 50,000,000 khi gửi).";
        $amount = 50000000;
    }

    $requestId   = time() . "";
    $requestType = "captureWallet";
    $orderId = ($orderIdRaw !== '') ? $orderIdRaw : ($requestId);
    $rawHash = "accessKey=" . $accessKey .
               "&amount=" . $amount .
               "&extraData=" . $extraData .
               "&ipnUrl=" . $ipnUrl .
               "&orderId=" . $orderId .
               "&orderInfo=" . $orderInfo .
               "&partnerCode=" . $partnerCode .
               "&redirectUrl=" . $redirectUrl .
               "&requestId=" . $requestId .
               "&requestType=" . $requestType;

    $signature = hash_hmac("sha256", $rawHash, $secretKey);

    $data = [
        'partnerCode' => $partnerCode,
        'partnerName' => 'Test',
        'storeId'     => 'MomoTestStore',
        'requestId'   => $requestId,
        'amount'      => (string)$amount,   
        'orderId'     => $orderId,
        'orderInfo'   => $orderInfo,
        'redirectUrl' => $redirectUrl,
        'ipnUrl'      => $ipnUrl,
        'lang'        => 'vi',
        'extraData'   => $extraData,
        'requestType' => $requestType,
        'signature'   => $signature
    ];

    // Gửi request
    $curlErr = null;
    $jsonSent = json_encode($data, JSON_UNESCAPED_UNICODE);
    $result = execPostRequest($endpoint, $jsonSent, $curlErr, 15);

    if ($curlErr) {
        $errors[] = "Lỗi CURL: " . $curlErr;
    }

    if ($result === false || $result === null) {
        $errors[] = "Không nhận được phản hồi từ MoMo.";
        if ($debug) $info[] = "Request payload: " . $jsonSent;
    } else {
        $jsonResult = json_decode($result, true);
        if ($jsonResult === null) {
            $errors[] = "Không giải mã được JSON trả về từ MoMo.";
            if ($debug) {
                $info[] = "Raw response: " . $result;
                $info[] = "Request payload: " . $jsonSent;
            }
        } else {
            // Nếu payUrl tồn tại -> redirect
            if (!empty($jsonResult['payUrl'])) {
                header('Location: ' . $jsonResult['payUrl']);
                exit();
            } else {
                // Hiển thị lỗi trả về từ MoMo
                $errors[] = "MoMo trả về lỗi:";
                $info[] = print_r($jsonResult, true);
                if ($debug) {
                    $info[] = "RawHash: " . $rawHash;
                    $info[] = "Signature: " . $signature;
                    $info[] = "Request payload: " . $jsonSent;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<title>MoMo Sandbox - Init Payment (Sửa lỗi)</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css"/>
<style>
body { padding:20px; font-family: Arial, sans-serif; }
.panel { max-width:900px; margin:0 auto; }
.alert-pre { white-space: pre-wrap; font-family: monospace; }
</style>
</head>
<body>
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading"><h3 class="panel-title">Tạo yêu cầu thanh toán MoMo (Sandbox)</h3></div>
        <div class="panel-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach($errors as $e) echo "<div>".htmlspecialchars($e)."</div>"; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($info) && $debug): ?>
                <div class="alert alert-info">
                    <strong>Debug info:</strong>
                    <div class="alert-pre"><?php echo htmlspecialchars(implode("\n\n", $info)); ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>PartnerCode</label>
                        <input class="form-control" name="partnerCode" value="<?php echo htmlspecialchars($partnerCode); ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>AccessKey</label>
                        <input class="form-control" name="accessKey" value="<?php echo htmlspecialchars($accessKey); ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>SecretKey</label>
                        <input class="form-control" name="secretKey" value="<?php echo htmlspecialchars($secretKey); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>OrderId</label>
                        <input class="form-control" name="orderId" value="<?php echo isset($orderId) ? htmlspecialchars($orderId) : time(); ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>OrderInfo</label>
                        <input class="form-control" name="orderInfo" value="<?php echo htmlspecialchars($orderInfoDefault); ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Amount (VND)</label>
                        <input class="form-control" name="amount" value="<?php echo isset($amount) ? htmlspecialchars($amount) : $defaultAmount; ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>IpnUrl</label>
                        <input class="form-control" name="ipnUrl" value="<?php echo htmlspecialchars($ipnUrl); ?>">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>RedirectUrl</label>
                        <input class="form-control" name="redirectUrl" value="<?php echo htmlspecialchars($redirectUrl); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>ExtraData (optional)</label>
                    <input class="form-control" name="extraData" value="<?php echo isset($extraData) ? htmlspecialchars($extraData) : ''; ?>">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Start MoMo payment</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
