#include <iostream>
#include <vector>
#include <algorithm>
using namespace std;

// Cấu trúc lưu thông tin một hoạt động
struct HoatDong {
    int id;       // Số thứ tự hoạt động
    int batDau;   // Thời gian bắt đầu
    int ketThuc;  // Thời gian kết thúc
};

// Hàm so sánh để sắp xếp các hoạt động theo thời gian kết thúc
bool soSanh(HoatDong a, HoatDong b) {
    return a.ketThuc < b.ketThuc;
}

// Hàm giải bài toán lập lịch tham lam
vector<int> lapLichThamLam(vector<HoatDong>& hoatDong) {
    // Sắp xếp các hoạt động theo thời gian kết thúc
    sort(hoatDong.begin(), hoatDong.end(), soSanh);

    vector<int> ketQua;  // Lưu danh sách các hoạt động được chọn
    int ketThucTruoc = 0; // Thời gian kết thúc của hoạt động trước (ban đầu là 0)

    for (auto& hd : hoatDong) {
        if (hd.batDau >= ketThucTruoc) {
            // Nếu hoạt động không trùng lặp thời gian, chọn hoạt động này
            ketQua.push_back(hd.id);
            ketThucTruoc = hd.ketThuc; // Cập nhật thời gian kết thúc
        }
    }

    return ketQua;
}

int main() {
    // Dữ liệu đầu vào: danh sách các hoạt động
    vector<HoatDong> hoatDong = {
        {1, 1, 4}, {2, 3, 5}, {3, 0, 6}, {4, 5, 7}, {5, 3, 8},
        {6, 5, 9}, {7, 6, 10}, {8, 8, 11}, {9, 8, 12}, {10, 2, 13}, {11, 12, 16}
    };

    // Gọi hàm giải bài toán lập lịch
    vector<int> ketQua = lapLichThamLam(hoatDong);

    // In kết quả
    cout << "Cac hoat dong duoc chon la:" << endl;
    for (int id : ketQua) {
        cout << "Hoat dong " << id << endl;
    }

    return 0;
}
