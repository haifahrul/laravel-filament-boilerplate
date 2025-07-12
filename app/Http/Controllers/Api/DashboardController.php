<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\Order;
use App\Traits\ApiResponse;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        try {
            $user  = $request->user();
            $today = Carbon::today();

            // Visits
            $visits = Visit::whereDate('created_at', $today)
                ->where('user_id', $user->id);

            $totalVisits     = (clone $visits)->count();
            $pendingCheckout = (clone $visits)->whereNull('checked_out_at')->count();
            $completedVisits = (clone $visits)->whereNotNull('checked_out_at')->count();

            // Orders
            $orders = Order::whereDate('created_at', $today)
                ->where('user_id', $user->id);

            $totalOrders = (clone $orders)->count();
            $totalAmount = (clone $orders)->sum('total_amount');

            $result = [
                'visits' => [
                    'total'            => $totalVisits,
                    'pending_checkout' => $pendingCheckout,
                    'completed'        => $completedVisits,
                ],
                'orders' => [
                    'total'        => $totalOrders,
                    'total_amount' => $totalAmount,
                ],
            ];

            return $this->success($result, 'Berhasil mengambil data harian');
        } catch (\Throwable $e) {
            return $this->error('Gagal mengambil data ringkasan', 500);
        }
    }
}
