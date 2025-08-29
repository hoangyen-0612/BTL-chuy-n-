<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <link rel="stylesheet" href="css.css">
</head>
<body>
<?php
include("connect.php");

// Đếm tổng số thuốc
$sql_thuoc = "SELECT COUNT(*) AS tong_thuoc FROM thuoc";
$result_thuoc = $conn->query($sql_thuoc);
$tong_thuoc = $result_thuoc->fetch_assoc()['tong_thuoc'] ?? 0;

// Đếm tổng tồn kho 
$sql = "SELECT SUM(ton_kho) AS tong_ton FROM kho";
$result =$conn->query($sql);
$row = $result->fetch_assoc();
$tongTon = $row['tong_ton'] ?? 0;  // Nếu NULL thì đặt là 0
   
// Hóa đơn hôm nay
$today = date("Y-m-d");
$sql_hoadon = "SELECT COUNT(so_hd) AS hoa_don_hom_nay FROM hoadon WHERE DATE(ngay_ban) = '$today'";
$result_hd = $conn->query($sql_hoadon);
$hoa_don_hom_nay = $result_hd->fetch_assoc()['hoa_don_hom_nay'] ?? 0;

// Thuốc sắp hết hạn (hạn < 30 ngày nữa)
$sql_expire = "SELECT * 
               FROM thuoc 
               WHERE han_su_dung <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
               ORDER BY han_su_dung ASC";
$result_expire = $conn->query($sql_expire);
$sap_het_han = $result_expire->num_rows;
?>

<div class="page-header">
    <h1 class="page-title">Trang chủ</h1>
    <p class="page-subtitle">Tổng quan hệ thống quản lý hiệu thuốc</p>
</div>

<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-content">
            <div class="stat-icon blue">💊</div>
            <div class="stat-info">
                <h3>Tổng số thuốc</h3>
                <p><?= $tong_thuoc ?></p>
            </div>
        </div>
    </div>
    <div class="stat-card green">
        <div class="stat-content">
            <div class="stat-icon green">📦</div>
            <div class="stat-info">
                <h3>Tồn kho</h3>
                <p><?= $tongTon ?></p>
            </div>
        </div>
    </div>
    <div class="stat-card red">
        <div class="stat-content">
            <div class="stat-icon red">🧾</div>
            <div class="stat-info">
                <h3>Hóa đơn hôm nay</h3>
                <p><?= $hoa_don_hom_nay ?></p>
            </div>
        </div>
    </div>
    <div class="stat-card yellow">
        <div class="stat-content">
            <div class="stat-icon yellow">⚠️</div>
            <div class="stat-info">
                <h3>Sắp hết hạn</h3>
                <p><?= $sap_het_han ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Bảng thuốc sắp hết hạn -->
<div class="table-container">
    <h2>⚠️ Danh sách thuốc sắp hết hạn (dưới 30 ngày)</h2>
    <table border="1" width="100%" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th>Mã thuốc</th>
                <th>Tên thuốc</th>
                <th>Nhà sản xuất</th>
                <th>Đơn vị tính</th>
                <th>Giá bán</th>
                <th>Hạn sử dụng</th>
                <th>Hoạt chất</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($sap_het_han > 0) {
                while ($thuoc = $result_expire->fetch_assoc()) { ?>
                <tr>
                    <td><?= $thuoc['ma_thuoc'] ?></td>
                    <td><?= $thuoc['ten_thuoc'] ?></td>
                    <td><?= $thuoc['nha_san_xuat'] ?></td>
                    <td><?= $thuoc['don_vi_tinh'] ?? '-' ?></td>
                    <td><?= number_format($thuoc['gia_ban'],0,',','.') ?> đ</td>
                    <td style="color:red; font-weight:bold;"><?= $thuoc['han_su_dung'] ?></td>
                    <td><?= $thuoc['hoat_chat'] ?></td>
                </tr>
            <?php } 
            } else { ?>
                <tr>
                    <td colspan="7" style="text-align:center;">✅ Không có thuốc nào sắp hết hạn</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>

