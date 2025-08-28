#include <iostream> 
#include <vector>

using namespace std;

void backtracking(vector<int>& a, vector<bool>& visited, vector<int>& subset, int sum, int target, int index) { 
    if (sum == target) {
// In ra dãy con có tổng bằng M
for (int i = 0; i < subset.size(); i++) { 
    cout << subset[i] << " ";
}
cout << endl;
}

for (int i = index; i < a.size(); i++) {
if (!visited[i] && sum + a[i] <= target) { 
    visited[i] = true; 
    subset.push_back(a[i]);
backtracking(a, visited, subset, sum + a[i], target, i + 1);
visited[i] = false; 
subset.pop_back();
}
}
}

void findSubsequences(vector<int>& a, int target) { 
    vector<bool> visited(a.size(), false);
    vector<int> subset;
backtracking(a, visited, subset, 0, target, 0);
}

int main() {
vector<int> a = {7, 1, 4, 3, 5, 6}; 
int target = 11;
findSubsequences(a, target); return 0;
}
