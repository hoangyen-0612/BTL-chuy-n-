<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Khách hàng</title>
</head>
<body>
    <div id="khach-hang" class="page">
        <div class="page-header">
            <h1 class="page-title">Quản lý khách hàng</h1>
            <p class="page-subtitle">Thông tin khách hàng</p>
        </div>

        <div class="table-container">
            <div class="table-header">
                <div class="search-filters">
                    <input type="text" class="search-input" placeholder="Tìm kiếm khách hàng..." onkeyup="timKiem('kh', this.value)" />
                </div>
                <button class="btn btn-primary" onclick="moModal('modal-them-khach-hang')">
                    ➕ Thêm khách hàng
                </button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Mã khách</th>
                        <th>Tên khách hàng</th>
                        <th>Địa chỉ</th>
                        <th>Điện thoại</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="danh-sach-khach-hang">
                    <!-- Dữ liệu sẽ được thêm bằng JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal thêm khách hàng -->
    <div id="modal-them-khach-hang" class="modal hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Thêm khách hàng</h3>
                <button class="close-btn" onclick="dongModal('modal-them-khach-hang')">&times;</button>
            </div>
            <form onsubmit="themKhachHang(event)">
                <div class="form-group">
                    <label class="form-label">Tên khách hàng</label>
                    <input type="text" class="form-input" name="tenKhachHang" required />
                </div>
                <div class="form-group">
                    <label class="form-label">Địa chỉ</label>
                    <textarea class="form-textarea" name="diaChi" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Điện thoại</label>
                    <input type="tel" class="form-input" name="dienThoai" required />
                </div>
                <div class="form-actions">
                    <button type="button" class="btn" onclick="dongModal('modal-them-khach-hang')">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm khách hàng</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal sửa khách hàng -->
    <div id="modal-sua-khach-hang" class="modal hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Sửa khách hàng</h3>
                <button class="close-btn" onclick="dongModal('modal-sua-khach-hang')">&times;</button>
            </div>
            <form onsubmit="capNhatKhachHang(event)">
                <input type="hidden" name="maKhachHang" id="edit-maKhachHang" />
                <div class="form-group">
                    <label class="form-label">Tên khách hàng</label>
                    <input type="text" class="form-input" name="tenKhachHang" id="edit-tenKhachHang" required />
                </div>
                <div class="form-group">
                    <label class="form-label">Địa chỉ</label>
                    <textarea class="form-textarea" name="diaChi" id="edit-diaChi" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Điện thoại</label>
                    <input type="tel" class="form-input" name="dienThoai" id="edit-dienThoai" required />
                </div>
                <div class="form-actions">
                    <button type="button" class="btn" onclick="dongModal('modal-sua-khach-hang')">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Mảng lưu danh sách khách hàng
        let danhSachKhachHang = [];

        // Hàm tạo mã khách hàng tự động (ví dụ KH001, KH002,...)
        function taoMaKhachHang() {
            let maxId = 0;
            danhSachKhachHang.forEach(kh => {
                let num = parseInt(kh.maKhach.replace('KH', ''));
                if (num > maxId) maxId = num;
            });
            return 'KH' + String(maxId + 1).padStart(3, '0');
        }

        // Hàm hiển thị danh sách khách hàng lên bảng
        function hienThiDanhSach() {
            const tbody = document.getElementById('danh-sach-khach-hang');
            tbody.innerHTML = '';
            danhSachKhachHang.forEach(kh => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${kh.maKhach}</td>
                    <td>${kh.tenKhachHang}</td>
                    <td>${kh.diaChi}</td>
                    <td>${kh.dienThoai}</td>
                    <td>
                        <button class="btn btn-edit" onclick="moModalSuaKhachHang('${kh.maKhach}')">✏️ Sửa</button>
                        <button class="btn btn-delete" onclick="xoaKhachHang('${kh.maKhach}')">🗑️ Xóa</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Hàm mở modal
        function moModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        // Hàm đóng modal
        function dongModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        // Thêm khách hàng mới
        function themKhachHang(event) {
            event.preventDefault();
            const form = event.target;
            const tenKhachHang = form.tenKhachHang.value.trim();
            const diaChi = form.diaChi.value.trim();
            const dienThoai = form.dienThoai.value.trim();

            if (!tenKhachHang || !diaChi || !dienThoai) {
                alert('Vui lòng nhập đầy đủ thông tin!');
                return;
            }

            const maKhach = taoMaKhachHang();

            danhSachKhachHang.push({ maKhach, tenKhachHang, diaChi, dienThoai });
            hienThiDanhSach();
            dongModal('modal-them-khach-hang');
            form.reset();
        }

        // Mở modal sửa khách hàng và điền dữ liệu vào form
        function moModalSuaKhachHang(maKhach) {
            const kh = danhSachKhachHang.find(kh => kh.maKhach === maKhach);
            if (!kh) return alert('Không tìm thấy khách hàng!');

            document.getElementById('edit-maKhachHang').value = kh.maKhach;
            document.getElementById('edit-tenKhachHang').value = kh.tenKhachHang;
            document.getElementById('edit-diaChi').value = kh.diaChi;
            document.getElementById('edit-dienThoai').value = kh.dienThoai;

            moModal('modal-sua-khach-hang');
        }

        // Cập nhật thông tin khách hàng
        function capNhatKhachHang(event) {
            event.preventDefault();
            const form = event.target;
            const maKhach = form.maKhachHang.value;
            const tenKhachHang = form.tenKhachHang.value.trim();
            const diaChi = form.diaChi.value.trim();
            const dienThoai = form.dienThoai.value.trim();

            if (!tenKhachHang || !diaChi || !dienThoai) {
                alert('Vui lòng nhập đầy đủ thông tin!');
                return;
            }

            const index = danhSachKhachHang.findIndex(kh => kh.maKhach === maKhach);
            if (index === -1) return alert('Không tìm thấy khách hàng!');

            danhSachKhachHang[index] = { maKhach, tenKhachHang, diaChi, dienThoai };
            hienThiDanhSach();
            dongModal('modal-sua-khach-hang');
        }

        // Xóa khách hàng
        function xoaKhachHang(maKhach) {
            if (!confirm('Bạn có chắc muốn xóa khách hàng này?')) return;
            danhSachKhachHang = danhSachKhachHang.filter(kh => kh.maKhach !== maKhach);
            hienThiDanhSach();
        }

        // Hàm tìm kiếm khách hàng
        function timKiem(prefix, keyword) {
            keyword = keyword.toLowerCase();
            const tbody = document.getElementById('danh-sach-khach-hang');
            tbody.innerHTML = '';
            danhSachKhachHang.forEach(kh => {
                if (
                    kh.maKhach.toLowerCase().includes(keyword) ||
                    kh.tenKhachHang.toLowerCase().includes(keyword) ||
                    kh.diaChi.toLowerCase().includes(keyword) ||
                    kh.dienThoai.toLowerCase().includes(keyword)
                ) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${kh.maKhach}</td>
                        <td>${kh.tenKhachHang}</td>
                        <td>${kh.diaChi}</td>
                        <td>${kh.dienThoai}</td>
                        <td>
                            <button class="btn btn-edit" onclick="moModalSuaKhachHang('${kh.maKhach}')">✏️ Sửa</button>
                            <button class="btn btn-delete" onclick="xoaKhachHang('${kh.maKhach}')">🗑️ Xóa</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                }
            });
        }

        // Khởi tạo trang với dữ liệu mẫu
        document.addEventListener('DOMContentLoaded', () => {
            danhSachKhachHang = [
                { maKhach: 'KH001', tenKhachHang: 'Nguyễn Văn A', diaChi: 'Hà Nội', dienThoai: '0123456789' },
                { maKhach: 'KH002', tenKhachHang: 'Trần Thị B', diaChi: 'Hồ Chí Minh', dienThoai: '0987654321' }
            ];
            hienThiDanhSach();
        });
    </script>
</body>
</html> 
