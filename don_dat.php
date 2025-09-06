<?php

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
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn đặt hàng - Hiệu thuốc</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c7fb8;
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .status {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
        }
        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }
        .status-shipping {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-processing {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-pending {
            background-color: #f8d7da;
            color: #721c24;
        }
        .total-row {
            font-weight: bold;
            background-color: #e9ecef;
        }
        .search-box {
            margin-bottom: 20px;
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>QUẢN LÝ ĐƠN ĐẶT HÀNG</h1>
        
        <input type="text" id="searchInput" class="search-box" placeholder="Tìm kiếm theo tên khách hàng, số điện thoại hoặc trạng thái...">
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên khách hàng</th>
                    <th>Số điện thoại</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody id="orderTable">
                <?php 
                $totalAll = 0;
                foreach ($orders as $order): 
                    $totalAll += $order['total_amount'];
                ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo $order['customer_name']; ?></td>
                    <td><?php echo $order['customer_phone']; ?></td>
                    <td><?php echo $order['order_date']; ?></td>
                    <td><?php echo formatCurrency($order['total_amount']); ?></td>
                    <td><span class="status <?php echo getStatusClass($order['status']); ?>"><?php echo $order['status']; ?></span></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="4">TỔNG CỘNG</td>
                    <td><?php echo formatCurrency($totalAll); ?></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        // Chức năng tìm kiếm
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let input = this.value.toLowerCase();
            let table = document.getElementById('orderTable');
            let rows = table.getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length - 1; i++) { // -1 để bỏ qua dòng tổng cộng
                let cells = rows[i].getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < cells.length; j++) {
                    let cellText = cells[j].textContent.toLowerCase();
                    if (cellText.indexOf(input) > -1) {
                        found = true;
                        break;
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        });
    </script>
</body>
</html>

