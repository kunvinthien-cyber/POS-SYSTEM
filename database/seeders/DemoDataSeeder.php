<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $owner = User::updateOrCreate(
                ['email' => 'owner@demo.com'],
                [
                    'name' => 'Demo Shop Owner',
                    'password' => Hash::make('password123'),
                    'role' => 'shop_owner',
                ]
            );

            $shop = Shop::updateOrCreate(
                ['name' => 'ហាងលក់ទំនិញទូទៅ (Demo Store)'],
                [
                    'owner_id' => $owner->id,
                    'phone' => '012 345 678',
                    'address' => 'ភ្នំពេញ',
                    'status' => 'active',
                ]
            );

            $owner->update(['shop_id' => $shop->id]);

            $cashier = User::updateOrCreate(
                ['email' => 'cashier@demo.com'],
                [
                    'name' => 'Demo Cashier',
                    'password' => Hash::make('password123'),
                    'role' => 'cashier',
                    'shop_id' => $shop->id,
                ]
            );

            $this->seedSettings($shop->id);
            $categories = $this->seedCategories($shop->id);
            $products = $this->seedProducts($shop->id, $categories);
            $this->seedOrders($shop->id, $cashier->id, $products);
        });
    }

    /** @return array<string, Category> */
    private function seedCategories(int $shopId): array
    {
        $definitions = [
            'beverages' => ['name' => 'ភេសជ្ជៈ', 'description' => 'Beverages'],
            'snacks' => ['name' => 'នំចំណី', 'description' => 'Snacks'],
            'groceries' => ['name' => 'គ្រឿងទេស', 'description' => 'Groceries'],
            'household' => ['name' => 'សម្ភារៈប្រើប្រាស់', 'description' => 'Household'],
        ];

        $categories = [];
        foreach ($definitions as $key => $definition) {
            $categories[$key] = Category::updateOrCreate(
                ['shop_id' => $shopId, 'name' => $definition['name']],
                ['description' => $definition['description'], 'status' => true]
            );
        }

        return $categories;
    }

    /** @param array<string, Category> $categories
     *  @return array<string, Product>
     */
    private function seedProducts(int $shopId, array $categories): array
    {
        $definitions = [
            'coca_cola' => ['name' => 'Coca Cola 330ml', 'barcode' => '8841000000011', 'category' => 'beverages', 'cost' => 0.45, 'price' => 0.75, 'stock' => 100, 'alert' => 10],
            'carabao' => ['name' => 'Carabao Energy Drink', 'barcode' => '8850389100012', 'category' => 'beverages', 'cost' => 0.55, 'price' => 0.90, 'stock' => 80, 'alert' => 10],
            'anchor_beer' => ['name' => 'Anchor Beer Can 330ml', 'barcode' => '8888200000013', 'category' => 'beverages', 'cost' => 0.70, 'price' => 1.10, 'stock' => 60, 'alert' => 12],
            'vital_water' => ['name' => 'Vital Water 500ml', 'barcode' => '8852000000014', 'category' => 'beverages', 'cost' => 0.20, 'price' => 0.40, 'stock' => 120, 'alert' => 20],
            'pringles' => ['name' => 'Pringles Original 42g', 'barcode' => '8888000000015', 'category' => 'snacks', 'cost' => 0.85, 'price' => 1.30, 'stock' => 50, 'alert' => 8],
            'oreo' => ['name' => 'Oreo Original 137g', 'barcode' => '7622300000016', 'category' => 'snacks', 'cost' => 0.65, 'price' => 1.00, 'stock' => 70, 'alert' => 10],
            'rice' => ['name' => 'Angkor Jasmine Rice 5kg', 'barcode' => '8850000000017', 'category' => 'groceries', 'cost' => 5.50, 'price' => 6.75, 'stock' => 35, 'alert' => 5],
            'fish_sauce' => ['name' => 'Tiparos Fish Sauce 700ml', 'barcode' => '8850000000018', 'category' => 'groceries', 'cost' => 1.10, 'price' => 1.60, 'stock' => 45, 'alert' => 8],
            'shampoo' => ['name' => 'Head & Shoulders Shampoo 170ml', 'barcode' => '4900000000019', 'category' => 'household', 'cost' => 2.20, 'price' => 3.25, 'stock' => 40, 'alert' => 6],
            'tissue' => ['name' => 'Kleenex Tissue Box', 'barcode' => '8990000000020', 'category' => 'household', 'cost' => 0.90, 'price' => 1.40, 'stock' => 55, 'alert' => 10],
        ];

        $products = [];
        foreach ($definitions as $key => $product) {
            $products[$key] = Product::updateOrCreate(
                ['shop_id' => $shopId, 'barcode' => $product['barcode']],
                [
                    'name' => $product['name'],
                    'category_id' => $categories[$product['category']]->id,
                    'cost_price' => $product['cost'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'alert_quantity' => $product['alert'],
                ]
            );
        }

        return $products;
    }

    /** @param array<string, Product> $products */
    private function seedOrders(int $shopId, int $cashierId, array $products): void
    {
        $orders = [
            ['receipt' => 'DEMO-001', 'days_ago' => 2, 'payment' => 'cash', 'items' => [['coca_cola', 3], ['pringles', 2], ['vital_water', 2]]],
            ['receipt' => 'DEMO-002', 'days_ago' => 1, 'payment' => 'aba', 'items' => [['anchor_beer', 4], ['oreo', 2], ['fish_sauce', 1]]],
            ['receipt' => 'DEMO-003', 'days_ago' => 0, 'payment' => 'cash', 'items' => [['carabao', 3], ['shampoo', 1], ['tissue', 2]]],
        ];

        foreach ($orders as $definition) {
            $subtotal = collect($definition['items'])->sum(
                fn (array $item): float => (float) $products[$item[0]]->price * $item[1]
            );
            $orderedAt = now()->subDays($definition['days_ago'])->setTime(10 + $definition['days_ago'], 30);

            $order = Order::updateOrCreate(
                ['shop_id' => $shopId, 'receipt_no' => $definition['receipt']],
                [
                    'cashier_id' => $cashierId,
                    'total' => $subtotal,
                    'status' => 'completed',
                    'payment_method' => $definition['payment'],
                    'created_at' => $orderedAt,
                    'updated_at' => $orderedAt,
                ]
            );

            $order->items()->delete();
            foreach ($definition['items'] as [$productKey, $quantity]) {
                $product = $products[$productKey];
                $item = new OrderItem([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'subtotal' => $product->price * $quantity,
                ]);
                $item->shop_id = $shopId;
                $item->created_at = $orderedAt;
                $item->updated_at = $orderedAt;
                $order->items()->save($item);
            }
        }
    }

    private function seedSettings(int $shopId): void
    {
        foreach ([
            'shop_name' => 'ហាងលក់ទំនិញទូទៅ (Demo Store)',
            'shop_phone' => '012 345 678',
            'shop_address' => 'ភ្នំពេញ',
            'currency_symbol' => '$',
            'tax_rate' => '0',
        ] as $key => $value) {
            Setting::updateOrCreate(['shop_id' => $shopId, 'key' => $key], ['value' => $value]);
        }
    }
}
