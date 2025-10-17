<?php
include("connect.php");
$conn->set_charset("utf8");

// ================== XỬ LÝ FORM ================== //

// Thêm khách hàng
if (isset($_POST['add'])) {
    $ten = $_POST['ten'];
    $dia_chi = $_POST['dia_chi'];
    $dien_thoai = $_POST['dien_thoai'];

    $result = $conn->query("SELECT MAX(CAST(SUBSTRING(ma_khach,3) AS UNSIGNED)) AS max_kh FROM khachhang");
    $row = $result->fetch_assoc();
    $nextNum = $row['max_kh'] ? intval($row['max_kh']) + 1 : 1;
    $ma_khach = 'KH' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

    $conn->query("INSERT INTO khachhang (ma_khach, ten_khach_hang, dia_chi, dien_thoai) 
                  VALUES ('$ma_khach', '$ten', '$dia_chi', '$dien_thoai')");
}

// Sửa khách hàng
if (isset($_POST['edit'])) {
    $ma_khach = $_POST['ma_khach'];
    $ten = $_POST['ten'];
    $dia_chi = $_POST['dia_chi'];
    $dien_thoai = $_POST['dien_thoai'];

    $conn->query("UPDATE khachhang 
                  SET ten_khach_hang='$ten', dia_chi='$dia_chi', dien_thoai='$dien_thoai'
                  WHERE ma_khach='$ma_khach'");
}

// Xóa khách hàng
if (isset($_POST['delete'])) {
    $ma_khach = $_POST['ma_khach'];
    $conn->query("DELETE FROM khachhang WHERE ma_khach='$ma_khach'");
}

// ================== LẤY DỮ LIỆU ================== //
$list = $conn->query("SELECT * FROM khachhang ORDER BY ma_khach ASC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quản lý Khách hàng</title>
<link rel="stylesheet" href="css.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="page">
    <h1>Quản lý Khách hàng</h1>

    <div class="table-header">
        <input type="text" class="search-input" placeholder="Tìm kiếm..." onkeyup="timKiem(this.value)">
        <button class="btn btn-primary" onclick="moModal('modal-them')">➕ Thêm khách hàng</button>
    </div>

    <table class="table" id="table-kh">
        <thead>
            <tr>
                <th>Mã KH</th>
                <th>Tên KH</th>
                <th>Địa chỉ</th>
                <th>Điện thoại</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
        <?php while($r = $list->fetch_assoc()) { ?>
            <tr>
                <td><?= $r['ma_khach'] ?></td>
                <td><?= $r['ten_khach_hang'] ?></td>
                <td><?= $r['dia_chi'] ?></td>
                <td><?= $r['dien_thoai'] ?></td>
                <td>
                    <button class="btn btn-info" onclick='suaKH(<?= json_encode($r, JSON_HEX_APOS|JSON_HEX_QUOT) ?>)'>Sửa</button>
                    <form method="post" style="display:inline;" onsubmit="return confirm('Xóa khách hàng này?')">
                        <input type="hidden" name="ma_khach" value="<?= $r['ma_khach'] ?>">
                        <button type="submit" name="delete" class="btn btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal Thêm -->
<div id="modal-them" class="modal">
<div class="modal-header">
<h3>Thêm khách hàng</h3>
<button class="close-btn" onclick="dongModal('modal-them')">&times;</button>
</div>
<form method="post">
    <label>Tên KH</label><br>
    <input type="text" name="ten" required><br>
    <label>Địa chỉ</label><br>
    <textarea name="dia_chi" required></textarea><br>
    <label>Điện thoại</label><br>
    <input type="tel" name="dien_thoai" required><br><br>
    <button type="submit" name="add" class="btn btn-primary">Thêm</button>
    <button type="button" class="btn" onclick="dongModal('modal-them')">Hủy</button>
</form>
</div>

<!-- Modal Sửa -->
<div id="modal-sua" class="modal">
<div class="modal-header">
<h3>Sửa khách hàng</h3>
<button class="close-btn" onclick="dongModal('modal-sua')">&times;</button>
</div>
<form method="post">
    <input type="hidden" name="ma_khach" id="edit_ma_khach">
    <label>Tên KH</label><br>
    <input type="text" name="ten" id="edit_ten" required><br>
    <label>Địa chỉ</label><br>
    <textarea name="dia_chi" id="edit_dia_chi" required></textarea><br>
    <label>Điện thoại</label><br>
    <input type="tel" name="dien_thoai" id="edit_dien_thoai" required><br><br>
    <button type="submit" name="edit" class="btn btn-primary">Cập nhật</button>
    <button type="button" class="btn" onclick="dongModal('modal-sua')">Hủy</button>
</form>
</div>

<script>
function moModal(id){ $('#'+id).show(); }
function dongModal(id){ $('#'+id).hide(); }

function suaKH(r){
    $('#edit_ma_khach').val(r.ma_khach);
    $('#edit_ten').val(r.ten_khach_hang);
    $('#edit_dia_chi').val(r.dia_chi);
    $('#edit_dien_thoai').val(r.dien_thoai);
    moModal('modal-sua');
}

// Tìm kiếm trực tiếp
function timKiem(value){
    value = value.toLowerCase();
    $('#table-kh tbody tr').each(function(){
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
}
</script>
</body>
</html>

