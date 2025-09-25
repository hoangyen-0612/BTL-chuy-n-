<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quản lý hiệu thuốc</title>
<link rel="stylesheet" href="css.css">
<style>
  .menu-title {
  cursor: pointer;
  padding: 10px 14px;
  margin-top: 15px;
  border-radius: 6px;
  font-weight: 600;
 color: white;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);     
  transition: background .2s;
}
.menu-title:hover {
  background: #83b0ebff;       
}
.menu-title.open {
  background: #83b0ebff;       
}
.menu-group {
  display: none;
  flex-direction: column;
  margin: 6px 0 0 5px;
  border-left: 2px solid #e2e8f0;
  padding-left: 8px;
}
.menu-group a {
  color: #000;
  text-decoration: none;
  padding: 8px 12px;
  margin: 3px 0;
  border-radius: 6px;
  transition: background .2s;
  font-size: 15px;
}
.menu-group a:hover {
  background: #e2e8f0;
}
.menu-group a.active {
  background: #cbdcf7;       
  color: #000;
  font-weight: 600;
}
</style>
</head>
<body>
<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
$currentPage = isset($_GET['page']) ? $_GET['page'] : 'quanly.php?page=trang-chu';
?>
<header class="header">
    <div class="header-content">
        <div class="logo">
            <span>Hệ Thống Quản Lý Hiệu Thuốc</span>
        </div>
        <div class="user-info">
            <span>Xin chào, <strong>Đây là hệ thống quản lý hiệu thuốc</strong></span>
            <a href="logout.php" class="logout-btn">Đăng xuất</a>
        </div>
    </div>
</header>

<div class="container">
    <nav class="sidebar">
        <div class="menu-title">Quản lý chính</div>
        <div class="menu-group">
            <a class="<?php echo $currentPage=='trang-chu'?'active':''?>" href="quanly.php?page=trang-chu">Trang chủ</a>
            <a class="<?php echo $currentPage=='quan-ly-thuoc'?'active':''?>" href="quanly.php?page=quan-ly-thuoc">Quản lý thuốc</a>
            <a class="<?php echo $currentPage=='quan-ly-kho'?'active':''?>" href="quanly.php?page=quan-ly-kho">Quản lý kho</a>
           <a class="<?php echo $currentPage=='quan-ly-dm'?'active':''?>" href="quanly.php?page=quan-ly-dm">Quản lý danh mục</a>
        </div>
        <div class="menu-title">Giao dịch</div>
        <div class="menu-group">
            <a class="<?php echo $currentPage=='phieu-nhap'?'active':''?>" href="quanly.php?page=phieu-nhap">Nhập hàng & Phiếu nhập</a>
            <a class="<?php echo $currentPage=='hoa-don'?'active':''?>" href="quanly.php?page=hoa-don">Đơn hàng & Hóa đơn</a>
        </div>
        <div class="menu-title">Đối tác</div>
        <div class="menu-group">
            <a class="<?php echo $currentPage=='nha-cung-cap'?'active':''?>" href="quanly.php?page=nha-cung-cap">Nhà cung cấp</a>
            <a class="<?php echo $currentPage=='khach-hang'?'active':''?>" href="quanly.php?page=khach-hang">Khách hàng</a>
            <a class="<?php echo $currentPage=='nhan-vien'?'active':''?>" href="quanly.php?page=nhan-vien">Nhân viên</a>
        </div>
    </nav>

    <main class="main-content">
        <?php
        if (!isset($_GET['page'])) {
            include("trang-chu.php");
        } else {
            switch ($_GET['page']) {
                case "trang-chu": include("trang-chu.php"); break;
                case "quan-ly-thuoc": include("quan-ly-thuoc.php"); break;
                case "quan-ly-kho": include("quan-ly-kho.php"); break;
                case "quan-ly-dm": include("quan-ly-dm.php"); break;
                case "phieu-nhap": include("phieu-nhap.php"); break;
                case "hoa-don": include("hoa-don.php"); break;
                case "nha-cung-cap": include("nha-cung-cap.php"); break;
                case "khach-hang": include("khach-hang.php"); break;
                case "nhan-vien": include("nhan-vien.php"); break;
                case "logout":
                session_destroy();
                session_unset();
                header('Location: login.php');
                exit();
                break;
                default: echo "<h2>Trang không tồn tại!</h2>";
            }
        }
        ?>
    </main>
</div>

<script>
// Mở/đóng menu khi click
document.querySelectorAll('.menu-title').forEach(function(title){
    title.addEventListener('click', function(){
        title.classList.toggle('open');
        const group = title.nextElementSibling;
        group.style.display = (group.style.display === 'flex') ? 'none' : 'flex';
    });
});

// Mở nhóm chứa mục đang active khi load
const activeLink = document.querySelector('.menu-group a.active');
if(activeLink){
    const group = activeLink.closest('.menu-group');
    group.style.display = 'flex';
    group.previousElementSibling.classList.add('open');
}
</script>
</body>
</html>
