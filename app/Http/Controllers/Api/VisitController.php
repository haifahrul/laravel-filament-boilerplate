<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VisitController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $limit  = (int) $request->input('limit', 10);
        $visits = Visit::with('customer')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate($limit);

        $result = self::paginate($visits, function ($order) {
            return $order;
        });

        return $this->success($result);
    }

    public function checkIn(Request $request)
    {
        $user = $request->user();

        $ongoingVisit = Visit::where('user_id', $user->id)
            ->whereNull('checked_out_at')
            ->first();

        if ($ongoingVisit) {
            return response()->json([
                'success' => false,
                'message' => 'Harap checkout dari kunjungan sebelumnya terlebih dahulu.',
            ], 422);
        }

        $validated = $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'activity_type' => 'required|string|in:visit,presentation,follow-up,offering',
            'note'          => 'nullable|string',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
            'photo'         => 'nullable|image|max:2048', // hanya file image
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('visits', 'public');
        }

        $visit = Visit::create([
            'user_id'       => $user->id,
            'customer_id'   => $validated['customer_id'],
            'activity_type' => $validated['activity_type'],
            'note'          => $validated['note'] ?? null,
            'latitude'      => $validated['latitude'],
            'longitude'     => $validated['longitude'],
            'photo_path'    => $photoPath,
            'checked_in_at' => now(),
        ]);

        return $this->success(null, 'Check-in berhasil.');
    }

    public function checkOut(Request $request)
    {
        $user = $request->user();

        $visit = Visit::where('user_id', $user->id)
            ->whereNull('checked_out_at')
            ->latest()
            ->first();

        if (!$visit) {
            return $this->error('Tidak ada kunjungan aktif yang perlu di-checkout.', 404);
        }

        $visit->update([
            'checked_out_at' => now(),
        ]);

        return $this->success(null, 'Checkout berhasil.');
    }
}
