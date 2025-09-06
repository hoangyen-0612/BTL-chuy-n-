<?php
include("connect.php");

$sql = "SELECT ma_nha_cung_cap, ten_nha_cung_cap, dia_chi, dien_thoai, created_at 
        FROM nhacungcap";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td class='px-6 py-4 text-sm font-medium text-gray-900'>{$row['ma_nha_cung_cap']}</td>
                <td class='px-6 py-4 text-sm text-gray-900'>{$row['ten_nha_cung_cap']}</td>
                <td class='px-6 py-4 text-sm text-gray-900'>{$row['dia_chi']}</td>
                <td class='px-6 py-4 text-sm text-gray-900'>{$row['dien_thoai']}</td>
                <td class='px-6 py-4 text-sm text-gray-900'>{$row['created_at']}</td>
                <td class='px-6 py-4 text-sm font-medium'>
                    <button class='text-blue-600 hover:text-blue-900 mr-3'>Sửa</button>
                    <button class='text-red-600 hover:text-red-900'>Xóa</button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6' class='px-6 py-4 text-center text-sm text-gray-500'>Chưa có dữ liệu</td></tr>";
}

$conn->close();
?>

