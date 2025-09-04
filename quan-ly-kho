<?php
include("connect.php");

// ==================== AUTO TĂNG MÃ KHO ====================
function taoMaKho($conn){
    $sql = "SELECT ma_kho FROM kho ORDER BY ma_kho DESC LIMIT 1";
    $result = $conn->query($sql);
    if($row = $result->fetch_assoc()){
        $last_id = intval(substr($row['ma_kho'], 1)) + 1;
    } else {
        $last_id = 1;
    }
    return "K" . str_pad($last_id, 3, "0", STR_PAD_LEFT);
}

// ==================== THÊM KHO ====================
if (isset($_POST['action']) && $_POST['action'] == 'them') {
    $ma_kho = taoMaKho($conn);
    $ma_hang = $_POST['ma_hang'];
    $sl_nhap = $_POST['sl_nhap'];
    $sl_giao = $_POST['sl_giao'];
    $ton_kho = $sl_nhap - $sl_giao;

    $stmt = $conn->prepare("INSERT INTO kho (ma_kho, ma_hang, sl_nhap, sl_giao, ton_kho) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiii", $ma_kho, $ma_hang, $sl_nhap, $sl_giao, $ton_kho);
    $stmt->execute();
    header("Location: quanly.php?page=quan-ly-kho");
    exit();
}

// ==================== SỬA KHO ====================
if (isset($_POST['action']) && $_POST['action'] == 'sua') {
    $ma_kho = $_POST['ma_kho'];
    $ma_hang = $_POST['ma_hang'];
    $sl_nhap = $_POST['sl_nhap'];
    $sl_giao = $_POST['sl_giao'];
    $ton_kho = $sl_nhap - $sl_giao;

    $stmt = $conn->prepare("UPDATE kho SET ma_hang=?, sl_nhap=?, sl_giao=?, ton_kho=? WHERE ma_kho=?");
    $stmt->bind_param("siiis", $ma_hang, $sl_nhap, $sl_giao, $ton_kho, $ma_kho);
    $stmt->execute();
    header("Location: quanly.php?page=quan-ly-kho");
    exit();
}

// ==================== XÓA KHO ====================
if (isset($_GET['delete'])) {
    $ma_kho = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM kho WHERE ma_kho=?");
    $stmt->bind_param("s", $ma_kho);
    $stmt->execute();
    header("Location: quanly.php?page=quan-ly-kho");
    exit();
}

// ==================== LẤY DANH SÁCH KHO ====================
$sql = "SELECT k.*, t.ten_thuoc 
        FROM kho k 
        JOIN thuoc t ON k.ma_hang = t.ma_thuoc 
        ORDER BY k.ma_kho ASC";
$kho_list = $conn->query($sql);

// Lấy danh sách thuốc để hiển thị trong select
$thuoc_list = $conn->query("SELECT ma_thuoc, ten_thuoc FROM thuoc ORDER BY ten_thuoc ASC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý kho</title>
    <link rel="stylesheet" href="css.css">
    <style>
        .hidden { display:none; }
        .modal { position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); display:flex; align-items:center; justify-content:center; }
        .modal-content { background:#fff; padding:20px; border-radius:8px; width:500px; box-shadow:0 5px 15px rgba(0,0,0,0.3); }
        .close { float:right; font-size:22px; cursor:pointer; }
        .btn { padding:5px 10px; border:none; cursor:pointer; border-radius:4px; }
        .btn-primary { background:#007bff; color:#fff; }
        .btn-danger { background:#dc3545; color:#fff; }
        .btn-info { background:#17a2b8; color:#fff; }
        table { width:100%; border-collapse:collapse; margin-top:15px; }
        table, th, td { border:1px solid #ccc; }
        th, td { padding:10px; text-align:center; }
    </style>
</head>
<body>
<div id="quan-ly-kho" class="page">
    <div class="page-header">
        <h1 class="page-title">Quản lý kho</h1>
        <p class="page-subtitle">Thông tin tồn kho và xuất nhập</p>
    </div>

    <div class="table-container">
        <div class="table-header">
            <div class="search-filters">
                <input type="text" id="searchInput" class="search-input" placeholder="Tìm kiếm mã kho, mã thuốc, tên thuốc..." onkeyup="timKiem()">
            </div>
            <button class="btn btn-primary" onclick="moModal('modal-them')">➕ Thêm kho mới</button>
        </div>

        <table id="khoTable">
            <thead>
                <tr>
                    <th>Mã kho</th>
                    <th>Mã hàng</th>
                    <th>Tên thuốc</th>
                    <th>SL nhập</th>
                    <th>SL giao</th>
                    <th>Tồn kho</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $kho_list->fetch_assoc()){ ?>
                <tr>
                    <td><?= $row['ma_kho'] ?></td>
                    <td><?= $row['ma_hang'] ?></td>
                    <td><?= $row['ten_thuoc'] ?></td>
                    <td><?= $row['sl_nhap'] ?></td>
                    <td><?= $row['sl_giao'] ?></td>
                    <td><?= $row['ton_kho'] ?></td>
                    <td>
                        <button class="btn btn-info" onclick="suaKho('<?= $row['ma_kho'] ?>','<?= $row['ma_hang'] ?>','<?= $row['sl_nhap'] ?>','<?= $row['sl_giao'] ?>')">✏️ Sửa</button>
                        <a href="?delete=<?= $row['ma_kho'] ?>" class="btn btn-danger" onclick="return confirm('Xóa kho này?')">🗑️ Xóa</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Thêm -->
<div id="modal-them" class="modal hidden">
    <div class="modal-content">
        <span class="close" onclick="dongModal('modal-them')">&times;</span>
        <h2>Thêm kho mới</h2>
        <form method="POST">
            <input type="hidden" name="action" value="them">
            <label>Thuốc:</label>
            <select name="ma_hang" required>
                <option value="">-- Chọn thuốc --</option>
                <?php while($t = $thuoc_list->fetch_assoc()){ ?>
                    <option value="<?= $t['ma_thuoc'] ?>"><?= $t['ma_thuoc'] ?> - <?= $t['ten_thuoc'] ?></option>
                <?php } ?>
            </select><br><br>
            <label>SL nhập:</label>
            <input type="number" name="sl_nhap" required><br><br>
            <label>SL giao:</label>
            <input type="number" name="sl_giao" value="0" required><br><br>
            <button type="submit" class="btn btn-primary">Thêm</button>
        </form>
    </div>
</div>

<!-- Modal Sửa -->
<div id="modal-sua" class="modal hidden">
    <div class="modal-content">
        <span class="close" onclick="dongModal('modal-sua')">&times;</span>
        <h2>Sửa kho</h2>
        <form method="POST">
            <input type="hidden" name="action" value="sua">
            <input type="hidden" name="ma_kho" id="edit_ma_kho">
            <label>Thuốc:</label>
            <select name="ma_hang" id="edit_ma_hang" required>
                <?php
                $thuoc_list2 = $conn->query("SELECT ma_thuoc, ten_thuoc FROM thuoc ORDER BY ten_thuoc ASC");
                while($t = $thuoc_list2->fetch_assoc()){ ?>
                    <option value="<?= $t['ma_thuoc'] ?>"><?= $t['ma_thuoc'] ?> - <?= $t['ten_thuoc'] ?></option>
                <?php } ?>
            </select><br><br>
            <label>SL nhập:</label>
            <input type="number" name="sl_nhap" id="edit_sl_nhap" required><br><br>
            <label>SL giao:</label>
            <input type="number" name="sl_giao" id="edit_sl_giao" required><br><br>
            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        </form>
    </div>
</div>

<script>
function moModal(id){ document.getElementById(id).classList.remove('hidden'); }
function dongModal(id){ document.getElementById(id).classList.add('hidden'); }

function suaKho(ma_kho, ma_hang, sl_nhap, sl_giao){
    document.getElementById('edit_ma_kho').value = ma_kho;
    document.getElementById('edit_ma_hang').value = ma_hang;
    document.getElementById('edit_sl_nhap').value = sl_nhap;
    document.getElementById('edit_sl_giao').value = sl_giao;
    moModal('modal-sua');
}

function timKiem(){
    let keyword = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll("#khoTable tbody tr").forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(keyword) ? "" : "none";
    });
}
</script>
</body>
</html>

