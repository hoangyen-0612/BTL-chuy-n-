<?php
include("connect.php");

// --- Hàm tạo mã thuốc tự động ---
function taoMaThuoc($conn){
    $sql = "SELECT ma_thuoc FROM thuoc ORDER BY ma_thuoc DESC LIMIT 1";
    $result = $conn->query($sql);
    if($row = $result->fetch_assoc()){
        $last_id = intval(substr($row['ma_thuoc'], 1)) + 1;
    } else {
        $last_id = 1;
    }
    return "T" . str_pad($last_id, 3, "0", STR_PAD_LEFT);
}

// --- Lấy danh mục để đổ vào select ---
$dm_list = $conn->query("SELECT * FROM danh_muc_thuoc ORDER BY ma_danh_muc ASC");

// --- Xử lý thêm ---
if (isset($_POST['action']) && $_POST['action'] === 'them') {
    $ma_thuoc = taoMaThuoc($conn);
    $stmt = $conn->prepare("INSERT INTO thuoc (ma_thuoc, ten_thuoc, ma_danh_muc, nha_san_xuat, gia_ban, han_su_dung, hoat_chat, don_vi_tinh) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssisss", $ma_thuoc, $_POST['ten_thuoc'], $_POST['ma_danh_muc'], 
                      $_POST['nha_san_xuat'], $_POST['gia_ban'], $_POST['han_su_dung'], 
                      $_POST['hoat_chat'], $_POST['don_vi_tinh']);
    $stmt->execute();
    header("Location: quanly.php?page=quan-ly-thuoc");
    exit;
}

// --- Xử lý sửa ---
if (isset($_POST['action']) && $_POST['action'] === 'sua') {
    $stmt = $conn->prepare("UPDATE thuoc 
                               SET ten_thuoc=?, ma_danh_muc=?, nha_san_xuat=?, gia_ban=?, han_su_dung=?, hoat_chat=?, don_vi_tinh=? 
                             WHERE ma_thuoc=?");
    $stmt->bind_param("sssissss", $_POST['ten_thuoc'], $_POST['ma_danh_muc'], $_POST['nha_san_xuat'], 
                      $_POST['gia_ban'], $_POST['han_su_dung'], $_POST['hoat_chat'], $_POST['don_vi_tinh'], $_POST['ma_thuoc']);
    $stmt->execute();
    header("Location: quanly.php?page=quan-ly-thuoc");
    exit;
}

// --- Xử lý xóa ---
if (isset($_GET['delete'])) {
    $ma_thuoc = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM thuoc WHERE ma_thuoc=?");
    $stmt->bind_param("s", $ma_thuoc);
    $stmt->execute();

    header("Location: quanly.php?page=quan-ly-thuoc");
    exit;
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
        <button class="btn btn-primary" onclick="moModal('modal-them-thuoc')"> Thêm thuốc mới</button>
    </div>

    <table border="0" width="100%" cellspacing="0" cellpadding="5">
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
               <td><?=  date("d-m-Y", strtotime($thuoc['han_su_dung'])) ?></td>
                <td><?= $thuoc['hoat_chat'] ?></td>
                <td>
                    <button class="btn btn-info btn-sm" 
                        onclick="moModalSuaThuoc('<?= $thuoc['ma_thuoc'] ?>',
                                                 '<?= $thuoc['ten_thuoc'] ?>',
                                                 '<?= $thuoc['ma_danh_muc'] ?>',
                                                 '<?= $thuoc['nha_san_xuat'] ?>',
                                                 '<?= $thuoc['don_vi_tinh'] ?>',
                                                 '<?= $thuoc['gia_ban'] ?>',
                                                 '<?= $thuoc['han_su_dung'] ?>',
                                                 '<?= $thuoc['hoat_chat'] ?>')"> Sửa</button>

                    <a class="btn btn-danger btn-sm" 
                       href="quanly.php?page=quan-ly-thuoc&delete=<?= $thuoc['ma_thuoc'] ?>" 
                       onclick="return confirm('Xóa thuốc này?')"> Xóa</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal Thêm -->
<div id="modal-them-thuoc" class="modal hidden">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Thêm thuốc mới</h3>
            <button class="close-btn" onclick="dongModal('modal-them-thuoc')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="them">
            <div class="form-group">
                <label class="form-label">Tên thuốc</label>
                <input type="text" class="form-input" name="ten_thuoc" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Mã danh mục</label>
                    <select class="form-select" name="ma_danh_muc" required>
                        <option value="">Chọn danh mục</option>
                        <?php 
                        $dm_list->data_seek(0);
                        while($dm = $dm_list->fetch_assoc()){ ?>
                            <option value="<?= $dm['ma_danh_muc'] ?>"><?= $dm['ten_danh_muc'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Nhà sản xuất</label>
                    <input type="text" class="form-input" name="nha_san_xuat" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Đơn vị tính</label>
                    <input type="text" class="form-input" name="don_vi_tinh" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Giá bán (VNĐ)</label>
                    <input type="number" class="form-input" name="gia_ban" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Hạn sử dụng</label>
                    <input type="date" class="form-input" name="han_su_dung" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Hoạt chất</label>
                    <input type="text" class="form-input" name="hoat_chat" required>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn" onclick="dongModal('modal-them-thuoc')">Hủy</button>
                <button type="submit" class="btn btn-primary">Thêm thuốc</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Sửa -->
<div id="modal-sua-thuoc" class="modal hidden">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Sửa thuốc</h3>
            <button class="close-btn" onclick="dongModal('modal-sua-thuoc')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="sua">
            <input type="hidden" name="ma_thuoc" id="edit-ma">
            
            <div class="form-group">
                <label class="form-label">Tên thuốc</label>
                <input type="text" class="form-input" name="ten_thuoc" id="edit-ten" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Mã danh mục</label>
                    <input type="text" class="form-input" name="ma_danh_muc" id="edit-danhmuc" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nhà sản xuất</label>
                    <input type="text" class="form-input" name="nha_san_xuat" id="edit-nsx" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Đơn vị tính</label>
                    <input type="text" class="form-input" name="don_vi_tinh" id="edit-dvt" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Giá bán (VNĐ)</label>
                    <input type="number" class="form-input" name="gia_ban" id="edit-gia" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Hạn sử dụng</label>
                    <input type="date" class="form-input" name="han_su_dung" id="edit-hsd" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Hoạt chất</label>
                    <input type="text" class="form-input" name="hoat_chat" id="edit-hoatchat" required>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn" onclick="dongModal('modal-sua-thuoc')">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </div>
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
    moModal('modal-sua-thuoc'); // thay id mới

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
