#include <iostream>
#include <climits>

using namespace std;

// Hàm tìm dãy con liên tiếp có tổng lớn nhất
int maxSubArraySum(int arr[], int low, int high)
{
// Trường hợp cơ bản: chỉ có một phần tử 
if (low == high)
return arr[low];

// Tìm chỉ số giữa
int mid = (low + high) / 2;

// Tìm dãy con liên tiếp có tổng lớn nhất trong phần đầu tiên (từ đầu đến mid) 
int leftMax = INT_MIN;
int sum = 0;
for (int i = mid; i >= low; i--)
{
sum += arr[i];
if (sum > leftMax) leftMax = sum;
}

// Tìm dãy con liên tiếp có tổng lớn nhất trong phần thứ hai (từ mid+1 đến cuối) 
int rightMax = INT_MIN;
sum = 0;
for (int i = mid + 1; i <= high; i++)
{
sum += arr[i];
if (sum > rightMax) rightMax = sum;
}

// Tìm dãy con liên tiếp có tổng lớn nhất chứa phần tử ở giữa (mid)
int crossMax = leftMax + rightMax;

// Trả về dãy con liên tiếp có tổng lớn nhất trong 3 trường hợp
return max(crossMax, max(maxSubArraySum(arr, low, mid), maxSubArraySum(arr, mid + 1, high)));
}

int main()
{
int arr[] = {2, -5, 4, -2, 6,-8 , 3, 1, 5, -7};
int n = sizeof(arr) / sizeof(arr[0]);

int maxSum = maxSubArraySum(arr, 0, n - 1);
cout << "Tong lon nhat cua day con lien tiep: " << maxSum << endl;

return 0;
}
