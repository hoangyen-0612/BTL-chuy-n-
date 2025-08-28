#include <iostream>
 #include <algorithm>
  using namespace std;

struct Item { int weight; int value;
};

bool compare(Item a, Item b) {
return (a.value / a.weight) > (b.value / b.weight);
}

int knapsack(int w, Item items[], int n) {
// sắp xếp giá trị giảm dần 
sort(items, items + n, compare);

int max_value = 0;
int current_weight = 0;

for (int i = 0; i < n; i++) {
if (current_weight + items[i].weight <= w) { 
    current_weight += items[i].weight; 
    max_value += items[i].value;
}
}

return max_value;
}

int main() {
int w = 10; // Khối lượng tối đa của túi 
int n = 5; // Số lượng đồ vật

Item items[n] = {{2, 10}, {3, 5}, {5, 15}, {7, 7}, {1, 6}};

int max_value = knapsack(w, items, n);
cout << "Giá trị lớn nhất của túi: " << max_value << endl; 
 return 0;
}
