#include <iostream>
#include <vector>
using namespace std;

// Hàm chia để trị để tính factorial
long long factorialDivideAndConquer(int start, int end) {
    if (start > end) return 1;      // Nếu không còn số nào để nhân
    if (start == end) return start; // Nếu chỉ còn một số
    int mid = (start + end) / 2;
    // Chia nhỏ bài toán và kết hợp
    long long left = factorialDivideAndConquer(start, mid);
    long long right = factorialDivideAndConquer(mid + 1, end);
    return left * right;
}
long long factorialDC(int n) {
    return factorialDivideAndConquer(1, n);
}
// Hàm tính factorial bằng quy hoạch động
long long factorialDP(int n) {
    vector<long long> dp(n + 1, 1); // Khởi tạo mảng dp
    for (int i = 2; i <= n; i++) {
        dp[i] = dp[i - 1] * i; // Tính i! từ (i-1)!
    }
    return dp[n];
}

int main() {
    int n;
    cout << "Nhập giá trị n: ";
    cin >> n;

    cout << "Tính n! bằng phương pháp chia để trị: " << endl;
    cout << n << "! = " << factorialDC(n) << endl;

    cout << "Tính n! bằng phương pháp quy hoạch động: " << endl;
    cout << n << "! = " << factorialDP(n) << endl;

    return 0;
}
