#include <iostream>
#include <vector>
using namespace std;
void doiTien(int n, vector<int>& menhGia) {
    vector<int> soLuong(menhGia.size(), 0); // Lưu số lượng từng loại tiền
    int remaining = n;
    for (size_t i = 0; i < menhGia.size(); ++i) {
        soLuong[i] = remaining / menhGia[i]; // Số lượng tiền của mệnh giá hiện tại
        remaining %= menhGia[i];            // Cập nhật số tiền còn lại
    }
    if (remaining != 0) {
        cout << "Không thể đổi chính xác số tiền." << endl;
        return;
    }
    cout << "Cách đổi " << n << " như sau:\n";
    for (size_t i = 0; i < menhGia.size(); ++i) {
        if (soLuong[i] > 0) {
            cout << menhGia[i] << " x " << soLuong[i] << endl;
        }
    }
}
int main() {
    int n;
    cout << "Nhập số tiền cần đổi (n): ";
    cin >> n;
    vector<int> menhGia = {5000, 2000, 1000, 500}; // Các mệnh giá
    doiTien(n, menhGia); // Gọi hàm đổi tiền
    return 0;
}
