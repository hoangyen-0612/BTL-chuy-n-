<?php
include("connect.php");
mysqli_set_charset($conn, "utf8");

// ==================== XOÁ KHO ====================
if (isset($_GET['delete'])) {
    $ma_kho = $_GET['delete'];

    // Bỏ liên kết thuốc
    $conn->query("UPDATE thuoc SET ma_kho=NULL WHERE ma_kho='$ma_kho'");

    // Xoá kho
    $conn->query("DELETE FROM kho WHERE ma_kho='$ma_kho'");
    header("Location: quanly.php?page=quan-ly-kho");
    exit();
}

// ==================== SỬA KHO ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['suaKho'])) {
    $maKho  = $_POST['maKho'];
    $slNhap = intval($_POST['slNhap']);
    $slGiao = intval($_POST['slGiao']);
    $tonKho = $slNhap - $slGiao;

    $sql = "UPDATE kho SET sl_nhap='$slNhap', sl_giao='$slGiao', ton_kho='$tonKho' WHERE ma_kho='$maKho'";
    if ($conn->query($sql)) {
        header("Location: quanly.php?page=quan-ly-kho");
        exit();
    } else {
        echo "Lỗi sửa kho: " . $conn->error;
    }
}

// ==================== TÌM KIẾM ====================
$keyword = isset($_GET['search']) ? $_GET['search'] : "";
$sql = "SELECT k.ma_kho, t.ten_thuoc, k.sl_nhap, k.sl_giao, k.ton_kho 
        FROM kho k 
        LEFT JOIN thuoc t ON t.ma_kho = k.ma_kho
        WHERE t.ten_thuoc LIKE '%$keyword%' OR k.ma_kho LIKE '%$keyword%'";
$result = $conn->query($sql);
?>

<!-- Quản lý kho -->
<div id="quan-ly-kho" class="page">
    <div class="page-header">
        <h1 class="page-title">Quản lý kho</h1>
        <p class="page-subtitle">Thông tin tồn kho và xuất nhập</p>
    </div>

    <div class="table-container">
        <div class="table-header">
            <div class="search-filters">
                <form method="get" action="quanly.php">
                    <input type="hidden" name="page" value="quan-ly-kho">
                    <input type="text" class="search-input" name="search" placeholder="Tìm kiếm kho..." value="<?php echo $keyword; ?>">
                </form>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Mã kho</th>
                    <th>Tên thuốc</th>
                    <th>SL nhập</th>
                    <th>SL giao</th>
                    <th>Tồn kho</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['ma_kho']; ?></td>
                    <td><?php echo $row['ten_thuoc']; ?></td>
                    <td><?php echo $row['sl_nhap']; ?></td>
                    <td><?php echo $row['sl_giao']; ?></td>
                    <td><?php echo $row['ton_kho']; ?></td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="suaKho('<?php echo $row['ma_kho']; ?>','<?php echo $row['sl_nhap']; ?>','<?php echo $row['sl_giao']; ?>')">✏️ Sửa</button>
                        <a href="quan-ly-kho.php?delete=<?php echo $row['ma_kho']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xoá kho này?')">🗑️ Xóa</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal sửa kho -->
<div id="modal-sua-kho" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Sửa kho</h3>
            <button class="close-btn" onclick="dongModal('modal-sua-kho')">&times;</button>
        </div>
        <form method="post">
            <input type="hidden" name="suaKho" value="1">
            <input type="hidden" name="maKho" id="suaMaKho">

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">SL nhập</label>
                    <input type="number" class="form-input" name="slNhap" id="suaSlNhap" required>
                </div>
                <div class="form-group">
                    <label class="form-label">SL giao</label>
                    <input type="number" class="form-input" name="slGiao" id="suaSlGiao" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Tồn kho</label>
                <input type="number" class="form-input" id="suaTonKho" readonly>
            </div>

            <div class="form-actions">
                <button type="button" class="btn" onclick="dongModal('modal-sua-kho')">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
function moModal(id){ document.getElementById(id).style.display='block'; }
function dongModal(id){ document.getElementById(id).style.display='none'; }

function suaKho(ma, slNhap, slGiao){
    document.getElementById('suaMaKho').value = ma;
    document.getElementById('suaSlNhap').value = slNhap;
    document.getElementById('suaSlGiao').value = slGiao;
    document.getElementById('suaTonKho').value = slNhap - slGiao;
    moModal('modal-sua-kho');
}

// Cập nhật tồn kho tự động khi sửa
document.getElementById('suaSlNhap').addEventListener('input', updateTonKho);
document.getElementById('suaSlGiao').addEventListener('input', updateTonKho);

function updateTonKho(){
    let slNhap = parseInt(document.getElementById('suaSlNhap').value) || 0;
    let slGiao = parseInt(document.getElementById('suaSlGiao').value) || 0;
    document.getElementById('suaTonKho').value = slNhap - slGiao;
}
</script>
