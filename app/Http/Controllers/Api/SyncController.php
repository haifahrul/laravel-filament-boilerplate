<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Visit;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SyncController extends Controller
{
    use ApiResponse;

    public function sync(Request $request)
    {
        $user    = $request->user();
        $results = [
            'visits'    => [],
            'orders'    => [],
            'customers' => [],
        ];

        DB::beginTransaction();

        try {
            // Sinkronisasi customers
            foreach ($request->customers ?? [] as $item) {
                $customer = Customer::create([
                    'user_id'       => $user->id,
                    'name'          => $item['name'],
                    'address'       => $item['address'],
                    'latitude'      => $item['latitude'],
                    'longitude'     => $item['longitude'],
                    'contact'       => $item['contact'] ?? null,
                    'business_type' => $item['business_type'] ?? null,
                ]);

                $results['customers'][] = [
                    'local_id' => $item['local_id'],
                    'id'       => $customer->id,
                ];
            }

            // Sinkronisasi visits
            foreach ($request->visits ?? [] as $item) {
                $visit = Visit::create([
                    'user_id'        => $user->id,
                    'customer_id'    => $item['customer_id'],
                    'activity_type'  => $item['activity_type'],
                    'note'           => $item['note'] ?? null,
                    'latitude'       => $item['latitude'],
                    'longitude'      => $item['longitude'],
                    'checked_in_at'  => $item['checked_in_at'],
                    'checked_out_at' => $item['checked_out_at'] ?? null,
                ]);

                $results['visits'][] = [
                    'local_id' => $item['local_id'],
                    'id'       => $visit->id,
                ];
            }

            // Sinkronisasi orders
            foreach ($request->orders ?? [] as $item) {
                $order = Order::create([
                    'user_id'     => $user->id,
                    'customer_id' => $item['customer_id'],
                    'order_date'  => $item['order_date'],
                    'notes'       => $item['notes'] ?? null,
                ]);

                $subTotal = 0;

                foreach ($item['items'] ?? [] as $orderItem) {
                    $product = Product::find($orderItem['product_id']);
                    if (!$product)
                        continue;

                    $lineTotal = $product->price * $orderItem['quantity'];
                    $subTotal += $lineTotal;

                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity'   => $orderItem['quantity'],
                        'price'      => $product->price,
                        'sub_total'  => $lineTotal,
                    ]);
                }

                $order->update([
                    'total_amount' => $subTotal,
                ]);

                $results['orders'][] = [
                    'local_id' => $item['local_id'],
                    'id'       => $order->id,
                ];
            }

            DB::commit();

            return $this->success($results, 'Sync berhasil');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error('Sync gagal', 500, $e->getMessage());
        }
    }
}
