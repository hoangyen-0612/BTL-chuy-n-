#include <iostream> 
using namespace std;

// Hàm hoán đổi giá trị của hai phần tử 
void swap(int* a, int* b) {
int temp = *a;
*a = *b;
*b = temp;
}
// Hàm chia dãy thành hai phần và trả về chỉ số pivot 
int partition(int arr[], int low, int high) {
int pivot = arr[high]; // Chọn phần tử cuối làm pivot 
int i = (low - 1); // Chỉ số của phần tử nhỏ hơn pivot

for (int j = low; j <= high - 1; j++) {
// Nếu phần tử hiện tại nhỏ hơn hoặc bằng pivot 
if (arr[j] <= pivot) {
i++; // Tăng chỉ số của phần tử nhỏ hơn pivot 
swap(&arr[i], &arr[j]);
}
}
swap(&arr[i + 1], &arr[high]); return (i + 1);
}

// Hàm thực hiện thuật toán QuickSort 
void quickSort(int arr[], int low, int high) {
if (low < high) {
// Tìm chỉ số pivot
int pi = partition(arr, low, high);

// Đệ quy sắp xếp các phần tử nhỏ hơn và lớn hơn pivot 
quickSort(arr, low, pi - 1);
quickSort(arr, pi + 1, high);
}
}

// Hàm in dãy số
void printArray(int arr[], int size) { 
    for (int i = 0; i < size; i++) {
cout << arr[i] << " ";
}
cout << endl;
}

int main() {
int arr[] = {10, 7, 8, 9, 1, 5};
int n = sizeof(arr) / sizeof(arr[0]);

cout << "day so truoc khi sap xep: "; 
printArray(arr, n);

quickSort(arr, 0, n - 1);
cout << "day so sau khi sap xep: "; 
printArray(arr, n);

return 0;
}
