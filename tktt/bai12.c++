#include <iostream> 
using namespace std;

int knapsack(int w, int n, int weights[], int values[]) { 
    int dp[n+1][w+1];
for (int i = 0; i <= n; i++) { for (int j = 0; j <= w; j++) {
if (i == 0 || j == 0) dp[i][j] = 0;
else if (weights[i-1] <= j)
dp[i][j] = max(values[i-1] + dp[i-1][j-weights[i-1]], dp[i-1][j]); 
else
dp[i][j] = dp[i-1][j];
}
}
return dp[n][w];
}

int main() {
int w = 10; // Khối lượng tối đa của túi 
int n = 5; // Số lượng đồ vật
int weights[] = {2, 3, 5, 7,1}; // Khối lượng của từng đồ vật
int values[] = {10, 5, 15, 7,6}; // Giá trị của từng đồ vật

int max_value = knapsack(w, n, weights, values);
cout << "Gia tri lon nhat tui co the chua: " << max_value << endl;

return 0;
}
