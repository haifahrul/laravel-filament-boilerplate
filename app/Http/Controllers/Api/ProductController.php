<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        try {
            $limit = (int) $request->input('limit', 10);

            $query = Product::query()
                ->when($request->search, function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                })
                ->where('status', Product::STATUS_ACTIVE)
                ->orderBy('name');

            $paginator = $query->paginate($limit);

            $result = $this->paginate($paginator, function ($product) {
                return [
                    'id'          => $product->id,
                    'name'        => $product->name,
                    'sku'         => $product->sku,
                    'description' => $product->description,
                    'price'       => $product->price,
                    'image_path'  => $product->image_path,
                ];
            });

            return $this->success($result);
        } catch (\Throwable $e) {
            return $this->error('Gagal mengambil data produk.', 500, $e->getMessage());
        }
    }
}
