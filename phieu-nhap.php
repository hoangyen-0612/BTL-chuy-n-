<?php
include("connect.php");
$conn->set_charset("utf8");
if (isset($_POST['add'])) {
    $ma_thuoc  = $_POST['ma_thuoc'];
    $ma_nha_cung_cap    = $_POST['ma_nha_cung_cap'];
    $ma_nhan_vien     = $_POST['ma_nhan_vien'];
    $so_luong  = intval($_POST['so_luong_nhap']);
    $gia_nhap  = floatval($_POST['gia_nhap']);
    $thanh_tien= $so_luong * $gia_nhap;

    $r = $conn->query("SELECT MAX(CAST(SUBSTRING(so_pn,3) AS UNSIGNED)) AS max_pn FROM phieunhap");
    $row = $r->fetch_assoc();
    $next = $row['max_pn'] ? $row['max_pn'] + 1 : 1;
    $so_pn = 'PN'.str_pad($next,3,'0',STR_PAD_LEFT);

    $sql = "INSERT INTO phieunhap(so_pn,ma_thuoc,ma_nha_cung_cap,ma_nhan_vien,so_luong_nhap,gia_nhap,thanh_tien_nhap,ngay_nhap)
            VALUES ('$so_pn','$ma_thuoc','$ma_nha_cung_cap','$ma_nhan_vien','$so_luong','$gia_nhap','$thanh_tien',NOW())";
    if ($conn->query($sql)) {
        // ======= Cập nhật tồn kho ======= //
        $check = $conn->query("SELECT k.ma_kho 
                               FROM kho k JOIN thuoc t ON k.ma_kho = t.ma_kho 
                               WHERE t.ma_thuoc = '$ma_thuoc'");
        if ($check->num_rows > 0) {
            $conn->query("UPDATE kho k 
                          JOIN thuoc t ON k.ma_kho = t.ma_kho 
                          SET k.ton_kho = k.ton_kho + $so_luong,
                              k.sl_nhap = k.sl_nhap + $so_luong
                          WHERE t.ma_thuoc = '$ma_thuoc'");
        } else {
            $ma_kho = 'K'.time();
            $conn->query("INSERT INTO kho(ma_kho, sl_nhap, sl_giao, ton_kho)
                          VALUES ('$ma_kho',$so_luong,0,$so_luong)");
            $conn->query("UPDATE thuoc SET ma_kho='$ma_kho' WHERE ma_thuoc='$ma_thuoc'");
        }
    } else echo "Lỗi Thêm: ".$conn->error;
}

// Sửa phiếu nhập
if (isset($_POST['edit'])) {
    $so_pn    = $_POST['so_pn'];
    $ma_thuoc = $_POST['ma_thuoc'];
    $ma_nha_cung_cap   = $_POST['ma_nha_cung_cap'];
    $ma_nhan_vien    = $_POST['ma_nhan_vien'];
    $sl_moi   = intval($_POST['so_luong_nhap']);
    $gia_moi  = floatval($_POST['gia_nhap']);
    $thanh_tien = $sl_moi * $gia_moi;

    $old = $conn->query("SELECT so_luong_nhap FROM phieunhap WHERE so_pn='$so_pn'")->fetch_assoc();
    $sl_cu = intval($old['so_luong_nhap']);

    $sql = "UPDATE phieunhap SET 
                ma_nha_cung_cap='$ma_nha_cung_cap', ma_nhan_vien='$ma_nhan_vien',
                so_luong_nhap='$sl_moi', gia_nhap='$gia_moi', thanh_tien_nhap='$thanh_tien'
            WHERE so_pn='$so_pn'";
    if ($conn->query($sql)) {
        $diff = $sl_moi - $sl_cu;
        $conn->query("UPDATE kho k 
                      JOIN thuoc t ON k.ma_kho = t.ma_kho 
                      SET k.ton_kho = k.ton_kho + $diff,
                          k.sl_nhap = k.sl_nhap + $diff
                      WHERE t.ma_thuoc = '$ma_thuoc'");
    } else echo "Lỗi Sửa: ".$conn->error;
}

// Xóa phiếu nhập
if (isset($_POST['delete_pn'])) {
    $so_pn = $_POST['delete_pn'];
    $row = $conn->query("SELECT ma_thuoc, so_luong_nhap FROM phieunhap WHERE so_pn='$so_pn'")->fetch_assoc();
    if ($row) {
        $ma_thuoc = $row['ma_thuoc'];
        $sl = intval($row['so_luong_nhap']);
        $conn->query("DELETE FROM phieunhap WHERE so_pn='$so_pn'");
        $conn->query("UPDATE kho k 
                      JOIN thuoc t ON k.ma_kho = t.ma_kho 
                      SET k.ton_kho = k.ton_kho - $sl,
                          k.sl_nhap = k.sl_nhap - $sl
                      WHERE t.ma_thuoc = '$ma_thuoc'");
    }
}

// ================== LẤY DỮ LIỆU ================== //
$list_sql = "SELECT pn.*, t.ten_thuoc, d.ten_danh_muc, t.ma_kho,
                    ncc.ten_nha_cung_cap, nv.ten_nhan_vien
             FROM phieunhap pn
             JOIN thuoc t ON pn.ma_thuoc = t.ma_thuoc
             JOIN danh_muc_thuoc d ON t.ma_danh_muc = d.ma_danh_muc
             LEFT JOIN nhacungcap ncc ON pn.ma_nha_cung_cap = ncc.ma_nha_cung_cap
             LEFT JOIN nhanvien nv ON pn.ma_nhan_vien = nv.ma_nhan_vien
             ORDER BY pn.ngay_nhap ASC";
$list = $conn->query($list_sql);

$thuoc_sql = "SELECT t.ma_thuoc, t.ten_thuoc, d.ten_danh_muc, t.ma_kho, k.ton_kho
              FROM thuoc t
              LEFT JOIN danh_muc_thuoc d ON t.ma_danh_muc = d.ma_danh_muc
              LEFT JOIN kho k ON t.ma_kho = k.ma_kho
              ORDER BY t.ten_thuoc";
$thuoc_list = $conn->query($thuoc_sql);

// danh sách NCC + NV
$ncc_list = $conn->query("SELECT ma_nha_cung_cap, ten_nha_cung_cap FROM nhacungcap ORDER BY ten_nha_cung_cap");
$nv_list  = $conn->query("SELECT ma_nhan_vien, ten_nhan_vien  FROM nhanvien   ORDER BY ten_nhan_vien");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý Phiếu Nhập</title>
<link rel="stylesheet" href="css.css">
</head>
<body>

<div class="page-header">
  <h1 class="page-title">Quản lý Phiếu Nhập</h1>
  <p class="page-subtitle">Quản lý phiếu nhập hàng</p>
</div>

<div class="table-container">
    <div class="table-header">
        <div class="search-filters">
            <input type="text" class="search-input" placeholder="Tìm kiếm phiếu nhập..."
                   onkeyup="timKiem('danh-sach-phieu-nhap', this.value)">
        </div>
        <button class="btn btn-primary" onclick="moModal('modal-them-phieu-nhap')"> Tạo phiếu nhập</button>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Số PN</th><th>Ngày nhập</th>
                <th>Tên thuốc</th><th>Loại thuốc</th>
                <th>Nhà cung cấp</th><th>Nhân viên</th>
                <th>SL nhập</th><th>Giá nhập</th><th>Thành tiền</th>
                <th>Mã kho</th><th>Thao tác</th>
            </tr>
        </thead>
        <tbody id="danh-sach-phieu-nhap">
        <?php while($r = $list->fetch_assoc()) { ?>
            <tr>
                <td><?= $r['so_pn'] ?></td>
                <td><?= $r['ngay_nhap'] ?></td>
                <td><?= $r['ten_thuoc'] ?></td>
                <td><?= $r['ten_danh_muc'] ?></td>
                <td><?= $r['ten_nha_cung_cap'] ?></td>
                <td><?= $r['ten_nhan_vien'] ?></td>
                <td><?= $r['so_luong_nhap'] ?></td>
                <td><?= number_format($r['gia_nhap']) ?></td>
                <td><?= number_format($r['thanh_tien_nhap']) ?></td>
                <td><?= $r['ma_kho'] ?></td>
                <td>
                    <button class="btn btn-info btn-sm"
                        onclick='editData(<?= json_encode($r, JSON_HEX_APOS|JSON_HEX_QUOT) ?>)'>Sửa</button>
                    <form method="post" style="display:inline;" onsubmit="return confirm('Xóa phiếu này?')">
                        <input type="hidden" name="delete_pn" value="<?= $r['so_pn'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                    <button class="btn btn-primary btn-sm"
                        onclick='printInvoice(<?= json_encode($r, JSON_HEX_APOS|JSON_HEX_QUOT) ?>)'>In</button>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal thêm phiếu nhập -->
<div id="modal-them-phieu-nhap" class="modal">
<div class="modal-content">
<div class="modal-header">
    <h3 class="modal-title">Tạo phiếu nhập</h3>
    <button class="close-btn" onclick="dongModal('modal-them-phieu-nhap')">&times;</button>
</div>
<form method="post">
    <div class="form-group">
        <label class="form-label">Thuốc</label>
        <select class="form-select" name="ma_thuoc" id="thuocSelectAdd" required>
            <option value="">-- Chọn thuốc --</option>
            <?php while($t=$thuoc_list->fetch_assoc()){ ?>
            <option value="<?= $t['ma_thuoc'] ?>" data-loai="<?= $t['ten_danh_muc'] ?>"
                    data-kho="<?= $t['ma_kho'] ?>" data-ton="<?= $t['ton_kho'] ?>">
                <?= $t['ten_thuoc'] ?>
            </option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label class="form-label">Nhà cung cấp</label>
        <select class="form-select" name="ma_nha_cung_cap" required>
            <option value=""> Chọn nhà cung cấp </option>
            <?php while($n=$ncc_list->fetch_assoc()){ ?>
                <option value="<?= $n['ma_nha_cung_cap'] ?>"><?= $n['ten_nha_cung_cap'] ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label class="form-label">Nhân viên nhập</label>
        <select class="form-select" name="ma_nhan_vien" required>
            <option value="">Chọn nhân viên </option>
            <?php while($nv=$nv_list->fetch_assoc()){ ?>
                <option value="<?= $nv['ma_nhan_vien'] ?>"><?= $nv['ten_nhan_vien'] ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Loại thuốc</label>
            <input type="text" class="form-input" id="loaiThuocAdd" disabled>
        </div>
        <div class="form-group">
            <label class="form-label">Mã kho</label>
            <input type="text" class="form-input" id="maKhoAdd" disabled>
        </div>
        <div class="form-group">
            <label class="form-label">Tồn kho</label>
            <input type="text" class="form-input" id="tonKhoAdd" disabled>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Số lượng nhập</label>
            <input type="number" class="form-input" name="so_luong_nhap" required>
        </div>
        <div class="form-group">
            <label class="form-label">Giá nhập (VNĐ)</label>
            <input type="number" class="form-input" name="gia_nhap" required>
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn" onclick="dongModal('modal-them-phieu-nhap')">Hủy</button>
        <button type="submit" name="add" class="btn btn-primary">Tạo phiếu nhập</button>
    </div>
</form>
</div>
</div>

<!-- Modal Sửa -->
<div id="modal-sua-phieu-nhap" class="modal">
<div class="modal-content">
<div class="modal-header">
    <h3 class="modal-title">Sửa phiếu nhập</h3>
    <button class="close-btn" onclick="dongModal('modal-sua-phieu-nhap')">&times;</button>
</div>
<form method="post">
    <input type="hidden" name="so_pn" id="editMaPhieu">
    <input type="hidden" name="ma_thuoc" id="editMaThuoc">

    <div class="form-group">
        <label class="form-label">Tên thuốc</label>
        <input type="text" class="form-input" id="editTenThuoc" disabled>
    </div>

    <div class="form-group">
        <label class="form-label">Nhà cung cấp</label>
        <select class="form-select" name="ma_nha_cung_cap" id="editNCC" required></select>
    </div>

    <div class="form-group">
        <label class="form-label">Nhân viên nhập</label>
        <select class="form-select" name="ma_nhan_vien" id="editNV" required></select>
    </div>

    <div class="form-group">
        <label class="form-label">Số lượng nhập</label>
        <input type="number" class="form-input" name="so_luong_nhap" id="editSoLuong" required>
    </div>
    <div class="form-group">
        <label class="form-label">Giá nhập (VNĐ)</label>
        <input type="number" class="form-input" name="gia_nhap" id="editGiaNhap" required>
    </div>
    <div class="form-actions">
        <button type="button" class="btn" onclick="dongModal('modal-sua-phieu-nhap')">Hủy</button>
        <button type="submit" name="edit" class="btn btn-primary">Cập nhật</button>
    </div>
</form>
</div>
</div>

<div id="printArea" style="display:none;"></div>
<script>
// Modal
function moModal(id){ document.getElementById(id).style.display='flex'; }
function dongModal(id){ document.getElementById(id).style.display='none'; }

// Thông tin thuốc
document.addEventListener('DOMContentLoaded', function(){
    const sel = document.getElementById('thuocSelectAdd');
    if(sel){
        sel.addEventListener('change', function(){
            let opt = this.options[this.selectedIndex];
            document.getElementById('loaiThuocAdd').value = opt.dataset.loai || '';
            document.getElementById('maKhoAdd').value    = opt.dataset.kho  || '';
            document.getElementById('tonKhoAdd').value   = opt.dataset.ton  || '';
        });
    }
});

// Dữ liệu NCC/NV cho form Sửa
const nccData = <?php
    $ncc_all = $conn->query("SELECT ma_nha_cung_cap,ten_nha_cung_cap FROM nhacungcap");
    $arr=[]; while($x=$ncc_all->fetch_assoc()) $arr[]=$x;
    echo json_encode($arr);
?>;
const nvData = <?php
    $nv_all = $conn->query("SELECT ma_nhan_vien,ten_nhan_vien FROM nhanvien");
    $arr=[]; while($x=$nv_all->fetch_assoc()) $arr[]=$x;
    echo json_encode($arr);
?>;

function editData(row){
    document.getElementById('editMaPhieu').value = row.so_pn;
    document.getElementById('editMaThuoc').value = row.ma_thuoc;
    document.getElementById('editTenThuoc').value= row.ten_thuoc;
    document.getElementById('editSoLuong').value = row.so_luong_nhap;
    document.getElementById('editGiaNhap').value = row.gia_nhap;
    const nccSel = document.getElementById('editNCC');
    nccSel.innerHTML = nccData.map(n => `<option value="${n.ma_nha_cung_cap}" ${n.ma_nha_cung_cap==row.ma_nha_cung_cap?'selected':''}>${n.ten_nha_cung_cap}</option>`).join('');

    const nvSel = document.getElementById('editNV');
    nvSel.innerHTML  = nvData.map(n => `<option value="${n.ma_nhan_vien}" ${n.ma_nhan_vien==row.ma_nhan_vien?'selected':''}>${n.ten_nhan_vien}</option>`).join('');

    moModal('modal-sua-phieu-nhap');
}

// In phiếu
function printInvoice(d) {
    const win = window.open('', 'PRINT', 'height=600,width=800');

    const html = `
    <html>
    <head>
        <meta charset="utf-8">
        <title>Phiếu Nhập ${d.so_pn}</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                padding: 20px;
                line-height: 1.6;
            }
            h1 {
                text-align: center;
                margin-bottom: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }
            th, td {
                border: 1px solid #444;
                padding: 8px 12px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }
            .footer {
                margin-top: 30px;
                text-align: right;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <h1>Phiếu Nhập ${d.so_pn}</h1>
        <table>
            <tr>
                <th>Ngày nhập</th>
                <td>${d.ngay_nhap}</td>
            </tr>
            <tr>
                <th>Thuốc</th>
                <td>${d.ten_thuoc}</td>
            </tr>
            <tr>
                <th>Loại</th>
                <td>${d.ten_danh_muc}</td>
            </tr>
            <tr>
                <th>Nhà cung cấp</th>
                <td>${d.ten_nha_cung_cap}</td>
            </tr>
            <tr>
                <th>Nhân viên</th>
                <td>${d.ten_nhan_vien}</td>
            </tr>
            <tr>
                <th>Số lượng</th>
                <td>${d.so_luong_nhap}</td>
            </tr>
            <tr>
                <th>Giá nhập</th>
                <td>${d.gia_nhap}</td>
            </tr>
            <tr>
                <th>Thành tiền</th>
                <td>${d.thanh_tien_nhap}</td>
            </tr>
        </table>
        <div class="footer">
            Ngày in: ${new Date().toLocaleDateString()}
        </div>
    </body>
    </html>
    `;

    win.document.write(html);
    win.document.close();
    win.focus();
    win.print();
    win.close();
}
function timKiem(id, val){
    val = val.toLowerCase();
    document.querySelectorAll(`#${id} tr`).forEach(tr=>{
        tr.style.display = tr.textContent.toLowerCase().includes(val) ? '' : 'none';
    });
}
</script>
</body>
</html>
