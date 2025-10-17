<?php
include("connect.php");
$conn->set_charset("utf8");

// ================== XỬ LÝ FORM ================== //

// Thêm nhà cung cấp
if (isset($_POST['add'])) {
    $ten = $_POST['ten'];
    $dia_chi = $_POST['dia_chi'];
    $dien_thoai = $_POST['dien_thoai'];

    // Lấy mã NCC lớn nhất
    $result = $conn->query("SELECT MAX(CAST(SUBSTRING(ma_nha_cung_cap,4) AS UNSIGNED)) AS max_ncc FROM nhacungcap");
    $row = $result->fetch_assoc();
    $nextNum = $row['max_ncc'] ? intval($row['max_ncc']) + 1 : 1;
    $ma_nha_cung_cap = 'NCC' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

    $sql = "INSERT INTO nhacungcap (ma_nha_cung_cap, ten_nha_cung_cap, dia_chi, dien_thoai) 
            VALUES ('$ma_nha_cung_cap', '$ten', '$dia_chi', '$dien_thoai')";
    $conn->query($sql);
}

// Sửa nhà cung cấp
if (isset($_POST['edit'])) {
    $ma_nha_cung_cap = $_POST['ma_nha_cung_cap'];
    $ten = $_POST['ten'];
    $dia_chi = $_POST['dia_chi'];
    $dien_thoai = $_POST['dien_thoai'];

    $sql = "UPDATE nhacungcap 
            SET ten_nha_cung_cap='$ten', dia_chi='$dia_chi', dien_thoai='$dien_thoai'
            WHERE ma_nha_cung_cap='$ma_nha_cung_cap'";
    $conn->query($sql);
}

// Xóa nhà cung cấp
if (isset($_POST['delete'])) {
    $ma_nha_cung_cap = $_POST['ma_nha_cung_cap'];
    $conn->query("DELETE FROM nhacungcap WHERE ma_nha_cung_cap='$ma_nha_cung_cap'");
}

// ================== LẤY DỮ LIỆU ================== //
$list_sql = "SELECT * FROM nhacungcap ORDER BY ma_nha_cung_cap ASC";
$list = $conn->query($list_sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quản lý Nhà cung cấp</title>
<link rel="stylesheet" href="css.css">
<style>
.modal { display:none; position: fixed; top:50%; left:50%; transform: translate(-50%, -50%); background:#eee; padding:20px; border:1px solid #333; z-index:1000; }
.modal-content { width: 400px; }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div id="nha-cung-cap" class="page">
    <div class="page-header">
        <h1 class="page-title">Quản lý nhà cung cấp</h1>
        <p class="page-subtitle">Thông tin các nhà cung cấp thuốc</p>
    </div>

    <div class="table-container">
        <div class="table-header">
            <div class="search-filters">
                <input type="text" class="search-input" placeholder="Tìm kiếm nhà cung cấp..." onkeyup="timKiem(this.value)">
            </div>
            <button class="btn btn-primary" onclick="moModal('modal-them')">➕ Thêm nhà cung cấp</button>
        </div>

        <table class="table" id="table-ncc">
            <thead>
                <tr>
                    <th>Mã NCC</th>
                    <th>Tên nhà cung cấp</th>
                    <th>Địa chỉ</th>
                    <th>Điện thoại</th>
                    <th>Thao tác</th>
                </tr>
</thead>
            <tbody>
                <?php while($r = $list->fetch_assoc()) { ?>
                <tr>
                    <td><?= $r['ma_nha_cung_cap'] ?></td>
                    <td><?= $r['ten_nha_cung_cap'] ?></td>
                    <td><?= $r['dia_chi'] ?></td>
                    <td><?= $r['dien_thoai'] ?></td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick='suaNCC(<?= json_encode($r, JSON_HEX_APOS|JSON_HEX_QUOT) ?>)'>Sửa</button>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Xóa nhà cung cấp này?')">
                            <input type="hidden" name="ma_nha_cung_cap" value="<?= $r['ma_nha_cung_cap'] ?>">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Xóa</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Thêm -->
<div id="modal-them" class="modal">
<div class="modal-content">
<h3>Thêm nhà cung cấp</h3>
<form method="post">
    <label>Tên nhà cung cấp</label>
    <input type="text" name="ten" required><br>
    <label>Địa chỉ</label>
    <textarea name="dia_chi" required></textarea><br>
    <label>Điện thoại</label>
    <input type="tel" name="dien_thoai" required><br>
    <button type="submit" name="add" class="btn btn-primary">Thêm</button>
    <button type="button" class="btn" onclick="dongModal('modal-them')">Hủy</button>
</form>
</div>
</div>

<!-- Modal Sửa -->
<div id="modal-sua" class="modal">
<div class="modal-content">
<h3>Sửa nhà cung cấp</h3>
<form method="post">
    <input type="hidden" name="ma_nha_cung_cap" id="edit_ma_nha_cung_cap">
    <label>Tên nhà cung cấp</label>
    <input type="text" name="ten" id="edit_ten" required><br>
    <label>Địa chỉ</label>
    <textarea name="dia_chi" id="edit_dia_chi" required></textarea><br>
    <label>Điện thoại</label>
    <input type="tel" name="dien_thoai" id="edit_dien_thoai" required><br>
    <button type="submit" name="edit" class="btn btn-primary">Cập nhật</button>
    <button type="button" class="btn" onclick="dongModal('modal-sua')">Hủy</button>
</form>
</div>
</div>

<script>
// Mở / đóng modal
function moModal(id){ $('#'+id).show(); }
function dongModal(id){ $('#'+id).hide(); }

// Sửa NCC
function suaNCC(r){
    $('#edit_ma_nha_cung_cap').val(r.ma_nha_cung_cap);
    $('#edit_ten').val(r.ten_nha_cung_cap);
    $('#edit_dia_chi').val(r.dia_chi);
    $('#edit_dien_thoai').val(r.dien_thoai);
    moModal('modal-sua');
}

// Tìm kiếm
function timKiem(value){
    value = value.toLowerCase();
    $('#table-ncc tbody tr').each(function(){
        let tdText = $(this).text().toLowerCase();
        $(this).toggle(tdText.indexOf(value) > -1);
    });
}
</script>

</body>
</html>

