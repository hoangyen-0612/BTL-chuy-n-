<?php
// --- PHẦN PHP XỬ LÝ ---
// Kết nối database
$host = 'localhost';
$dbname = 'pharmacy_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối database thất bại: " . $e->getMessage());
}

// Hàm xử lý input
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Hàm định dạng tiền tệ
function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . ' VNĐ';
}

// Hàm lấy class cho trạng thái
function getStatusClass($status) {
    switch ($status) {
        case 'Đã giao hàng':
            return 'status-delivered';
        case 'Đang giao hàng':
            return 'status-shipping';
        case 'Đang xử lý':
            return 'status-processing';
        case 'Chờ xác nhận':
            return 'status-pending';
        default:
            return 'status-default';
    }
}

// Xử lý form thêm đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_order'])) {
    $customer_name = sanitizeInput($_POST['customer_name']);
    $customer_phone = sanitizeInput($_POST['customer_phone']);
    $customer_address = sanitizeInput($_POST['customer_address']);
    $medicines = sanitizeInput($_POST['medicines']);
    $total_amount = (float)$_POST['total_amount'];
    $status = sanitizeInput($_POST['status']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_phone, customer_address, medicines, total_amount, status) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$customer_name, $customer_phone, $customer_address, $medicines, $total_amount, $status]);
        $success_message = "Thêm đơn hàng thành công!";
    } catch (PDOException $e) {
        $error_message = "Lỗi khi thêm đơn hàng: " . $e->getMessage();
    }
}

// Xử lý cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = sanitizeInput($_POST['new_status']);
    
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        $success_message = "Cập nhật trạng thái thành công!";
    } catch (PDOException $e) {
        $error_message = "Lỗi khi cập nhật trạng thái: " . $e->getMessage();
    }
}

// Xử lý xóa đơn hàng
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$delete_id]);
        $success_message = "Xóa đơn hàng thành công!";
    } catch (PDOException $e) {
        $error_message = "Lỗi khi xóa đơn hàng: " . $e->getMessage();
    }
}

// Lấy danh sách đơn hàng
try {
    $search_keyword = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
    
    if ($search_keyword) {
        $stmt = $pdo->prepare("SELECT * FROM orders 
                              WHERE customer_name LIKE ? OR customer_phone LIKE ? OR medicines LIKE ?
                              ORDER BY order_date DESC");
        $search_param = "%$search_keyword%";
        $stmt->execute([$search_param, $search_param, $search_param]);
    } else {
        $stmt = $pdo->query("SELECT * FROM orders ORDER BY order_date DESC");
    }
    
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi khi lấy dữ liệu đơn hàng: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng</title>
    <style>
        .status-delivered { color: green; }
        .status-shipping { color: orange; }
        .status-processing { color: blue; }
        .status-pending { color: gray; }
        .status-default { color: black; }
    </style>
</head>
<body>
    <h1>Thêm đơn hàng mới</h1>
    <?php if (isset($success_message)) echo "<p style='color:green;'>$success_message</p>"; ?>
    <?php if (isset($error_message)) echo "<p style='color:red;'>$error_message</p>"; ?>
    <form method="post" action="">
        <input type="text" name="customer_name" placeholder="Tên khách hàng" required><br>
        <input type="text" name="customer_phone" placeholder="Số điện thoại" required><br>
        <textarea name="customer_address" placeholder="Địa chỉ"></textarea><br>
        <textarea name="medicines" placeholder="Thuốc"></textarea><br>
        <input type="number" step="0.01" name="total_amount" placeholder="Tổng tiền" required><br>
        <select name="status">
            <option value="Chờ xác nhận">Chờ xác nhận</option>
            <option value="Đang xử lý">Đang xử lý</option>
            <option value="Đang giao hàng">Đang giao hàng</option>
            <option value="Đã giao hàng">Đã giao hàng</option>
        </select><br>
        <button type="submit" name="add_order">Thêm đơn hàng</button>
    </form>

    <h1>Danh sách đơn hàng</h1>
    <form method="get" action="">
        <input type="text" name="search" placeholder="Tìm kiếm..." value="<?php echo htmlspecialchars($search_keyword ?? ''); ?>">
        <button type="submit">Tìm</button>
    </form>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Khách hàng</th>
            <th>Điện thoại</th>
            <th>Địa chỉ</th>
            <th>Thuốc</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Ngày đặt</th>
        </tr>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?php echo $order['id']; ?></td>
            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
            <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
            <td><?php echo htmlspecialchars($order['customer_address']); ?></td>
            <td><?php echo htmlspecialchars($order['medicines']); ?></td>
            <td><?php echo formatCurrency($order['total_amount']); ?></td>
            <td class="<?php echo getStatusClass($order['status']); ?>"><?php echo htmlspecialchars($order['status']); ?></td>
            <td><?php echo $order['order_date']; ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <select name="new_status">
                        <option value="Chờ xác nhận" <?php if($order['status']=='Chờ xác nhận') echo 'selected'; ?>>Chờ xác nhận</option>
                        <option value="Đang xử lý" <?php if($order['status']=='Đang xử lý') echo 'selected'; ?>>Đang xử lý</option>
                        <option value="Đang giao hàng" <?php if($order['status']=='Đang giao hàng') echo 'selected'; ?>>Đang giao hàng</option>
                        <option value="Đã giao hàng" <?php if($order['status']=='Đã giao hàng') echo 'selected'; ?>>Đã giao hàng</option>
                    </select>
                    <button type="submit" name="update_status">Cập nhật</button>
                </form>
                <a href="?delete_id=<?php echo $order['id']; ?>" onclick="return confirm('Bạn có chắc muốn xóa đơn hàng này?');">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

