
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý hiệu thuốc</title>
    <link rel="stylesheet" href="css.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <span>💊</span>
                <span>Hệ Thống Quản Lý Hiệu Thuốc</span>
            </div>
            <div class="user-info">
                <span>Xin chào, <strong>Quản trị viên</strong></span>
                <a href="logout.php" class="logout-btn">🚪 Đăng xuất</a>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="menu-title">Quản lý chính</div>
            <a class="menu-item" href="quanly.php?page=trang-chu">📊 Trang chủ</a>
            <a class="menu-item" href="quanly.php?page=quan-ly-thuoc">💊 Quản lý thuốc</a>
            <a class="menu-item" href="quanly.php?page=quan-ly-kho">📦 Quản lý kho</a>

            <div class="menu-title">Giao dịch</div>
            <a class="menu-item" href="quanly.php?page=don-mua">🛒 Đơn mua</a>
            <a class="menu-item" href="quanly.php?page=phieu-nhap">⬇️ Phiếu nhập</a>
            <a class="menu-item" href="quanly.php?page=don-dat">📋 Đơn đặt</a>
            <a class="menu-item" href="quanly.php?page=hoa-don">🧾 Hóa đơn</a>

            <div class="menu-title">Đối tác</div>
            <a class="menu-item" href="quanly.php?page=nha-cung-cap">🚚 Nhà cung cấp</a>
            <a class="menu-item" href="quanly.php?page=khach-hang">👥 Khách hàng</a>
            <a class="menu-item" href="quanly.php?page=nhan-vien">👨‍💼 Nhân viên</a>
        </nav>

        <!-- Main content -->
        <main class="main-content">
            <?php
            if (!isset($_GET['page'])) {
                include("trang-chu.php"); // mặc định
            } else {
                switch ($_GET['page']) {
                    case "trang-chu": include("trang-chu.php"); break;
                    case "quan-ly-thuoc": include("quan-ly-thuoc.php"); break;
                    case "quan-ly-kho": include("quan-ly-kho.php"); break;
                    case "don-mua": include("don-mua.php"); break;
                    case "phieu-nhap": include("phieu-nhap.php"); break;
                    case "don-dat": include("don-dat.php"); break;
                    case "hoa-don": include("hoa-don.php"); break;
                    case "nha-cung-cap": include("nha-cung-cap.php"); break;
                    case "khach-hang": include("khach-hang.php"); break;
                    case "nhan-vien": include("nhan-vien.php"); break;
                    default: echo "<h2>Trang không tồn tại!</h2>";
                }
            }
            ?>
        </main>
    </div>
</body>
</html>
