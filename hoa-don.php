<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

include("connect.php");
mysqli_set_charset($conn, "utf8");
$momoConfig = include 'config.php';
//(THÊM / SỬA)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $soHD    = $_POST['so_hd'] ?? '';
    $ngayBan = $_POST['ngay_ban'] ?? '';
    $tenKhach= $_POST['ten_khach'] ?? '';
    $maThuoc = $_POST['ma_thuoc'] ?? '';
    $soLuong = intval($_POST['so_luong_ban'] ?? 0);
    $giaBan  = floatval($_POST['gia_ban'] ?? 0);
    $phuongThuc = $_POST['phuong_thuc_thanh_toan'] ?? 'Tiền mặt';
    $maNV    = $_POST['ma_nhan_vien'] ?? '';
    $maKho   = $_POST['ma_kho'] ?? '';
    $trangThai = ($phuongThuc === 'Tiền mặt') ? ($_POST['trang_thai'] ?? 'Chưa thanh toán') : 'Chưa thanh toán';

    // Lấy thành tiền: ưu tiên raw (hidden), nếu không có -> tính lại từ server
    if (isset($_POST['thanh_tien_ban_raw']) && $_POST['thanh_tien_ban_raw'] !== '') {
        $thanhTien = (float) preg_replace('/[^\d.]/', '', $_POST['thanh_tien_ban_raw']);
    } else {
        $thanhTien = $giaBan * $soLuong;
    }
    $amount = (int) ceil($thanhTien);

    // debug log (ghi file để dễ kiểm tra)
    file_put_contents(__DIR__ . '/debug_momo.log',
        date('Y-m-d H:i:s') . " | POST_ACTION={$action} | soHD={$soHD} | giaBan={$giaBan} | soLuong={$soLuong} | thanhTien={$thanhTien} | amount={$amount}\n",
        FILE_APPEND
    );

    // --------- THÊM ----------
    if ($action === 'them') {
        if (empty($ngayBan)) $ngayBan = date('Y-m-d');

        // tạo so_hd
        $res = $conn->query("SELECT so_hd FROM hoadon ORDER BY so_hd DESC LIMIT 1");
        $soHD = ($res && $res->num_rows>0) ?
                "HD".str_pad((int)substr($res->fetch_assoc()['so_hd'],2)+1,3,"0",STR_PAD_LEFT) :
                "HD001";

        // insert
        $stmt = $conn->prepare("INSERT INTO hoadon
            (so_hd, ngay_ban, ten_khach, ma_thuoc, so_luong_ban, gia_ban,
             thanh_tien_ban, phuong_thuc_thanh_toan, trang_thai, ma_nhan_vien, ma_kho)
            VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssiddssss", $soHD, $ngayBan, $tenKhach, $maThuoc,
                          $soLuong, $giaBan, $thanhTien, $phuongThuc,
                          $trangThai, $maNV, $maKho);
        $stmt->execute();
        $stmt->close();

        // cập nhật tồn kho
        $conn->query("UPDATE kho
                      SET sl_giao = sl_giao + $soLuong,
                          ton_kho = sl_nhap - sl_giao
                      WHERE ma_kho = '". $conn->real_escape_string($maKho) ."'");

        // Nếu chọn Chuyển khoản -> Gọi MoMo
        if ($phuongThuc === 'Chuyển khoản') {
            // kiểm tra giới hạn MoMo
            if ($amount < 1000 || $amount > 50000000) {
                echo "<script>alert('Số tiền thanh toán phải từ 1.000 đến 50.000.000 VND.');window.location='hoa-don.php';</script>";
                exit();
            }
            $redirectUrl = $momoConfig['redirectUrl'] ?? null;
            $ipnUrl      = $momoConfig['ipnUrl'] ?? null;
            if (empty($redirectUrl) || empty($ipnUrl)) {
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT']==443) ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $base = $protocol . '://' . $host . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/';
                if (empty($redirectUrl)) $redirectUrl = $base . 'result.php';
                if (empty($ipnUrl))      $ipnUrl      = $base . 'ipn_momo.php';
                file_put_contents(__DIR__ . '/debug_momo.log',
                    date('Y-m-d H:i:s') . " | WARNING: fallback redirect/ipn => $redirectUrl / $ipnUrl\n",
                    FILE_APPEND
                );
            }

            // payload
            $endpoint    = $momoConfig['endpoint'] ?? 'https://test-payment.momo.vn/v2/gateway/api/create';
            $orderInfo   = "Thanh toán hóa đơn $soHD";
            $orderId     = $soHD . '-' . time();
            $requestId   = time()."";
            $requestType = "captureWallet";
            $extraData   = "";

            $rawHash = "accessKey=".$momoConfig['accessKey'].
                       "&amount=".$amount.
                       "&extraData=".$extraData.
                       "&ipnUrl=".$ipnUrl.
                       "&orderId=".$orderId.
                       "&orderInfo=".$orderInfo.
                       "&partnerCode=".$momoConfig['partnerCode'].
                       "&redirectUrl=".$redirectUrl.
                       "&requestId=".$requestId.
                       "&requestType=".$requestType;

            $signature = hash_hmac("sha256", $rawHash, $momoConfig['secretKey']);

            $data = [
                'partnerCode' => $momoConfig['partnerCode'],
                'partnerName' => 'Test',
                'storeId'     => 'TestStore',
                'requestId'   => $requestId,
                'amount'      => $amount,
                'orderId'     => $orderId,
                'orderInfo'   => $orderInfo,
                'redirectUrl' => $redirectUrl,
                'ipnUrl'      => $ipnUrl,
                'lang'        => 'vi',
                'extraData'   => $extraData,
                'requestType' => $requestType,
                'signature'   => $signature
            ];

            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS    => json_encode($data),
                CURLOPT_RETURNTRANSFER=> true,
                CURLOPT_HTTPHEADER    => ['Content-Type: application/json']
            ]);
            $result = curl_exec($ch);
            curl_close($ch);
            $jsonResult = json_decode($result, true);

            // log 
            file_put_contents(__DIR__ . '/debug_momo.log',
                date('Y-m-d H:i:s') . " | MOMO_RESPONSE | " . print_r($jsonResult, true) . "\n",
                FILE_APPEND
            );

            if (empty($jsonResult['payUrl'])) {
                echo "<h3>MoMo trả về lỗi:</h3><pre>".print_r($jsonResult, true)."</pre>";
                exit();
            }
            header('Location: '.$jsonResult['payUrl']);
            exit();
        }
       header("Location:quanly.php?page=hoa-don");
        exit();
    }

    // --------- SỬA ----------
    if ($action === 'sua') {
        if (empty($thanhTien)) $thanhTien = $giaBan * $soLuong;
        $old = $conn->query("SELECT so_luong_ban, ma_kho FROM hoadon WHERE so_hd='". $conn->real_escape_string($soHD) ."'")->fetch_assoc();
        $oldSL  = (int)$old['so_luong_ban'];
        $oldKho = $old['ma_kho'];

        $stmt = $conn->prepare("UPDATE hoadon
            SET ngay_ban=?, ten_khach=?, ma_thuoc=?, so_luong_ban=?, gia_ban=?,
                thanh_tien_ban=?, phuong_thuc_thanh_toan=?, trang_thai=?, ma_nhan_vien=?, ma_kho=?
            WHERE so_hd=?");
        $stmt->bind_param("sssiddsssss", $ngayBan, $tenKhach, $maThuoc,
                          $soLuong, $giaBan, $thanhTien,
                          $phuongThuc, $trangThai, $maNV, $maKho, $soHD);
        $stmt->execute();
        $stmt->close();

        // cập nhật tồn kho
        if ($oldKho !== $maKho) {
            $conn->query("UPDATE kho
                          SET sl_giao = sl_giao - $oldSL,
                              ton_kho = sl_nhap - sl_giao
                          WHERE ma_kho = '". $conn->real_escape_string($oldKho) ."'");
            $conn->query("UPDATE kho
                          SET sl_giao = sl_giao + $soLuong,
                              ton_kho = sl_nhap - sl_giao
                          WHERE ma_kho = '". $conn->real_escape_string($maKho) ."'");
        } else {
            $diff = $soLuong - $oldSL;
            $conn->query("UPDATE kho
                          SET sl_giao = sl_giao + $diff,
                              ton_kho = sl_nhap - sl_giao
                          WHERE ma_kho = '". $conn->real_escape_string($maKho) ."'");
        }

       header("Location:quanly.php?page=hoa-don");
        exit();
    }
}

// ====================== XỬ LÝ XÓA ======================
if (isset($_GET['xoa'])) {
    $soHD = $_GET['xoa'];
    $row = $conn->query("SELECT so_luong_ban, ma_kho FROM hoadon WHERE so_hd='". $conn->real_escape_string($soHD) ."'")->fetch_assoc();
    if ($row) {
        $sl  = (int)$row['so_luong_ban'];
        $kho = $row['ma_kho'];
        $stmt = $conn->prepare("DELETE FROM hoadon WHERE so_hd=?");
        $stmt->bind_param("s", $soHD);
        $stmt->execute();
        $stmt->close();
        $conn->query("UPDATE kho
                      SET sl_giao = sl_giao - $sl,
                          ton_kho = sl_nhap - sl_giao
                      WHERE ma_kho = '". $conn->real_escape_string($kho) ."'");
    }
    header("Location:quanly.php?page=hoa-don");
    exit();
}

$thuocAll = [];
$thuocRes = $conn->query("SELECT ma_thuoc, ten_thuoc, ma_kho, gia_ban FROM thuoc");
while ($t = $thuocRes->fetch_assoc()) $thuocAll[] = $t;

$nvAll = [];
$nvRes = $conn->query("SELECT ma_nhan_vien, ten_nhan_vien FROM nhanvien");
while ($n = $nvRes->fetch_assoc()) $nvAll[] = $n;

$result = $conn->query("SELECT h.*, t.ten_thuoc, n.ten_nhan_vien
                        FROM hoadon h
                        LEFT JOIN thuoc t ON h.ma_thuoc=t.ma_thuoc
                        LEFT JOIN nhanvien n ON h.ma_nhan_vien=n.ma_nhan_vien
                        ORDER BY h.so_hd ASC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý hóa đơn</title>
<link rel="stylesheet" href="css.css"> 
<style>
</style>
</head>
<body>
<div id="hoa-don" class="page">
    <div class="page-header">
        <h1 class="page-title">Quản lý hóa đơn</h1>
        <p class="page-subtitle">Danh sách hóa đơn bán thuốc</p>
    </div>

    <div class="table-container">
        <div class="table-header">
            <div class="search-filters">
                <input type="text" id="searchInput" class="search-input" placeholder="Tìm kiếm..." onkeyup="timKiem()">
            </div>
            <button class="btn btn-primary" onclick="moModal('modal-them-hoa-don')"> Thêm hóa đơn</button>
        </div>

        <table id="hoaDonTable">
            <thead>
                <tr>
                    <th>Số HĐ</th>
                    <th>Ngày bán</th>
                    <th>Khách hàng</th>
                    <th>Thuốc</th>
                    <th>SL</th>
                    <th>Giá bán</th>
                    <th>Thành tiền</th>
                    <th>Nhân viên</th>
                    <th>Kho</th>
                    <th>PTTT</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row=$result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['so_hd']) ?></td>
                    <td><?= htmlspecialchars($row['ngay_ban']) ?></td>
                    <td><?= htmlspecialchars($row['ten_khach']) ?></td>
                    <td><?= htmlspecialchars($row['ten_thuoc']) ?></td>
                    <td><?= htmlspecialchars($row['so_luong_ban']) ?></td>
                    <td><?= number_format($row['gia_ban']) ?> VNĐ</td>
                    <td><?= number_format($row['thanh_tien_ban']) ?> VNĐ</td>
                    <td><?= htmlspecialchars($row['ten_nhan_vien']) ?></td>
                    <td><?= htmlspecialchars($row['ma_kho']) ?></td>
                    <td><?= htmlspecialchars($row['phuong_thuc_thanh_toan']) ?></td>
                    <td><?= htmlspecialchars($row['trang_thai']) ?></td>
                    <td>
                        <button class="btn btn-info btn-sm"
                            onclick="suaHD('<?= addslashes($row['so_hd'])?>','<?= addslashes($row['ngay_ban'])?>','<?= addslashes($row['ten_khach'])?>','<?= addslashes($row['ma_thuoc'])?>','<?= addslashes($row['so_luong_ban'])?>','<?= addslashes($row['gia_ban'])?>','<?= addslashes($row['phuong_thuc_thanh_toan'])?>','<?= addslashes($row['ma_nhan_vien'])?>','<?= addslashes($row['ma_kho'])?>')"> Sửa</button>

                        <a class="btn btn-danger btn-sm"
                           href="hoa-don.php?xoa=<?= urlencode($row['so_hd'])?>"
                           onclick="return confirm('Xóa hóa đơn này?')"> Xóa</a>

                        <button class="btn btn-info btn-sm"
                            onclick="inHoaDon('<?= addslashes($row['so_hd'])?>','<?= addslashes($row['ngay_ban'])?>','<?= addslashes($row['ten_khach'])?>','<?= addslashes($row['ten_thuoc'])?>','<?= addslashes($row['so_luong_ban'])?>','<?= addslashes($row['gia_ban'])?>','<?= addslashes($row['thanh_tien_ban'])?>','<?= addslashes($row['phuong_thuc_thanh_toan'])?>','<?= addslashes($row['ten_nhan_vien'])?>','<?= addslashes($row['ma_kho'])?>')">In</button>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal thêm hóa đơn -->
<div id="modal-them-hoa-don" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Tạo hóa đơn</h3>
            <button class="close-btn" onclick="dongModal('modal-them-hoa-don')">&times;</button>
        </div>
        <form method="post" id="form_them" onsubmit="return validateBeforeSubmit('them_')">
            <input type="hidden" name="action" value="them">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Ngày bán</label>
                    <input type="date" class="form-input" name="ngay_ban" id="them_ngay_ban" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tên khách</label>
                    <input type="text" class="form-input" name="ten_khach" id="them_ten_khach" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Thuốc</label>
                <select class="form-select" name="ma_thuoc" id="them_ma_thuoc" required onchange="chonThuoc(this,'them_')">
                    <option value="">Chọn thuốc</option>
                    <?php foreach($thuocAll as $t):
                        $ton = $conn->query("SELECT ton_kho FROM kho WHERE ma_kho='".$conn->real_escape_string($t['ma_kho'])."'")->fetch_assoc()['ton_kho'] ?? 0;
                        $disabled = ($ton <= 0) ? 'disabled' : '';
                    ?>
                    <option value="<?= htmlspecialchars($t['ma_thuoc'])?>"
                            data-kho="<?= htmlspecialchars($t['ma_kho'])?>"
                            data-gia="<?= htmlspecialchars($t['gia_ban'])?>"
                            <?= $disabled ?>>
                        <?= htmlspecialchars($t['ten_thuoc'])?> (<?= $ton > 0 ? "Còn: $ton" : "Hết hàng" ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Số lượng bán</label>
                    <input type="number" class="form-input" name="so_luong_ban" id="them_so_luong_ban" required oninput="capNhatThanhTien('them_')" min="1">
                </div>
                <div class="form-group">
                    <label class="form-label">Giá bán (VNĐ)</label>
                    <input type="number" step="0.01" class="form-input" name="gia_ban" id="them_gia_ban" required oninput="capNhatThanhTien('them_')" min="0">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Thành tiền (VNĐ)</label>
                <input type="text" class="form-input" name="thanh_tien_ban" id="them_thanh_tien" readonly>
                <input type="hidden" name="thanh_tien_ban_raw" id="them_thanh_tien_raw">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Phương thức thanh toán</label>
                    <select class="form-select" name="phuong_thuc_thanh_toan" id="them_phuong_thuc_thanh_toan">
                        <option>Tiền mặt</option>
                        <option>Chuyển khoản</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Nhân viên</label>
                    <select class="form-select" name="ma_nhan_vien" id="them_ma_nhan_vien">
                        <?php foreach($nvAll as $nv): ?>
                            <option value="<?= htmlspecialchars($nv['ma_nhan_vien'])?>"><?= htmlspecialchars($nv['ten_nhan_vien'])?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group" id="group_trang_thai_them">
                <label class="form-label">Trạng thái</label>
                <select class="form-select" name="trang_thai">
                    <option>Chưa thanh toán</option>
                    <option>Đã thanh toán</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Mã kho</label>
                <input type="text" class="form-input" name="ma_kho" id="them_ma_kho" readonly>
            </div>
            <div class="form-actions">
                <button type="button" class="btn" onclick="dongModal('modal-them-hoa-don')">Hủy</button>
                <button type="submit" class="btn btn-primary">Tạo hóa đơn & QR thanh toán</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal sửa hóa đơn -->
<div id="modal-sua-hoa-don" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Sửa hóa đơn</h3>
            <button class="close-btn" onclick="dongModal('modal-sua-hoa-don')">&times;</button>
        </div>
        <form method="post" id="form_sua" onsubmit="return validateBeforeSubmit('sua_')">
            <input type="hidden" name="action" id="action_sua" value="sua">
            <input type="hidden" name="so_hd" id="so_hd">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Ngày bán</label>
                    <input type="date" class="form-input" name="ngay_ban" id="sua_ngay_ban" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tên khách</label>
                    <input type="text" class="form-input" name="ten_khach" id="sua_ten_khach" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Thuốc</label>
                <select class="form-select" name="ma_thuoc" id="sua_ma_thuoc" required onchange="chonThuoc(this,'sua_')">
                    <option value="">Chọn thuốc</option>
                    <?php foreach($thuocAll as $t): ?>
                        <option value="<?= htmlspecialchars($t['ma_thuoc'])?>" data-kho="<?= htmlspecialchars($t['ma_kho'])?>" data-gia="<?= htmlspecialchars($t['gia_ban'])?>"><?= htmlspecialchars($t['ten_thuoc'])?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Số lượng bán</label>
                    <input type="number" class="form-input" name="so_luong_ban" id="sua_so_luong_ban" required oninput="capNhatThanhTien('sua_')" min="1">
                </div>
                <div class="form-group">
                    <label class="form-label">Giá bán (VNĐ)</label>
                    <input type="number" step="0.01" class="form-input" name="gia_ban" id="sua_gia_ban" required oninput="capNhatThanhTien('sua_')" min="0">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Thành tiền (VNĐ)</label>
                <input type="text" class="form-input" name="thanh_tien_ban" id="sua_thanh_tien" readonly>
                <input type="hidden" name="thanh_tien_ban_raw" id="sua_thanh_tien_raw">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Phương thức thanh toán</label>
                    <select class="form-select" name="phuong_thuc_thanh_toan" id="sua_phuong_thuc_thanh_toan">
                        <option>Tiền mặt</option>
                        <option>Chuyển khoản</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Nhân viên</label>
                    <select class="form-select" name="ma_nhan_vien" id="sua_ma_nhan_vien">
                        <?php foreach($nvAll as $nv): ?>
                            <option value="<?= htmlspecialchars($nv['ma_nhan_vien'])?>"><?= htmlspecialchars($nv['ten_nhan_vien'])?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group" id="group_trang_thai_sua">
                <label class="form-label">Trạng thái</label>
                <select class="form-select" name="trang_thai" id="sua_trang_thai">
                    <option>Chưa thanh toán</option>
                    <option>Đã thanh toán</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Mã kho</label>
                <input type="text" class="form-input" name="ma_kho" id="sua_ma_kho" readonly>
            </div>
            <div class="form-actions">
                <button type="button" class="btn" onclick="dongModal('modal-sua-hoa-don')">Hủy</button>
                <button type="submit" class="btn btn-primary">Cập nhật hóa đơn</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleTrangThai(prefix){
        const pttt = document.getElementById(prefix+'phuong_thuc_thanh_toan') || document.querySelector('[name="phuong_thuc_thanh_toan"]');
        const grp  = document.getElementById('group_trang_thai_'+prefix);
        if(pttt && grp){
            grp.style.display = (pttt.value === 'Tiền mặt') ? 'block':'none';
        }
    }

    // Gán sự kiện cho select sửa
    document.addEventListener('DOMContentLoaded', function(){
        let el = document.getElementById('sua_phuong_thuc_thanh_toan');
        if (el) el.addEventListener('change', ()=>toggleTrangThai('sua_'));
        let btns = document.querySelectorAll("[onclick*=\"moModal('modal-them-hoa-don')\"]");
    });

    // Modal
    function moModal(id){
        document.getElementById(id).style.display='flex';
        if (id === 'modal-them-hoa-don') {
            let today = new Date().toISOString().split('T')[0];
            let el = document.getElementById('them_ngay_ban');
            if (el) el.value = today;
            if (document.getElementById('them_so_luong_ban')) document.getElementById('them_so_luong_ban').value=1;
            if (document.getElementById('them_gia_ban')) document.getElementById('them_gia_ban').value='';
            if (document.getElementById('them_thanh_tien')) document.getElementById('them_thanh_tien').value='';
            if (document.getElementById('them_thanh_tien_raw')) document.getElementById('them_thanh_tien_raw').value='';
            if (document.getElementById('them_ma_kho')) document.getElementById('them_ma_kho').value='';
            if (document.getElementById('them_ma_thuoc')) document.getElementById('them_ma_thuoc').value='';
            if (document.getElementById('them_ten_khach')) document.getElementById('them_ten_khach').value='';
        }
    }
    function dongModal(id){ document.getElementById(id).style.display='none'; }

    // Tìm kiếm
    function timKiem(){
        let kw = document.getElementById("searchInput").value.toLowerCase();
        document.querySelectorAll("#hoaDonTable tbody tr").forEach(row=>{
            row.style.display = row.textContent.toLowerCase().includes(kw) ? "" : "none";
        });
    }

    // Chọn thuốc tự động cập nhật mã kho + giá bán
    function chonThuoc(sel, prefix){
        let opt = sel.options[sel.selectedIndex];
        let kho = opt ? opt.getAttribute('data-kho') : '';
        let gia = opt ? opt.getAttribute('data-gia') : '';
        if (document.getElementById(prefix + 'ma_kho')) document.getElementById(prefix + 'ma_kho').value = kho;
        if (document.getElementById(prefix + 'gia_ban')) document.getElementById(prefix + 'gia_ban').value = gia;
        capNhatThanhTien(prefix);
    }

    // Cập nhật thành tiền
    function capNhatThanhTien(prefix){
        let slEl = document.getElementById(prefix + 'so_luong_ban');
        let giaEl = document.getElementById(prefix + 'gia_ban');
        let thanhEl = document.getElementById(prefix + 'thanh_tien');
        let rawEl = document.getElementById(prefix + 'thanh_tien_raw');
        let sl = slEl ? parseFloat(slEl.value || 0) : 0;
        let gia = giaEl ? parseFloat(giaEl.value || 0) : 0;
        let raw = Math.round(sl * gia);
        if (rawEl) rawEl.value = raw;
        if (thanhEl) thanhEl.value = raw.toLocaleString();
    }
    function validateBeforeSubmit(prefix){
        let ptttEl = document.getElementById(prefix + 'phuong_thuc_thanh_toan') || document.querySelector('[name="phuong_thuc_thanh_toan"]');
        let rawEl = document.getElementById(prefix + 'thanh_tien_raw');
        let slEl = document.getElementById(prefix + 'so_luong_ban');
        let giaEl = document.getElementById(prefix + 'gia_ban');

        let sl = slEl ? Number(slEl.value||0) : 0;
        let gia = giaEl ? Number(giaEl.value||0) : 0;
        let amount = rawEl ? Number(rawEl.value||0) : (sl * gia);

        if (sl <= 0) { alert('Nhập số lượng > 0'); return false; }
        if (gia <= 0) { alert('Nhập giá bán hợp lệ'); return false; }

        if (ptttEl && ptttEl.value === 'Chuyển khoản') {
            if (amount < 1000 || amount > 50000000) {
                alert('Số tiền thanh toán phải từ 1.000 đến 50.000.000 VND.');
                return false;
            }
        }
        return true;
    }

    // Sửa hóa đơn
    function suaHD(so_hd, ngay, khach, maThuoc, sl, gia, pttt, maNV, maKho){
        moModal('modal-sua-hoa-don');
        document.getElementById('action_sua').value = 'sua';
        document.getElementById('so_hd').value = so_hd;
        document.getElementById('sua_ngay_ban').value = ngay;
        document.getElementById('sua_ten_khach').value = khach;
        document.getElementById('sua_ma_thuoc').value = maThuoc;
        document.getElementById('sua_so_luong_ban').value = sl;
        document.getElementById('sua_gia_ban').value = gia;
        document.getElementById('sua_phuong_thuc_thanh_toan').value = pttt;
        document.getElementById('sua_ma_nhan_vien').value = maNV;
        document.getElementById('sua_ma_kho').value = maKho;
        // tính và hiển thị thành tiền (cập nhật cả raw)
        let thanh = Math.round((parseFloat(sl || 0) * parseFloat(gia || 0)));
        if (document.getElementById('sua_thanh_tien')) document.getElementById('sua_thanh_tien').value = thanh.toLocaleString();
        if (document.getElementById('sua_thanh_tien_raw')) document.getElementById('sua_thanh_tien_raw').value = thanh;
    }

    // In hóa đơn
    function inHoaDon(soHD, ngay, khach, thuoc, sl, gia, tien, pttt, nv, kho){
        let html = `<html><head><title>Hóa đơn ${soHD}</title>
        <style>body{font-family:Arial;padding:20px;}h2{text-align:center;}table{width:100%;border-collapse:collapse;margin-top:20px;}table,th,td{border:1px solid #333;}th,td{padding:8px;text-align:center;} .info{margin-top:10px;}</style>
        </head><body>
        <h2>HÓA ĐƠN BÁN THUỐC</h2>
        <div class="info"><b>Số HĐ:</b> ${soHD}</div>
        <div class="info"><b>Ngày bán:</b> ${ngay}</div>
        <div class="info"><b>Khách hàng:</b> ${khach}</div>
        <div class="info"><b>Nhân viên:</b> ${nv}</div>
        <div class="info"><b>Kho:</b> ${kho}</div>
        <table>
            <tr><th>Thuốc</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr>
            <tr><td>${thuoc}</td><td>${sl}</td><td>${gia}</td><td>${tien}</td></tr>
        </table>
        <div class="info"><b>PT Thanh toán:</b> ${pttt}</div>
        <h3 style="text-align:right;margin-top:20px;">Tổng cộng: ${tien} VNĐ</h3>
        </body></html>`;
        let win=window.open("","PrintWindow"); win.document.write(html); win.document.close(); win.print();
    }
</script>
</body>
</html>
