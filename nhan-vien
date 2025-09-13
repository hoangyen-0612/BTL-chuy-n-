<?php
include("connect.php");
$conn->set_charset("utf8");

// ================== XỬ LÝ FORM ================== //

// Thêm
if (isset($_POST['add'])) {
    $ten   = $_POST['ten'];
    $dia   = $_POST['dia'];
    $sdt   = $_POST['sdt'];
    $chuc  = $_POST['chuc'];

    // Tạo mã NV tự động NV001, NV002...
    $q = $conn->query("SELECT MAX(CAST(SUBSTRING(ma_nhan_vien,3) AS UNSIGNED)) AS maxid FROM nhanvien");
    $row = $q->fetch_assoc();
    $next = $row['maxid'] ? intval($row['maxid']) + 1 : 1;
    $ma_nhan_vien = 'NV'.str_pad($next,3,'0',STR_PAD_LEFT);

    $conn->query("INSERT INTO nhanvien(ma_nhan_vien,ten_nhan_vien,dia_chi,dien_thoai,chuc_vu)
                  VALUES ('$ma_nhan_vien','$ten','$dia','$sdt','$chuc')");
}

// Sửa
if (isset($_POST['edit'])) {
    $ma   = $_POST['ma'];
    $ten  = $_POST['ten'];
    $dia  = $_POST['dia'];
    $sdt  = $_POST['sdt'];
    $chuc = $_POST['chuc'];

    $conn->query("UPDATE nhanvien 
                  SET ten_nhan_vien='$ten', dia_chi='$dia', dien_thoai='$sdt', chuc_vu='$chuc'
                  WHERE ma_nhan_vien='$ma'");
}

// Xóa
if (isset($_POST['delete'])) {
    $ma = $_POST['ma'];
    $conn->query("DELETE FROM nhanvien WHERE ma_nhan_vien='$ma'");
}

// Lấy danh sách
$list = $conn->query("SELECT * FROM nhanvien ORDER BY ma_nhan_vien ASC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quản lý nhân viên</title>
<link rel="stylesheet" href="css.css">
<style>
.modal{display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
background:#fff;padding:20px;width:420px;z-index:1000;box-shadow:0 4px 10px rgba(0,0,0,.3);}
.modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;}
.close-btn{background:none;border:none;font-size:20px;cursor:pointer;}
.btn{padding:5px 10px;cursor:pointer;margin:2px;}
.btn-primary{background:#4CAF50;color:#fff;border:none;}
.btn-danger{background:#f44336;color:#fff;border:none;}
.btn-info{background:#2196F3;color:#fff;border:none;}
.search-input{padding:5px;width:220px;}
table{width:100%;border-collapse:collapse;margin-top:15px;}
th,td{border:1px solid #ccc;padding:6px;text-align:left;}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="page">
    <h1>Quản lý nhân viên</h1>

    <div class="table-header">
        <input type="text" class="search-input" placeholder="Tìm kiếm nhân viên..." onkeyup="timKiem(this.value)">
        <button class="btn btn-primary" onclick="moModal('modal-them')">➕ Thêm nhân viên</button>
    </div>

    <table id="table-nv">
        <thead>
            <tr>
                <th>Mã nhân viên</th>
                <th>Tên nhân viên</th>
                <th>Địa chỉ</th>
                <th>Điện thoại</th>
                <th>Chức vụ</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
        <?php while($r=$list->fetch_assoc()){ ?>
            <tr>
                <td><?= $r['ma_nhan_vien'] ?></td>
                <td><?= $r['ten_nhan_vien'] ?></td>
                <td><?= $r['dia_chi'] ?></td>
                <td><?= $r['dien_thoai'] ?></td>
                <td><?= $r['chuc_vu'] ?></td>
                <td>
                    <button class="btn btn-info" onclick='suaNV(<?= json_encode($r, JSON_HEX_APOS|JSON_HEX_QUOT) ?>)'>Sửa</button>
                    <form method="post" style="display:inline" onsubmit="return confirm('Xóa nhân viên này?')">
                        <input type="hidden" name="ma" value="<?= $r['ma_nhan_vien'] ?>">
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
        <h3>Thêm nhân viên</h3>
        <button class="close-btn" onclick="dongModal('modal-them')">&times;</button>
    </div>
    <form method="post">
        <label>Tên nhân viên</label><br>
        <input type="text" name="ten" required><br>
        <label>Địa chỉ</label><br>
        <textarea name="dia" required></textarea><br>
        <label>Điện thoại</label><br>
        <input type="tel" name="sdt" required><br>
        <label>Chức vụ</label><br>
        <select name="chuc" required>
            <option value="">Chọn chức vụ</option>
            <option value="Dược sĩ">Dược sĩ</option>
            <option value="Nhân viên bán hàng">Nhân viên bán hàng</option>
            <option value="Kế toán">Kế toán</option>
            <option value="Quản lý">Quản lý</option>
        </select><br><br>
        <button type="submit" name="add" class="btn btn-primary">Thêm</button>
        <button type="button" class="btn" onclick="dongModal('modal-them')">Hủy</button>
    </form>
</div>

<!-- Modal Sửa -->
<div id="modal-sua" class="modal">
    <div class="modal-header">
        <h3>Sửa nhân viên</h3>
        <button class="close-btn" onclick="dongModal('modal-sua')">&times;</button>
    </div>
    <form method="post">
        <input type="hidden" name="ma" id="edit_ma">
        <label>Tên nhân viên</label><br>
        <input type="text" name="ten" id="edit_ten" required><br>
        <label>Địa chỉ</label><br>
        <textarea name="dia" id="edit_dia" required></textarea><br>
        <label>Điện thoại</label><br>
        <input type="tel" name="sdt" id="edit_sdt" required><br>
        <label>Chức vụ</label><br>
        <select name="chuc" id="edit_chuc" required>
            <option value="Dược sĩ">Dược sĩ</option>
            <option value="Nhân viên bán hàng">Nhân viên bán hàng</option>
            <option value="Kế toán">Kế toán</option>
            <option value="Quản lý">Quản lý</option>
        </select><br><br>
        <button type="submit" name="edit" class="btn btn-primary">Cập nhật</button>
        <button type="button" class="btn" onclick="dongModal('modal-sua')">Hủy</button>
    </form>
</div>

<script>
function moModal(id){ $('#'+id).show(); }
function dongModal(id){ $('#'+id).hide(); }

function suaNV(r){
    $('#edit_ma').val(r.ma_nhan_vien);
    $('#edit_ten').val(r.ten_nhan_vien);
    $('#edit_dia').val(r.dia_chi);
    $('#edit_sdt').val(r.dien_thoai);
    $('#edit_chuc').val(r.chuc_vu);
    moModal('modal-sua');
}

function timKiem(v){
    v=v.toLowerCase();
    $('#table-nv tbody tr').each(function(){
        $(this).toggle($(this).text().toLowerCase().indexOf(v)>-1);
    });
}
</script>
</body>
</html>
