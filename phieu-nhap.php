<?php
// Kết nối CSDL
include("connect.php");
$conn->set_charset("utf8");

// ================== XỬ LÝ FORM ================== //

// Thêm phiếu nhập
if (isset($_POST['add'])) {
    $ma_thuoc = $_POST['ma_thuoc'];
    $so_luong_nhap = intval($_POST['so_luong_nhap']);
    $gia_nhap = floatval($_POST['gia_nhap']);
    $thanh_tien_nhap = $so_luong_nhap * $gia_nhap;

    $result = $conn->query("SELECT MAX(CAST(SUBSTRING(so_pn,3) AS UNSIGNED)) AS max_pn FROM phieunhap");
    $row = $result->fetch_assoc();
    $nextNum = $row['max_pn'] ? intval($row['max_pn']) + 1 : 1;
    $so_pn = 'PN' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

    $sql = "INSERT INTO phieunhap (so_pn, ma_thuoc, so_luong_nhap, gia_nhap, thanh_tien_nhap, ngay_nhap)
            VALUES ('$so_pn','$ma_thuoc','$so_luong_nhap','$gia_nhap','$thanh_tien_nhap', NOW())";
    if ($conn->query($sql)) {
        // Cập nhật tồn kho
        $conn->query("UPDATE kho k 
                      JOIN thuoc t ON k.ma_kho = t.ma_kho 
                      SET k.ton_kho = k.ton_kho + $so_luong_nhap 
                      WHERE t.ma_thuoc = '$ma_thuoc'");
    } else {
        echo "Lỗi Thêm: " . $conn->error;
    }
}

// Sửa phiếu nhập
if (isset($_POST['edit'])) {
    $so_pn = $_POST['so_pn'];
    $ma_thuoc = $_POST['ma_thuoc'];
    $so_luong_nhap_moi = intval($_POST['so_luong_nhap']);
    $gia_nhap = floatval($_POST['gia_nhap']);
    $thanh_tien_nhap = $so_luong_nhap_moi * $gia_nhap;

    $old = $conn->query("SELECT so_luong_nhap FROM phieunhap WHERE so_pn='$so_pn'")->fetch_assoc();
    $so_luong_nhap_cu = intval($old['so_luong_nhap']);

    $sql = "UPDATE phieunhap 
            SET so_luong_nhap='$so_luong_nhap_moi', gia_nhap='$gia_nhap', thanh_tien_nhap='$thanh_tien_nhap'
            WHERE so_pn='$so_pn'";
    if ($conn->query($sql)) {
        $chenh_lech = $so_luong_nhap_moi - $so_luong_nhap_cu;
        $conn->query("UPDATE kho k 
                      JOIN thuoc t ON k.ma_kho = t.ma_kho 
                      SET k.ton_kho = k.ton_kho + $chenh_lech 
                      WHERE t.ma_thuoc = '$ma_thuoc'");
    } else {
        echo "Lỗi Sửa: " . $conn->error;
    }
}

// Xóa phiếu nhập bằng POST
if (isset($_POST['delete_pn'])) {
    $so_pn = $_POST['delete_pn'];
    $row = $conn->query("SELECT ma_thuoc, so_luong_nhap FROM phieunhap WHERE so_pn='$so_pn'")->fetch_assoc();
    if ($row) {
        $ma_thuoc = $row['ma_thuoc'];
        $so_luong_nhap = intval($row['so_luong_nhap']);
        $conn->query("DELETE FROM phieunhap WHERE so_pn='$so_pn'");
        $conn->query("UPDATE kho k 
                      JOIN thuoc t ON k.ma_kho = t.ma_kho 
                      SET k.ton_kho = k.ton_kho - $so_luong_nhap 
                      WHERE t.ma_thuoc = '$ma_thuoc'");
    }
}

// ================== LẤY DỮ LIỆU ================== //

$list_sql = "SELECT pn.*, t.ten_thuoc, d.ten_danh_muc, t.ma_kho 
             FROM phieunhap pn
             JOIN thuoc t ON pn.ma_thuoc = t.ma_thuoc
             JOIN danh_muc_thuoc d ON t.ma_danh_muc = d.ma_danh_muc
             ORDER BY pn.ngay_nhap DESC";
$list = $conn->query($list_sql);

$thuoc_sql = "SELECT t.ma_thuoc, t.ten_thuoc, d.ten_danh_muc, t.ma_kho, k.ton_kho
              FROM thuoc t
              LEFT JOIN danh_muc_thuoc d ON t.ma_danh_muc = d.ma_danh_muc
              LEFT JOIN kho k ON t.ma_kho = k.ma_kho
              ORDER BY t.ten_thuoc";
$thuoc_list = $conn->query($thuoc_sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý Phiếu Nhập</title>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="css.css"> <!-- CSS dùng chung -->
</head>
<body>

<div class="page-header">
  <h1 class="page-title">Quản lý Phiếu Nhập</h1>
  <p class="page-subtitle">Quản lý phiếu nhập hàng</p>
</div>

<div class="table-container">
<button class="btn btn-primary" onclick="$('#modalAdd').show()">+ Thêm Phiếu Nhập</button>
<table class="table" id="table-phieu-nhap">
<tr>
<th>Mã phiếu</th><th>Tên thuốc</th><th>Loại thuốc</th><th>Mã kho</th>
<th>Số lượng</th><th>Giá nhập</th><th>Thành tiền</th><th>Ngày nhập</th><th>Hành động</th>
</tr>
<?php while($r = $list->fetch_assoc()) { ?>
<tr>
<td><?= $r['so_pn'] ?></td>
<td><?= $r['ten_thuoc'] ?></td>
<td><?= $r['ten_danh_muc'] ?></td>
<td><?= $r['ma_kho'] ?></td>
<td><?= $r['so_luong_nhap'] ?></td>
<td><?= number_format($r['gia_nhap']) ?></td>
<td><?= number_format($r['thanh_tien_nhap']) ?></td>
<td><?= $r['ngay_nhap'] ?></td>
<td>
<button class="btn btn-info btn-sm" onclick='editData(<?= json_encode($r, JSON_HEX_APOS|JSON_HEX_QUOT) ?>)'>Sửa</button>

<form method="post" style="display:inline;" onsubmit="return confirm('Xóa phiếu này?')">
  <input type="hidden" name="delete_pn" value="<?= $r['so_pn'] ?>">
  <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
</form>

<button class="btn btn-primary btn-sm" onclick='printInvoice(<?= json_encode($r, JSON_HEX_APOS|JSON_HEX_QUOT) ?>)'>In</button>
</td>
</tr>
<?php } ?>
</table>
</div>

<!-- Modal Thêm -->
<div id="modalAdd" class="modal">
<div class="modal-content">
<h3>Thêm Phiếu Nhập</h3>
<form method="post">
<label>Thuốc:</label>
<select name="ma_thuoc" id="thuocSelectAdd" required>
<option value="">-- Chọn thuốc --</option>
<?php while($t=$thuoc_list->fetch_assoc()){ ?>
<option value="<?= $t['ma_thuoc'] ?>" data-loai="<?= $t['ten_danh_muc'] ?>" data-kho="<?= $t['ma_kho'] ?>" data-ton="<?= $t['ton_kho'] ?>"><?= $t['ten_thuoc'] ?></option>
<?php } ?>
</select><br>
<label>Loại thuốc:</label><input type="text" id="loaiThuocAdd" disabled><br>
<label>Mã kho:</label><input type="text" id="maKhoAdd" disabled><br>
<label>Tồn kho:</label><input type="text" id="tonKhoAdd" disabled><br>
<label>Số lượng nhập:</label><input type="number" name="so_luong_nhap" required><br>
<label>Giá nhập:</label><input type="number" name="gia_nhap" required><br>
<button type="submit" name="add" class="btn btn-primary">Lưu</button>
<button type="button" class="btn" onclick="$('#modalAdd').hide()">Hủy</button>
</form>
</div>
</div>

<!-- Modal Sửa -->
<div id="modalEdit" class="modal">
<div class="modal-content">
<h3>Sửa Phiếu Nhập</h3>
<form method="post">
<input type="hidden" name="so_pn" id="editMaPhieu">
<input type="hidden" name="ma_thuoc" id="editMaThuoc">
<label>Thuốc:</label><input type="text" id="editTenThuoc" disabled><br>
<label>Số lượng nhập:</label><input type="number" name="so_luong_nhap" id="editSoLuong" required><br>
<label>Giá nhập:</label><input type="number" name="gia_nhap" id="editGiaNhap" required><br>
<button type="submit" name="edit" class="btn btn-primary">Cập nhật</button>
<button type="button" class="btn" onclick="$('#modalEdit').hide()">Hủy</button>
</form>
</div>
</div>

<!-- In phiếu -->
<div id="printArea" style="display:none;">
<h3>PHIẾU NHẬP KHO</h3>
<p>Mã phiếu: <span id="p_ma"></span></p>
<p>Tên thuốc: <span id="p_ten"></span></p>
<p>Loại thuốc: <span id="p_loai"></span></p>
<p>Mã kho: <span id="p_kho"></span></p>
<p>Số lượng: <span id="p_sl"></span></p>
<p>Giá nhập: <span id="p_gia"></span></p>
<p>Thành tiền: <span id="p_tt"></span></p>
<p>Ngày nhập: <span id="p_ngay"></span></p>
</div>

<script>
$("#thuocSelectAdd").change(function(){
  let opt=$(this).find(":selected");
  $("#loaiThuocAdd").val(opt.data("loai"));
  $("#maKhoAdd").val(opt.data("kho"));
  $("#tonKhoAdd").val(opt.data("ton"));
});

function editData(row){
  $("#editMaPhieu").val(row.so_pn);
  $("#editMaThuoc").val(row.ma_thuoc);
  $("#editTenThuoc").val(row.ten_thuoc);
  $("#editSoLuong").val(row.so_luong_nhap);
  $("#editGiaNhap").val(row.gia_nhap);
  $("#modalEdit").show();
}

function printInvoice(r){
  $("#p_ma").text(r.so_pn);
  $("#p_ten").text(r.ten_thuoc);
  $("#p_loai").text(r.ten_danh_muc);
  $("#p_kho").text(r.ma_kho);
  $("#p_sl").text(r.so_luong_nhap);
  $("#p_gia").text(r.gia_nhap);
  $("#p_tt").text(r.thanh_tien_nhap);
  $("#p_ngay").text(r.ngay_nhap);

  let printContent=document.getElementById("printArea").innerHTML;
  let newWin=window.open("");
  newWin.document.write(printContent);
  newWin.print();
  newWin.close();
}
</script>

</body>
</html>
