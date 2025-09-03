<?php
include("connect.php");

// --- Lấy danh mục để đổ vào select ---
$dm_list = $conn->query("SELECT * FROM danh_muc_thuoc ORDER BY ma_danh_muc ASC");

// --- Xử lý thêm ---
if (isset($_POST['action']) && $_POST['action'] === 'them') {
    $stmt = $conn->prepare("INSERT INTO thuoc (ma_thuoc, ten_thuoc, ma_danh_muc, nha_san_xuat, gia_ban, han_su_dung, hoat_chat, don_vi_tinh) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssisss", $_POST['ma_thuoc'], $_POST['ten_thuoc'], $_POST['ma_danh_muc'], 
                      $_POST['nha_san_xuat'], $_POST['gia_ban'], $_POST['han_su_dung'], 
                      $_POST['hoat_chat'], $_POST['don_vi_tinh']);
    $stmt->execute();
    header("Location: quan-ly-thuoc.php");
    exit();
}

// --- Xử lý sửa ---
if (isset($_POST['action']) && $_POST['action'] === 'sua') {
    $stmt = $conn->prepare("UPDATE thuoc 
                               SET ten_thuoc=?, ma_danh_muc=?, nha_san_xuat=?, gia_ban=?, han_su_dung=?, hoat_chat=?, don_vi_tinh=? 
                             WHERE ma_thuoc=?");
    $stmt->bind_param("sssissss", $_POST['ten_thuoc'], $_POST['ma_danh_muc'], $_POST['nha_san_xuat'], 
                      $_POST['gia_ban'], $_POST['han_su_dung'], $_POST['hoat_chat'], $_POST['don_vi_tinh'], $_POST['ma_thuoc']);
    $stmt->execute();
    header("Location: quan-ly-thuoc.php");
    exit();
}

// --- Xử lý xóa ---
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM thuoc WHERE ma_thuoc=?");
    $stmt->bind_param("s", $_GET['delete']);
    $stmt->execute();
    header("Location: quan-ly-thuoc.php");
    exit();
}

// --- Lấy danh sách thuốc ---
$thuoc_list = $conn->query("SELECT * FROM thuoc ORDER BY ma_thuoc ASC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý thuốc</title>
    <link rel="stylesheet" href="css.css">
    <style>
        .hidden { display: none; }
        .modal { position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); display:flex; justify-content:center; align-items:center; }
        .modal-content { background:#fff; padding:20px; border-radius:8px; width:500px; }
        .close { float:right; cursor:pointer; font-size:20px; }
        /* ===== Modal Layout ===== */
.modal {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
}
.modal.hidden {
  display: none;
}

.modal-content {
  background: #fff;
  border-radius: 8px;
  padding: 20px 24px;
  width: 500px;
  max-width: 95%;
  box-shadow: 0 8px 24px rgba(0,0,0,0.2);
  animation: fadeIn 0.3s ease;
}

/* Header */
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}
.modal-title {
  font-size: 18px;
  font-weight: 600;
}
.close-btn {
  background: none;
  border: none;
  font-size: 22px;
  cursor: pointer;
  color: #666;
}
.close-btn:hover {
  color: #000;
}

/* Form */
.form-group {
  margin-bottom: 14px;
  display: flex;
  flex-direction: column;
}
.form-row {
  display: flex;
  gap: 16px;
  margin-bottom: 14px;
}
.form-label {
  font-size: 14px;
  color: #444;
  margin-bottom: 6px;
}
.form-input,
.form-select {
  padding: 8px 10px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 14px;
  outline: none;
  width: 100%;
}
.form-input:focus,
.form-select:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 2px rgba(99,102,241,0.2);
}

/* Footer buttons */
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 10px;
}
.btn {
  padding: 8px 16px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  font-size: 14px;
}
.btn:hover { opacity: 0.9; }
.btn-primary {
  background: linear-gradient(90deg, #6366f1, #8b5cf6);
  color: white;
}
.btn-secondary {
  background: #f3f4f6;
  color: #374151;
}

/* Animation */
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95);}
  to   { opacity: 1; transform: scale(1);}
}

    </style>
</head>
<body>

<div class="page-header">
    <h1 class="page-title">Quản lý thuốc</h1>
    <p class="page-subtitle">Danh sách và thông tin chi tiết các loại thuốc</p>
</div>

<div class="table-container">
    <div class="table-header">
        <div class="search-filters">
            <input type="text" id="searchInput" class="search-input" placeholder="Tìm theo mã hoặc tên thuốc..." onkeyup="timKiemThuoc()">
        </div>
        <button class="btn btn-primary" onclick="moModal('modal-them')">➕ Thêm thuốc mới</button>
    </div>

    <table border="1" width="100%" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th>Mã thuốc</th>
                <th>Tên thuốc</th>
                <th>Mã danh mục</th>
                <th>Nhà sản xuất</th>
                <th>Đơn vị tính</th>
                <th>Giá bán</th>
                <th>Hạn sử dụng</th>
                <th>Hoạt chất</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody id="danh-sach-thuoc">
            <?php while($thuoc = $thuoc_list->fetch_assoc()) { ?>
            <tr>
                <td><?= $thuoc['ma_thuoc'] ?></td>
                <td><?= $thuoc['ten_thuoc'] ?></td>
                <td><?= $thuoc['ma_danh_muc'] ?></td>
                <td><?= $thuoc['nha_san_xuat'] ?></td>
                <td><?= $thuoc['don_vi_tinh'] ?></td>
                <td><?= number_format($thuoc['gia_ban'],0,',','.') ?> đ</td>
                <td><?= $thuoc['han_su_dung'] ?></td>
                <td><?= $thuoc['hoat_chat'] ?></td>
                <td>
                    <button class="btn btn-info btn-sm" onclick="moModalSuaThuoc('<?= $thuoc['ma_thuoc'] ?>','<?= $thuoc['ten_thuoc'] ?>','<?= $thuoc['ma_danh_muc'] ?>','<?= $thuoc['nha_san_xuat'] ?>','<?= $thuoc['don_vi_tinh'] ?>','<?= $thuoc['gia_ban'] ?>','<?= $thuoc['han_su_dung'] ?>','<?= $thuoc['hoat_chat'] ?>')">✏️ Sửa</button>
                    <a class="btn btn-danger btn-sm" href="?delete=<?= $thuoc['ma_thuoc'] ?>" onclick="return confirm('Xóa thuốc này?')">🗑️ Xóa</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal Thêm -->
<div id="modal-them" class="modal hidden">
    <div class="modal-content">
        <span class="close" onclick="dongModal('modal-them')">&times;</span>
        <h2>Thêm thuốc mới</h2>
        <form method="POST">
            <input type="hidden" name="action" value="them">
            <input type="text" name="ma_thuoc" placeholder="Mã thuốc" required><br><br>
            <input type="text" name="ten_thuoc" placeholder="Tên thuốc" required><br><br>
            <select name="ma_danh_muc" required>
                <option value="">Chọn danh mục</option>
                <?php while($dm = $dm_list->fetch_assoc()){ ?>
                    <option value="<?= $dm['ma_danh_muc'] ?>"><?= $dm['ten_danh_muc'] ?></option>
                <?php } ?>
            </select><br><br>
            <input type="text" name="nha_san_xuat" placeholder="Nhà sản xuất" required><br><br>
            <input type="text" name="don_vi_tinh" placeholder="Đơn vị tính (viên, tuýp, hộp...)" required><br><br>
            <input type="number" name="gia_ban" placeholder="Giá bán" required><br><br>
            <input type="date" name="han_su_dung" required><br><br>
            <input type="text" name="hoat_chat" placeholder="Hoạt chất" required><br><br>
            <button type="submit" class="btn btn-primary">Thêm thuốc</button>
        </form>
    </div>
</div>

<!-- Modal Sửa -->
<div id="modal-sua" class="modal hidden">
    <div class="modal-content">
        <span class="close" onclick="dongModal('modal-sua')">&times;</span>
        <h2>Sửa thuốc</h2>
        <form method="POST">
            <input type="hidden" name="action" value="sua">
            <input type="hidden" name="ma_thuoc" id="edit-ma"><br>
            <input type="text" name="ten_thuoc" id="edit-ten" placeholder="Tên thuốc" required><br><br>
            <input type="text" name="ma_danh_muc" id="edit-danhmuc" placeholder="Mã danh mục" required><br><br>
            <input type="text" name="nha_san_xuat" id="edit-nsx" placeholder="Nhà sản xuất" required><br><br>
            <input type="text" name="don_vi_tinh" id="edit-dvt" placeholder="Đơn vị tính" required><br><br>
            <input type="number" name="gia_ban" id="edit-gia" placeholder="Giá bán" required><br><br>
            <input type="date" name="han_su_dung" id="edit-hsd" required><br><br>
            <input type="text" name="hoat_chat" id="edit-hoatchat" placeholder="Hoạt chất" required><br><br>
            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        </form>
    </div>
</div>

<script>
function moModal(id){ document.getElementById(id).classList.remove('hidden'); }
function dongModal(id){ document.getElementById(id).classList.add('hidden'); }

function moModalSuaThuoc(ma,ten,dm,nsx,dvt,gia,hsd,hoatchat){
    document.getElementById('edit-ma').value = ma;
    document.getElementById('edit-ten').value = ten;
    document.getElementById('edit-danhmuc').value = dm;
    document.getElementById('edit-nsx').value = nsx;
    document.getElementById('edit-dvt').value = dvt;
    document.getElementById('edit-gia').value = gia;
    document.getElementById('edit-hsd').value = hsd;
    document.getElementById('edit-hoatchat').value = hoatchat;
    moModal('modal-sua');
}

function timKiemThuoc(){
    var keyword = document.getElementById("searchInput").value.toLowerCase();
    document.querySelectorAll("#danh-sach-thuoc tr").forEach(tr=>{
        let ma = tr.cells[0].innerText.toLowerCase();
        let ten = tr.cells[1].innerText.toLowerCase();
        tr.style.display = (ma.includes(keyword) || ten.includes(keyword)) ? "" : "none";
    });
}
</script>

</body>
</html>
