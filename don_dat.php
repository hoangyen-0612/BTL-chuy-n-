<?php
include("connect.php");

// Xử lý thêm, sửa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action    = $_POST['action'];
    $soDon     = $_POST['so_don_dat'] ?? '';
    $ngayDat   = $_POST['ngay_dat'] ?? '';
    $maKhach   = $_POST['ma_khach'] ?? '';
    $maThuoc   = $_POST['ma_thuoc'] ?? '';
    $soLuong   = intval($_POST['so_luong'] ?? 0);
    $giaBan    = floatval($_POST['gia_ban'] ?? 0);
    $thanhTien = $soLuong * $giaBan;
    $trangThai = $_POST['trang_thai'] ?? 'Chờ xử lý';

    if ($action == 'them') {
        // Tự động sinh mã đơn đặt
        $res = $conn->query("SELECT so_don_dat FROM dondat ORDER BY so_don_dat DESC LIMIT 1");
        if ($res && $res->num_rows > 0) {
            $last = $res->fetch_assoc()['so_don_dat'];
            $num  = (int)substr($last, 2) + 1;
            $soDon = "DD" . str_pad($num, 3, "0", STR_PAD_LEFT);
        } else {
            $soDon = "DD001";
        }

        $stmt = $conn->prepare("INSERT INTO dondat 
            (so_don_dat, ngay_dat, ma_khach, ma_thuoc, so_luong, gia_ban, thanh_tien, trang_thai) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssidds", $soDon, $ngayDat, $maKhach, $maThuoc,
                          $soLuong, $giaBan, $thanhTien, $trangThai);
        $stmt->execute();
        header("Location: don-dat.php");
        exit;
    }

    if ($action == 'sua') {
        $stmt = $conn->prepare("UPDATE dondat 
            SET ngay_dat=?, ma_khach=?, ma_thuoc=?, so_luong=?, gia_ban=?, thanh_tien=?, trang_thai=? 
            WHERE so_don_dat=?");
        $stmt->bind_param("sssiddss", $ngayDat, $maKhach, $maThuoc,
                          $soLuong, $giaBan, $thanhTien, $trangThai, $soDon);
        $stmt->execute();
    }
}

// Xử lý xóa
if (isset($_GET['xoa'])) {
    $soDon = $_GET['xoa'];
    $stmt = $conn->prepare("DELETE FROM dondat WHERE so_don_dat=?");
    $stmt->bind_param("s", $soDon);
    $stmt->execute();
}
// Lấy danh sách đơn đặt
$sql = "SELECT dd.*, kh.ten_khach_hang, t.ten_thuoc, t.gia_ban
        FROM dondat dd
        JOIN khachhang kh ON dd.ma_khach = kh.ma_khach
        JOIN thuoc t ON dd.ma_thuoc = t.ma_thuoc
        ORDER BY dd.so_don_dat DESC";
$result = $conn->query($sql);

// Lấy danh sách khách hàng + thuốc
$khach_list = $conn->query("SELECT * FROM khachhang");
$thuoc_list = $conn->query("SELECT * FROM thuoc");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn đặt</title>
    <link rel="stylesheet" href="css.css">
    <style>
        .modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;
               background:rgba(0,0,0,0.5);justify-content:center;align-items:center;}
        .modal-content{background:#fff;padding:20px;border-radius:8px;width:500px;}
        .close{float:right;cursor:pointer;}
    </style>
    <script>
        function moModal(id){ document.getElementById(id).style.display="flex"; }
        function dongModal(id){ document.getElementById(id).style.display="none"; }

        function capNhatGia(select, prefix){
            let gia = select.options[select.selectedIndex].getAttribute("data-gia");
            document.getElementById(prefix+"-gia").value = gia;
            tinhTien(prefix);
        }
        function tinhTien(prefix){
            let sl  = document.getElementById(prefix+"-sl").value || 0;
            let gia = document.getElementById(prefix+"-gia").value || 0;
            document.getElementById(prefix+"-thanhtien").value = sl * gia;
        }
        function timKiem(){
            let kw = document.getElementById("searchInput").value.toLowerCase();
            document.querySelectorAll("#donDatTable tbody tr").forEach(row=>{
                row.style.display = row.textContent.toLowerCase().includes(kw) ? "" : "none";
            });
        }
        function suaDonDat(so, ngay, kh, thuoc, sl, gia, tt){
            document.getElementById('edit-so').value = so;
            document.getElementById('edit-ngay').value = ngay;
            document.getElementById('edit-kh').value = kh;
            document.getElementById('edit-thuoc').value = thuoc;
            document.getElementById('edit-sl').value = sl;
            document.getElementById('edit-gia').value = gia;
            document.getElementById('edit-thanhtien').value = sl*gia;
            document.getElementById('edit-trangthai').value = tt;
            moModal('modal-sua');
        }
    </script>
</head>
<body>
    <div class="page-header">
        <h1 class="page-title">Quản lý đơn đặt</h1>
    </div>

    <div class="table-header">
        <div class="search-filters">
            <input type="text" id="searchInput" placeholder="Tìm kiếm đơn đặt..." onkeyup="timKiem()">
        </div>
        <button class="btn btn-primary" onclick="moModal('modal-them')">
            ➕ Thêm đơn đặt
        </button>
    </div>

    <table id="donDatTable">
        <thead>
            <tr>
                <th>Số đơn đặt</th>
                <th>Ngày đặt</th>
                <th>Khách hàng</th>
                <th>Thuốc</th>
                <th>Số lượng</th>
                <th>Giá bán</th>
                <th>Thành tiền</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()){ ?>
            <tr>
                <td><?= $row['so_don_dat'] ?></td>
                <td><?= $row['ngay_dat'] ?></td>
                <td><?= $row['ten_khach_hang'] ?></td>
                <td><?= $row['ten_thuoc'] ?></td>
                <td><?= $row['so_luong'] ?></td>
                <td><?= number_format($row['gia_ban']) ?> đ</td>
                <td><?= number_format($row['thanh_tien']) ?> đ</td>
                <td><?= $row['trang_thai'] ?></td>
                <td>
                    <button class="btn btn-info" onclick="suaDonDat(
                        '<?= $row['so_don_dat'] ?>','<?= $row['ngay_dat'] ?>',
                        '<?= $row['ma_khach'] ?>','<?= $row['ma_thuoc'] ?>',
                        '<?= $row['so_luong'] ?>','<?= $row['gia_ban'] ?>','<?= $row['trang_thai'] ?>'
                    )">✏️ Sửa</button>
                    <a class="btn btn-danger" href="?xoa=<?= $row['so_don_dat'] ?>" onclick="return confirm('Xóa đơn này?')">🗑️ Xóa</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Modal Thêm -->
    <div id="modal-them" class="modal">
        <div class="modal-content">
            <span class="close" onclick="dongModal('modal-them')">&times;</span>
            <h2>Tạo đơn đặt</h2>
            <form method="POST">
                <input type="hidden" name="action" value="them">
                <label>Ngày đặt</label><input type="date" name="ngay_dat" required><br>
                <label>Khách hàng</label>
                <select name="ma_khach" required>
                    <?php while($kh = $khach_list->fetch_assoc()){ ?>
                        <option value="<?= $kh['ma_khach'] ?>"><?= $kh['ten_khach_hang'] ?></option>
                    <?php } ?>
                </select><br>
                <label>Thuốc</label>
                <select name="ma_thuoc" onchange="capNhatGia(this,'add')" required>
                    <option value="">Chọn thuốc</option>
                    <?php $thuoc_list->data_seek(0); while($t = $thuoc_list->fetch_assoc()){ ?>
                        <option value="<?= $t['ma_thuoc'] ?>" data-gia="<?= $t['gia_ban'] ?>">
                            <?= $t['ten_thuoc'] ?>
                        </option>
                    <?php } ?>
                </select><br>
                <label>Số lượng</label><input type="number" id="add-sl" name="so_luong" onchange="tinhTien('add')" required><br>
                <label>Giá bán</label><input type="number" id="add-gia" name="gia_ban" readonly><br>
                <label>Thành tiền</label><input type="number" id="add-thanhtien" name="thanh_tien" readonly><br>
                <label>Trạng thái</label><input type="text" name="trang_thai"><br>
                <button type="submit" class="btn btn-primary">➕ Thêm</button>
            </form>
        </div>
    </div>

    <!-- Modal Sửa -->
    <div id="modal-sua" class="modal">
        <div class="modal-content">
            <span class="close" onclick="dongModal('modal-sua')">&times;</span>
            <h2>Sửa đơn đặt</h2>
            <form method="POST">
                <input type="hidden" name="action" value="sua">
                <input type="hidden" name="so_don_dat" id="edit-so">
                <label>Ngày đặt</label><input type="date" name="ngay_dat" id="edit-ngay" required><br>
                <label>Khách hàng</label>
                <select name="ma_khach" id="edit-kh" required>
                    <?php $khach_list->data_seek(0); while($kh = $khach_list->fetch_assoc()){ ?>
                        <option value="<?= $kh['ma_khach'] ?>"><?= $kh['ten_khach_hang'] ?></option>
                    <?php } ?>
                </select><br>
                <label>Thuốc</label>
                <select name="ma_thuoc" id="edit-thuoc" onchange="capNhatGia(this,'edit')" required>
                    <?php $thuoc_list->data_seek(0); while($t = $thuoc_list->fetch_assoc()){ ?>
                        <option value="<?= $t['ma_thuoc'] ?>" data-gia="<?= $t['gia_ban'] ?>">
                            <?= $t['ten_thuoc'] ?>
                        </option>
                    <?php } ?>
                </select><br>
                <label>Số lượng</label><input type="number" id="edit-sl" name="so_luong" onchange="tinhTien('edit')" required><br>
                <label>Giá bán</label><input type="number" id="edit-gia" name="gia_ban" readonly><br>
                <label>Thành tiền</label><input type="number" id="edit-thanhtien" name="thanh_tien" readonly><br>
                <label>Trạng thái</label><input type="text" name="trang_thai" id="edit-trangthai"><br>
                <button type="submit" class="btn btn-info">💾 Lưu</button>
            </form>
        </div>
    </div>
</body>
</html>
