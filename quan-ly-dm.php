<?php
include 'connect.php';
/* ====== THÊM MỚI ====== */
if (isset($_POST['addDanhMuc'])) {
    $ten  = trim($_POST['ten_danh_muc']);
    $mota = trim($_POST['mo_ta']);

    // Lấy mã hiện lớn nhất
    $res = $conn->query(
        "SELECT ma_danh_muc
         FROM danh_muc_thuoc
         ORDER BY CAST(SUBSTRING(ma_danh_muc,3) AS UNSIGNED) DESC
         LIMIT 1"
    );

    $nextNum = 1;
    if ($row = $res->fetch_assoc()) {
        $current = (int)substr($row['ma_danh_muc'], 2);
        $nextNum = $current + 1;
    }
    $newCode = 'DM' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

    $stmt = $conn->prepare(
        "INSERT INTO danh_muc_thuoc (ma_danh_muc, ten_danh_muc, mo_ta)
         VALUES (?, ?, ?)"
    );
    $stmt->bind_param('sss', $newCode, $ten, $mota);
    $stmt->execute();
    $stmt->close();
     header("Location: quanly.php?page=quan-ly-dm");
    exit;
}

/* ====== SỬA ====== */
if (isset($_POST['editDanhMuc'])) {
    $id   = $_POST['ma_danh_muc'];
    $ten  = trim($_POST['ten_danh_muc']);
    $mota = trim($_POST['mo_ta']);

    $stmt = $conn->prepare(
        "UPDATE danh_muc_thuoc
         SET ten_danh_muc=?, mo_ta=?
         WHERE ma_danh_muc=?"
    );
    $stmt->bind_param('sss', $ten, $mota, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: quanly.php?page=quan-ly-dm");
    exit;
}
/* ====== XÓA ====== */
if (isset($_POST['delete_ma_danh_muc'])) {
    $id = $_POST['delete_ma_danh_muc'];
    $stmt = $conn->prepare(
        "DELETE FROM danh_muc_thuoc WHERE ma_danh_muc=?"
    );
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $stmt->close();
    header("Location: quanly.php?page=quan-ly-dm");
    exit;
}
/* ====== TÌM KIẾM ====== */
$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT * FROM danh_muc_thuoc";
if ($keyword !== '') {
    $k = $conn->real_escape_string($keyword);
    $sql .= " WHERE ten_danh_muc LIKE '%$k%' OR mo_ta LIKE '%$k%' OR ma_danh_muc LIKE '%$k%'";
}
$sql .= " ORDER BY CAST(SUBSTRING(ma_danh_muc,3) AS UNSIGNED) ASC";
$list = $conn->query($sql);
?>
$sql .= " ORDER BY CAST(SUBSTRING(ma_danh_muc,3) AS UNSIGNED) ASC";
$list = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<title>Quản lý danh mục thuốc</title>
<link rel="stylesheet" href="css.css">
</style>
</head>
<body>
<div class="page-header">
    <h1 class="page-title">Quản lý danh mục thuốc</h1>
    <p class="page-subtitle">Danh sách và thông tin chi tiết danh mục thuốc</p>
</div>

<div class="table-container">
    <div class="table-header">
        <div class="search-filters">
            <form method="get">
                <input type="text" class="search-input" name="search"
                       placeholder="Tìm kiếm danh mục..."
                       value="<?php echo htmlspecialchars($keyword); ?>">
            </form>
        </div>
        <button class="btn btn-primary" onclick="moModal('modal-them')">Thêm danh mục</button>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Mã</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $list->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['ma_danh_muc']; ?></td>
                <td><?php echo htmlspecialchars($row['ten_danh_muc']); ?></td>
                <td><?php echo htmlspecialchars($row['mo_ta']); ?></td>
                <td>
                    <button class="btn btn-info btn-sm"
                        onclick="suaDM(
                            '<?php echo $row['ma_danh_muc']; ?>',
                            '<?php echo htmlspecialchars($row['ten_danh_muc'], ENT_QUOTES); ?>',
                            '<?php echo htmlspecialchars($row['mo_ta'], ENT_QUOTES); ?>'
                        )">Sửa</button>

                    <form method="post" style="display:inline"
                          onsubmit="return confirm('Xóa danh mục này?')">
                        <input type="hidden" name="delete_ma_danh_muc"
                               value="<?php echo htmlspecialchars($row['ma_danh_muc'], ENT_QUOTES); ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<!-- Modal Thêm -->
<div id="modal-them" class="modal">
  <div class="modal-content">
    <div class="modal-header">
        <h3>Thêm danh mục</h3>
        <button class="close-btn" onclick="dongModal('modal-them')">&times;</button>
    </div>
    <form method="post">
        <input type="hidden" name="addDanhMuc" value="1">
        <label>Tên danh mục</label>
        <input type="text" name="ten_danh_muc" class="form-input" required>
        <label>Mô tả</label>
        <textarea name="mo_ta" class="form-input"></textarea>
        <button type="submit" class="btn btn-primary">Lưu</button>
    </form>
  </div>
</div>

<!-- Modal Sửa -->
<div id="modal-sua" class="modal">
  <div class="modal-content">
    <div class="modal-header">
        <h3>Sửa danh mục</h3>
        <button class="close-btn" onclick="dongModal('modal-sua')">&times;</button>
    </div>
    <form method="post">
        <input type="hidden" name="editDanhMuc" value="1">
        <input type="hidden" name="ma_danh_muc" id="editMa">
        <label>Tên danh mục</label>
        <input type="text" name="ten_danh_muc" id="editTen" class="form-input" required>
        <label>Mô tả</label>
        <textarea name="mo_ta" id="editMoTa" class="form-input"></textarea>
        <button type="submit" class="btn btn-primary">Lưu</button>
    </form>
  </div>
</div>

<script>
function moModal(id){document.getElementById(id).style.display='block';}
function dongModal(id){document.getElementById(id).style.display='none';}
function suaDM(id,ten,mota){
    document.getElementById('editMa').value = id;
    document.getElementById('editTen').value = ten;
    document.getElementById('editMoTa').value = mota;
    moModal('modal-sua');
}
</script>
</body>
</html>
