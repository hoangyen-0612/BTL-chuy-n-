<?php
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

