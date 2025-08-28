#include <iostream>
#include <vector>
#include <algorithm>
using namespace std;
// Cấu trúc lưu trữ thông tin của mỗi mặt hàng
struct MatHang {
    double trongLuong;
    double giaTri;
    double tyLe; // Giá trị/Trọng lượng
};
      // Hàm so sánh tỷ lệ giá trị/trọng lượng, để sắp xếp theo thứ tự giảm dần
bool soSanh(MatHang a, MatHang b) {
    return a.tyLe > b.tyLe;
}
// Hàm giải bài toán cái túi dạng phân số
double baiToanCaiTui(vector<MatHang>& matHang, double M) {
    // Sắp xếp các mặt hàng theo thứ tự giảm dần của tỷ lệ giá trị/trọng lượng
    sort(matHang.begin(), matHang.end(), soSanh);

    double tongGiaTri = 0.0; // Tổng giá trị đạt được
    double trongLuongHienTai = 0.0; // Tổng trọng lượng hiện tại của túi

    for (auto& mh : matHang) {
        if (trongLuongHienTai + mh.trongLuong <= M) {
            // Nếu túi còn đủ sức chứa, thêm toàn bộ mặt hàng
          trongLuongHienTai += mh.trongLuong;
            tongGiaTri += mh.giaTri;
        } else {
            // Nếu không đủ sức chứa, chỉ thêm phần còn lại
            double phanConLai = M - trongLuongHienTai;
            tongGiaTri += mh.tyLe * phanConLai;
            break; // Túi đã đầy
        }
    }
    return tongGiaTri;
}
int main() {
    // Dữ liệu đầu vào
    vector<MatHang> matHang = {
        {18, 25, 0}, // A
        {15, 24, 0}, // B
        {10, 15, 0}  // C
    };
    double M = 20; // Sức chứa của túi
    // Tính tỷ lệ giá trị/trọng lượng cho từng mặt hàng
    for (auto& mh : matHang) {
        mh.tyLe = mh.giaTri / mh.trongLuong;
    }
         // Gọi hàm giải bài toán
    double ketQua = baiToanCaiTui(matHang, M);
    cout << "Tong gia tri toi da co the dat duoc la: " << ketQua << endl;
    return 0;
}
