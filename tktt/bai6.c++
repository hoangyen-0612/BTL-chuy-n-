#include <iostream>
#include <vector>
using namespace std;

const int N = 8;
vector<int> columns(N);        // Mảng lưu vị trí cột của quân hậu trên từng hàng
vector<bool> col(N, false);    // Đánh dấu cột đã có quân hậu
vector<bool> diag1(2 * N, false); // Đánh dấu đường chéo chính
vector<bool> diag2(2 * N, false); // Đánh dấu đường chéo phụ

int solution_count = 0; // Đếm số cách giải

// Hàm in bàn cờ
void printSolution() {
    for (int i = 0; i < N; ++i) {
        for (int j = 0; j < N; ++j) {
            if (columns[i] == j)
                cout << "Q ";
            else
                cout << ". ";
        }
        cout << endl;
    }
    cout << "-----------------\n";
}

// Hàm quay lui để tìm cách đặt quân hậu
void solve(int row) {
    if (row == N) {
        // Nếu đã đặt đủ 8 quân hậu, in kết quả
        solution_count++;
        printSolution();
        return;
    }

    for (int j = 0; j < N; ++j) { // Thử đặt quân hậu ở từng cột
        if (!col[j] && !diag1[row - j + N] && !diag2[row + j]) {
            // Đặt quân hậu tại (row, j)
            columns[row] = j;
            col[j] = diag1[row - j + N] = diag2[row + j] = true;

            // Tiếp tục đặt quân hậu ở hàng tiếp theo
            solve(row + 1);

            // Quay lui: Gỡ quân hậu tại (row, j)
            col[j] = diag1[row - j + N] = diag2[row + j] = false;
        }
    }
}

int main() {
    cout << "Cac cach dat 8 quan hau tren ban co 8x8 la: \n";
    solve(0); // Bắt đầu từ hàng đầu tiên
    cout << "Tong so cach: " << solution_count << endl;
    return 0;
}
