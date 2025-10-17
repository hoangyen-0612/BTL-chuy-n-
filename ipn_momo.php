<?php
header("Content-Type: application/json; charset=UTF-8");
http_response_code(200);

$logFile = __DIR__ . '/momo_ipn_debug.log';
file_put_contents($logFile, date('c')." | ===== IPN HIT =====\n", FILE_APPEND);

$rawInput = file_get_contents("php://input");
file_put_contents($logFile, date('c')." | RAW INPUT: $rawInput\n", FILE_APPEND);

$data = json_decode($rawInput, true);
if (!is_array($data)) {
    echo json_encode(["message"=>"Không đọc được JSON"]);
    exit;
}
$orderIdFull = $data['orderId'] ?? '';
$so_hd       = explode('-', $orderIdFull)[0];   
$resultCode  = (int)($data['resultCode'] ?? -1);
$conn = new mysqli("localhost", "root", "", "quanlyhieuthuoc");
if ($conn->connect_error) {
    file_put_contents($logFile, date('c')." | DB ERROR: ".$conn->connect_error."\n", FILE_APPEND);
    exit;
}

// === Cập nhật trạng thái ===
if ($resultCode === 0 && $so_hd !== '') {
    $stmt = $conn->prepare("UPDATE hoadon SET trang_thai='Đã thanh toán' WHERE so_hd=?");
    $stmt->bind_param("s", $so_hd);
    if ($stmt->execute()) {
        file_put_contents($logFile, date('c')." | UPDATE OK: $so_hd\n", FILE_APPEND);
        echo json_encode(["message" => "Đã cập nhật hóa đơn $so_hd"]);
    } else {
        file_put_contents($logFile, date('c')." | UPDATE FAIL: ".$stmt->error."\n", FILE_APPEND);
        echo json_encode(["message" => "Lỗi khi cập nhật DB"]);
    }
    $stmt->close();
} else {
    file_put_contents($logFile, date('c')." | SKIP: resultCode=$resultCode, so_hd=$so_hd\n", FILE_APPEND);
    echo json_encode(["message" => "Không cập nhật"]);
}

$conn->close();
?>
