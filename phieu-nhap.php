<?php
include("connect.php");

// Xử lý thêm, sửa, xóa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    $soDon = $_POST['so_don_mua'] ?? '';
    $ngayMua = $_POST['ngay_mua'] ?? '';
    $maNCC = $_POST['ma_nha_cung_cap'] ?? '';
    $maThuoc = $_POST['ma_thuoc'] ?? '';
    $loaiThuoc = $_POST['loai_thuoc'] ?? '';
    $soLuong = intval($_POST['so_luong'] ?? 0);
    $giaMua = floatval($_POST['gia_mua'] ?? 0);
    $thanhTien = $soLuong * $giaMua;
    $trangThai = $_POST['trang_thai'] ?? 'Chờ xử lý';
    $ghiChu = $_POST['ghi_chu'] ?? '';

    if ($action == 'them') {
        $stmt = $conn->prepare("INSERT INTO donmua 
            (so_don_mua, ngay_mua, ma_nha_cung_cap, ma_thuoc, loai_thuoc, so_luong, gia_mua, thanh_tien, trang_thai, ghi_chu) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssiddss", $soDon, $ngayMua, $maNCC, $maThuoc, $loaiThuoc,
                          $soLuong, $giaMua, $thanhTien, $trangThai, $ghiChu);
        $stmt->execute();
    }

    if ($action == 'sua') {
        $stmt = $conn->prepare("UPDATE donmua 
            SET ngay_mua=?, ma_nha_cung_cap=?, ma_thuoc=?, loai_thuoc=?, so_luong=?, gia_mua=?, thanh_tien=?, trang_thai=?, ghi_chu=? 
            WHERE so_don_mua=?");
        $stmt->bind_param("ssssiddsss", $ngayMua, $maNCC, $maThuoc, $loaiThuoc,
                          $soLuong, $giaMua, $thanhTien, $trangThai, $ghiChu, $soDon);
        $stmt->execute();
    }
}

if (isset($_GET['xoa'])) {
    $soDon = $_GET['xoa'];
    $stmt = $conn->prepare("DELETE FROM donmua WHERE so_don_mua=?");
    $stmt->bind_param("s", $soDon);
    $stmt->execute();
}

// Lấy danh sách đơn mua (JOIN để hiển thị tên NCC, tên thuốc)
$sql = "SELECT dm.*, ncc.ten_nha_cung_cap, t.ten_thuoc 
        FROM donmua dm
        JOIN nhacungcap ncc ON dm.ma_nha_cung_cap = ncc.ma_nha_cung_cap
        JOIN thuoc t ON dm.ma_thuoc = t.ma_thuoc
        ORDER BY dm.so_don_mua DESC";
$result = $conn->query($sql);

// Lấy danh sách NCC + thuốc
$ncc_list = $conn->query("SELECT * FROM nhacungcap");
$thuoc_list = $conn->query("SELECT * FROM thuoc");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đơn mua</title>
    <link rel="stylesheet" href="css.css">
    <style>
        .modal {display:none;position:fixed;top:0;left:0;width:100%;height:100%;
                background:rgba(0,0,0,0.5);justify-content:center;align-items:center;}
        .modal-content {background:#fff;padding:20px;border-radius:8px;width:500px;}
        .close {float:right;cursor:pointer;font-size:20px;}
    </style>
    <script>
        function moModal(id){ document.getElementById(id).style.display="flex"; }
        function dongModal(id){ document.getElementById(id).style.display="none"; }

        function suaDonMua(so, ngay, ncc, thuoc, loai, sl, gia, tt, gc){
            document.getElementById('edit-so').value = so;
            document.getElementById('edit-ngay').value = ngay;
            document.getElementById('edit-ncc').value = ncc;
            document.getElementById('edit-thuoc').value = thuoc;
            document.getElementById('edit-loai').value = loai;
            document.getElementById('edit-soluong').value = sl;
            document.getElementById('edit-gia').value = gia;
            document.getElementById('edit-trangthai').value = tt;
            document.getElementById('edit-ghichu').value = gc;
            moModal('modal-sua-don-mua');
        }
    </script>
</head>
<body>
<div class="page-header">
    <h1 class="page-title">Quản lý phiếu nhập </h1>
    <p class="page-subtitle">Phiếu nhập hàng vào kho với chi tiết thuốc</p>
</div>

<div class="table-container">
    <div class="table-header">
        <button class="btn btn-primary" onclick="moModal('modal-them-don-mua')">➕ Tạo phiếu nhập</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Số đơn</th>
                <th>Ngày mua</th>
                <th>Nhà cung cấp</th>
                <th>Tên thuốc</th>
                <th>Loại thuốc</th>
                <th>Số lượng</th>
                <th>Giá mua</th>
                <th>Thành tiền</th>
                <th>Trạng thái</th>
                <th>Ghi chú</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()){ ?>
            <tr>
                <td><?= $row['so_don_mua'] ?></td>
                <td><?= $row['ngay_mua'] ?></td>
                <td><?= $row['ten_nha_cung_cap'] ?></td>
                <td><?= $row['ten_thuoc'] ?></td>
                <td><?= $row['loai_thuoc'] ?></td>
                <td><?= $row['so_luong'] ?></td>
                <td><?= number_format($row['gia_mua']) ?> đ</td>
                <td><?= number_format($row['thanh_tien']) ?> đ</td>
                <td><?= $row['trang_thai'] ?></td>
                <td><?= $row['ghi_chu'] ?></td>
                <td>
                    <button class="btn btn-info btn-sm"
                        onclick="suaDonMua('<?= $row['so_don_mua'] ?>','<?= $row['ngay_mua'] ?>',
                                           '<?= $row['ma_nha_cung_cap'] ?>','<?= $row['ma_thuoc'] ?>',
                                           '<?= $row['loai_thuoc'] ?>','<?= $row['so_luong'] ?>',
                                           '<?= $row['gia_mua'] ?>','<?= $row['trang_thai'] ?>',
                                           '<?= $row['ghi_chu'] ?>')">✏️ Sửa</button>
                    <a href="?page=don-mua&xoa=<?= $row['so_don_mua'] ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Xóa đơn này?')">🗑️ Xóa</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal Thêm -->
<div id="modal-them-don-mua" class="modal">
    <div class="modal-content">
        <span class="close" onclick="dongModal('modal-them-don-mua')">&times;</span>
        <h2>Tạo đơn mua</h2>
        <form method="POST">
            <input type="hidden" name="action" value="them">
            <label>Số đơn</label><input type="text" name="so_don_mua" required><br>
            <label>Ngày mua</label><input type="date" name="ngay_mua" required><br>
            <label>Nhà cung cấp</label>
            <select name="ma_nha_cung_cap" required>
                <?php $ncc_list->data_seek(0); while($ncc = $ncc_list->fetch_assoc()){ ?>
                    <option value="<?= $ncc['ma_nha_cung_cap'] ?>"><?= $ncc['ten_nha_cung_cap'] ?></option>
                <?php } ?>
            </select><br>
            <label>Thuốc</label>
            <select name="ma_thuoc" required>
                <?php $thuoc_list->data_seek(0); while($t = $thuoc_list->fetch_assoc()){ ?>
                    <option value="<?= $t['ma_thuoc'] ?>"><?= $t['ten_thuoc'] ?></option>
                <?php } ?>
            </select><br>
            <label>Loại thuốc</label><input type="text" name="loai_thuoc" required><br>
            <label>Số lượng</label><input type="number" name="so_luong" required><br>
            <label>Giá mua</label><input type="number" name="gia_mua" required><br>
            <label>Trạng thái</label><input type="text" name="trang_thai"><br>
            <label>Ghi chú</label><input type="text" name="ghi_chu"><br>
            <button type="submit" class="btn btn-primary">Thêm</button>
        </form>
    </div>
</div>

<!-- Modal Sửa -->
<div id="modal-sua-don-mua" class="modal">
    <div class="modal-content">
        <span class="close" onclick="dongModal('modal-sua-don-mua')">&times;</span>
        <h2>Sửa đơn mua</h2>
        <form method="POST">
            <input type="hidden" name="action" value="sua">
            <label>Số đơn</label><input type="text" name="so_don_mua" id="edit-so" readonly><br>
            <label>Ngày mua</label><input type="date" name="ngay_mua" id="edit-ngay" required><br>
            <label>Nhà cung cấp</label>
            <select name="ma_nha_cung_cap" id="edit-ncc" required>
                <?php $ncc_list->data_seek(0); while($ncc = $ncc_list->fetch_assoc()){ ?>
                    <option value="<?= $ncc['ma_nha_cung_cap'] ?>"><?= $ncc['ten_nha_cung_cap'] ?></option>
                <?php } ?>
            </select><br>
            <label>Thuốc</label>
            <select name="ma_thuoc" id="edit-thuoc" required>
                <?php $thuoc_list->data_seek(0); while($t = $thuoc_list->fetch_assoc()){ ?>
                    <option value="<?= $t['ma_thuoc'] ?>"><?= $t['ten_thuoc'] ?></option>
                <?php } ?>
            </select><br>
            <label>Loại thuốc</label><input type="text" name="loai_thuoc" id="edit-loai" required><br>
            <label>Số lượng</label><input type="number" name="so_luong" id="edit-soluong" required><br>
            <label>Giá mua</label><input type="number" name="gia_mua" id="edit-gia" required><br>
            <label>Trạng thái</label><input type="text" name="trang_thai" id="edit-trangthai"><br>
            <label>Ghi chú</label><input type="text" name="ghi_chu" id="edit-ghichu"><br>
            <button type="submit" class="btn btn-primary">Lưu</button>
        </form>
    </div>
</div>
</body>
</html>

<?php
include("connect.php");

// Xử lý thêm, sửa, xóa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    $soDon = $_POST['so_don_mua'] ?? '';
    $ngayMua = $_POST['ngay_mua'] ?? '';
    $maNCC = $_POST['ma_nha_cung_cap'] ?? '';
    $maThuoc = $_POST['ma_thuoc'] ?? '';
    $loaiThuoc = $_POST['loai_thuoc'] ?? '';
    $soLuong = intval($_POST['so_luong'] ?? 0);
    $giaMua = floatval($_POST['gia_mua'] ?? 0);
    $thanhTien = $soLuong * $giaMua;
    $trangThai = $_POST['trang_thai'] ?? 'Chờ xử lý';
    $ghiChu = $_POST['ghi_chu'] ?? '';

    if ($action == 'them') {
        $stmt = $conn->prepare("INSERT INTO donmua 
            (so_don_mua, ngay_mua, ma_nha_cung_cap, ma_thuoc, loai_thuoc, so_luong, gia_mua, thanh_tien, trang_thai, ghi_chu) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssiddss", $soDon, $ngayMua, $maNCC, $maThuoc, $loaiThuoc,
                          $soLuong, $giaMua, $thanhTien, $trangThai, $ghiChu);
        $stmt->execute();
    }

    if ($action == 'sua') {
        $stmt = $conn->prepare("UPDATE donmua 
            SET ngay_mua=?, ma_nha_cung_cap=?, ma_thuoc=?, loai_thuoc=?, so_luong=?, gia_mua=?, thanh_tien=?, trang_thai=?, ghi_chu=? 
            WHERE so_don_mua=?");
        $stmt->bind_param("ssssiddsss", $ngayMua, $maNCC, $maThuoc, $loaiThuoc,
                          $soLuong, $giaMua, $thanhTien, $trangThai, $ghiChu, $soDon);
        $stmt->execute();
    }
}

if (isset($_GET['xoa'])) {
    $soDon = $_GET['xoa'];
    $stmt = $conn->prepare("DELETE FROM donmua WHERE so_don_mua=?");
    $stmt->bind_param("s", $soDon);
    $stmt->execute();
}

// Lấy danh sách đơn mua (JOIN để hiển thị tên NCC, tên thuốc)
$sql = "SELECT dm.*, ncc.ten_nha_cung_cap, t.ten_thuoc 
        FROM donmua dm
        JOIN nhacungcap ncc ON dm.ma_nha_cung_cap = ncc.ma_nha_cung_cap
        JOIN thuoc t ON dm.ma_thuoc = t.ma_thuoc
        ORDER BY dm.so_don_mua DESC";
$result = $conn->query($sql);

// Lấy danh sách NCC + thuốc
$ncc_list = $conn->query("SELECT * FROM nhacungcap");
$thuoc_list = $conn->query("SELECT * FROM thuoc");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đơn mua</title>
    <link rel="stylesheet" href="css.css">
    <style>
        .modal {display:none;position:fixed;top:0;left:0;width:100%;height:100%;
                background:rgba(0,0,0,0.5);justify-content:center;align-items:center;}
        .modal-content {background:#fff;padding:20px;border-radius:8px;width:500px;}
        .close {float:right;cursor:pointer;font-size:20px;}
    </style>
    <script>
        function moModal(id){ document.getElementById(id).style.display="flex"; }
        function dongModal(id){ document.getElementById(id).style.display="none"; }

        function suaDonMua(so, ngay, ncc, thuoc, loai, sl, gia, tt, gc){
            document.getElementById('edit-so').value = so;
            document.getElementById('edit-ngay').value = ngay;
            document.getElementById('edit-ncc').value = ncc;
            document.getElementById('edit-thuoc').value = thuoc;
            document.getElementById('edit-loai').value = loai;
            document.getElementById('edit-soluong').value = sl;
            document.getElementById('edit-gia').value = gia;
            document.getElementById('edit-trangthai').value = tt;
            document.getElementById('edit-ghichu').value = gc;
            moModal('modal-sua-don-mua');
        }
    </script>
</head>
<body>
<div class="page-header">
    <h1 class="page-title">Quản lý phiếu nhập </h1>
    <p class="page-subtitle">Phiếu nhập hàng vào kho với chi tiết thuốc</p>
</div>

<div class="table-container">
    <div class="table-header">
        <button class="btn btn-primary" onclick="moModal('modal-them-don-mua')">➕ Tạo phiếu nhập</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Số đơn</th>
                <th>Ngày mua</th>
                <th>Nhà cung cấp</th>
                <th>Tên thuốc</th>
                <th>Loại thuốc</th>
                <th>Số lượng</th>
                <th>Giá mua</th>
                <th>Thành tiền</th>
                <th>Trạng thái</th>
                <th>Ghi chú</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()){ ?>
            <tr>
                <td><?= $row['so_don_mua'] ?></td>
                <td><?= $row['ngay_mua'] ?></td>
                <td><?= $row['ten_nha_cung_cap'] ?></td>
                <td><?= $row['ten_thuoc'] ?></td>
                <td><?= $row['loai_thuoc'] ?></td>
                <td><?= $row['so_luong'] ?></td>
                <td><?= number_format($row['gia_mua']) ?> đ</td>
                <td><?= number_format($row['thanh_tien']) ?> đ</td>
                <td><?= $row['trang_thai'] ?></td>
                <td><?= $row['ghi_chu'] ?></td>
                <td>
                    <button class="btn btn-info btn-sm"
                        onclick="suaDonMua('<?= $row['so_don_mua'] ?>','<?= $row['ngay_mua'] ?>',
                                           '<?= $row['ma_nha_cung_cap'] ?>','<?= $row['ma_thuoc'] ?>',
                                           '<?= $row['loai_thuoc'] ?>','<?= $row['so_luong'] ?>',
                                           '<?= $row['gia_mua'] ?>','<?= $row['trang_thai'] ?>',
                                           '<?= $row['ghi_chu'] ?>')">✏️ Sửa</button>
                    <a href="?page=don-mua&xoa=<?= $row['so_don_mua'] ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Xóa đơn này?')">🗑️ Xóa</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal Thêm -->
<div id="modal-them-don-mua" class="modal">
    <div class="modal-content">
        <span class="close" onclick="dongModal('modal-them-don-mua')">&times;</span>
        <h2>Tạo đơn mua</h2>
        <form method="POST">
            <input type="hidden" name="action" value="them">
            <label>Số đơn</label><input type="text" name="so_don_mua" required><br>
            <label>Ngày mua</label><input type="date" name="ngay_mua" required><br>
            <label>Nhà cung cấp</label>
            <select name="ma_nha_cung_cap" required>
                <?php $ncc_list->data_seek(0); while($ncc = $ncc_list->fetch_assoc()){ ?>
                    <option value="<?= $ncc['ma_nha_cung_cap'] ?>"><?= $ncc['ten_nha_cung_cap'] ?></option>
                <?php } ?>
            </select><br>
            <label>Thuốc</label>
            <select name="ma_thuoc" required>
                <?php $thuoc_list->data_seek(0); while($t = $thuoc_list->fetch_assoc()){ ?>
                    <option value="<?= $t['ma_thuoc'] ?>"><?= $t['ten_thuoc'] ?></option>
                <?php } ?>
            </select><br>
            <label>Loại thuốc</label><input type="text" name="loai_thuoc" required><br>
            <label>Số lượng</label><input type="number" name="so_luong" required><br>
            <label>Giá mua</label><input type="number" name="gia_mua" required><br>
            <label>Trạng thái</label><input type="text" name="trang_thai"><br>
            <label>Ghi chú</label><input type="text" name="ghi_chu"><br>
            <button type="submit" class="btn btn-primary">Thêm</button>
        </form>
    </div>
</div>

<!-- Modal Sửa -->
<div id="modal-sua-don-mua" class="modal">
    <div class="modal-content">
        <span class="close" onclick="dongModal('modal-sua-don-mua')">&times;</span>
        <h2>Sửa đơn mua</h2>
        <form method="POST">
            <input type="hidden" name="action" value="sua">
            <label>Số đơn</label><input type="text" name="so_don_mua" id="edit-so" readonly><br>
            <label>Ngày mua</label><input type="date" name="ngay_mua" id="edit-ngay" required><br>
            <label>Nhà cung cấp</label>
            <select name="ma_nha_cung_cap" id="edit-ncc" required>
                <?php $ncc_list->data_seek(0); while($ncc = $ncc_list->fetch_assoc()){ ?>
                    <option value="<?= $ncc['ma_nha_cung_cap'] ?>"><?= $ncc['ten_nha_cung_cap'] ?></option>
                <?php } ?>
            </select><br>
            <label>Thuốc</label>
            <select name="ma_thuoc" id="edit-thuoc" required>
                <?php $thuoc_list->data_seek(0); while($t = $thuoc_list->fetch_assoc()){ ?>
                    <option value="<?= $t['ma_thuoc'] ?>"><?= $t['ten_thuoc'] ?></option>
                <?php } ?>
            </select><br>
            <label>Loại thuốc</label><input type="text" name="loai_thuoc" id="edit-loai" required><br>
            <label>Số lượng</label><input type="number" name="so_luong" id="edit-soluong" required><br>
            <label>Giá mua</label><input type="number" name="gia_mua" id="edit-gia" required><br>
            <label>Trạng thái</label><input type="text" name="trang_thai" id="edit-trangthai"><br>
            <label>Ghi chú</label><input type="text" name="ghi_chu" id="edit-ghichu"><br>
            <button type="submit" class="btn btn-primary">Lưu</button>
        </form>
    </div>
</div>
</body>
</html>
