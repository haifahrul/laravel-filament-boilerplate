<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $limit  = (int) $request->input('limit', 10);
        $search = $request->input('search');
        $from   = $request->input('from');
        $to     = $request->input('to');

        $query = Order::with(['customer', 'items.product'])
            ->where('user_id', auth()->id());

        // 🔍 Search by customer name or order number
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', fn ($sub) =>
                    $sub->where('name', 'like', "%{$search}%"))
                    ->orWhere('order_number', 'like', "%{$search}%");
            });
        }

        // 📅 Filter date range
        if ($from) {
            $query->whereDate('order_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('order_date', '<=', $to);
        }

        $orders = $query->latest()->paginate($limit);

        $result = self::paginate($orders, function ($order) {
            return [
                'id'           => $order->id,
                'order_number' => $order->order_number,
                'order_date'   => $order->order_date->toDateString(),
                'total_amount' => $order->total_amount,
            ];
        });

        return $this->success($result);
    }

    public function show($id)
    {
        $order = Order::with(['items.product'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$order) {
            return $this->error('Order tidak ditemukan', 404);
        }

        return $this->success([
            'id'           => $order->id,
            'order_number' => $order->order_number,
            'order_date'   => $order->order_date->toDateString(),
            'notes'        => $order->notes,
            'total_amount' => $order->total_amount,
            'items'        => $order->items->map(function ($item) {
                return [
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product->name ?? 'Produk tidak tersedia',
                    'price'        => $item->price,
                    'quantity'     => $item->quantity,
                    'subtotal'     => $item->subtotal,
                ];
            })->toArray(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'        => ['required', 'exists:customers,id'],
            'order_date'         => ['required', 'date'],
            'notes'              => ['nullable', 'string'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $userId = auth()->id(); // dari token

        DB::beginTransaction();

        try {
            $orderNumber = Order::generateOrderNumber();
            $totalAmount = 0;
            $itemsData   = [];

            foreach ($data['items'] as $item) {
                $product     = Product::findOrFail($item['product_id']);
                $subTotal    = $product->price * $item['quantity'];
                $totalAmount += $subTotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $product->price,
                    'subtotal'   => $subTotal,
                ];
            }

            $order = Order::create([
                'user_id'      => $userId,
                'customer_id'  => $data['customer_id'],
                'order_number' => $orderNumber,
                'order_date'   => $data['order_date'],
                'notes'        => $data['notes'] ?? null,
                'total_amount' => $totalAmount,
            ]);

            foreach ($itemsData as $item) {
                $order->items()->create($item);
            }

            DB::commit();
            return $this->success($order, 'Order berhasil dibuat.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error('Gagal membuat order', 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'order_date'         => 'required|date',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'notes'              => 'nullable|string',
        ]);

        $order = Order::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        // Hapus item lama
        $order->items()->delete();

        $totalAmount = 0;

        foreach ($validated['items'] as $item) {
            $product     = Product::findOrFail($item['product_id']);
            $subTotal    = $product->price * $item['quantity'];
            $totalAmount += $subTotal;

            $order->items()->create([
                'product_id' => $product->id,
                'quantity'   => $item['quantity'],
                'price'      => $product->price,
                'subtotal'   => $subTotal,
            ]);
        }

        $order->update([
            'order_date'   => $validated['order_date'],
            'notes'        => $validated['notes'] ?? '',
            'total_amount' => $totalAmount, // kalau belum pakai pajak/diskon
        ]);

        return $this->success(null, 'Order updated');
    }

    public function destroy($id)
    {
        $order = Order::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        $order->delete(); // ini sekarang hanya soft delete

        return $this->success(null, 'Order berhasil dibatalkan');
    }
}
