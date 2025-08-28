#include <iostream>
#include <vector>

using namespace std;

// Hàm tính số cách phân tích số n thành tổng các số nguyên dương
int countPartitions(int n) {
    // Tạo mảng dp để lưu số cách phân tích từ 0 đến n
    vector<int> dp(n + 1, 0);
    dp[0] = 1; // Số 0 có đúng 1 cách phân tích

    // Quy hoạch động: tính dp[i] cho mọi i từ 1 đến n
    for (int i = 1; i <= n; ++i) {
        for (int j = 0; j < i; ++j) {
            dp[i] += dp[j];
        }
    }

    return dp[n];
}

int main() {
    // Nhập giá trị n
    int n;
    cout << "Nhap so tu nhien n (< 100): ";
    cin >> n;

    if (n >= 100) {
        cout << "Gia tri n phai nho hon 100!" << endl;
        return 1;
    }

    // Tính số cách phân tích và in kết quả
    int result = countPartitions(n);
    cout << "So cach phan tich so " << n << " thanh tong cac so nguyen duong la: " << result << endl;

    return 0;
}
