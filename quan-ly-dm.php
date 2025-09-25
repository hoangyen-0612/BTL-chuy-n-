<?php
include 'connect.php';
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
