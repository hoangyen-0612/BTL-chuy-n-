#include <iostream>
using namespace std;

const int MAX = 10;

int board[MAX][MAX];
int dx[] = { -2, -1, 1, 2, 2, 1, -1, -2 };
int dy[] = { 1, 2, 2, 1, -1, -2, -2, -1 };
int n;

bool isValid(int x, int y) {
return (x >= 0 && x < n && y >= 0 && y < n && board[x][y] == 0);
}

bool solve(int x, int y, int moveCount) { 
    if (moveCount == n * n) {
    return true;
}

for (int i = 0; i < 8; i++) { 
    int nextX = x + dx[i]; 
    int nextY = y + dy[i];

if (isValid(nextX, nextY)) { 
    board[nextX][nextY] = moveCount + 1;

if (solve(nextX, nextY, moveCount + 1)) { 
    return true;
}

board[nextX][nextY] = 0;//quay lùi
}
}

return false;
}

void printBoard() {
for (int i = 0; i < n; i++) { 
    for (int j = 0; j < n; j++) {
cout << board[i][j] << "\t";
}
cout << endl;
}
}

int main() { 
int x0, y0;
cout << "nhap kich co co ban co (n): "; 
cin >> n;
cout << "nhap diem xuat phat (x0, y0): "; 
cin >> x0 >> y0;

// Initialize the chessboard 
for (int i = 0; i < n; i++) {
for (int j = 0; j < n; j++) { 
    board[i][j] = 0;
}
}

board[x0][y0] = 1;

if (solve(x0, y0, 1)) {
cout << "ban co : " << endl; printBoard();
} 
else {
cout << "khong tim thay du lieu." << endl;
}

return 0;
}
