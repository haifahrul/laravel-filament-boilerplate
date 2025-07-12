<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerController extends Controller
{
    use ApiResponse, ValidatesRequests;

    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $limit = (int) $request->input('limit', 10);
            $query = Customer::query()
                ->where('user_id', $user->id)
                ->when(
                    $request->search,
                    fn ($q) =>
                    $q->where('name', 'like', '%' . $request->search . '%')
                )
                ->with(['latestVisit' => fn ($q) => $q->latest('checked_in_at')]);

            $paginator = $query->paginate($limit);

            $result = self::paginate($paginator, function ($customer) {
                return [
                    'id'            => $customer->id,
                    'name'          => $customer->name,
                    'address'       => $customer->address,
                    'contact'       => $customer->contact,
                    'business_type' => $customer->business_type,
                    'latitude'      => $customer->latitude,
                    'longitude'     => $customer->longitude,
                    'last_visit'    => optional($customer->latestVisit)->checked_in_at,
                ];
            });

            return $this->success($result);
        } catch (\Throwable $th) {
            return $this->success('Internal Server Error', $th->getCode());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'          => 'required|string|max:255',
                'address'       => 'required|string',
                'contact'       => 'nullable|string|max:20',
                'business_type' => 'nullable|string|max:50',
                'latitude'      => 'required|numeric',
                'longitude'     => 'required|numeric',
            ]);

            $customer = Customer::create([
                ...$validated,
                'user_id' => $request->user()->id,
            ]);

            return $this->success([
                'id' => $customer->id,
            ], 'Customer created successfully.');
        } catch (\Throwable $th) {
            return $this->success('Internal Server Error', $th->getCode());
        }
    }

    public function show($id)
    {
        try {
            $customer = Customer::with([
                'latestVisit' => fn ($q) => $q->latest('checked_in_at'),
            ])
                ->where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            return $this->success([
                'id'            => $customer->id,
                'name'          => $customer->name,
                'address'       => $customer->address,
                'contact'       => $customer->contact,
                'business_type' => $customer->business_type,
                'latitude'      => $customer->latitude,
                'longitude'     => $customer->longitude,
                'last_visit'    => optional($customer->latestVisit)->checked_in_at,
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->error('Data tidak ditemukan', 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $customer = Customer::findOrFail($id);

            $validated = $request->validate([
                'name'          => 'required|string|max:255',
                'address'       => 'nullable|string',
                'contact'       => 'nullable|string|max:20',
                'business_type' => 'nullable|string|max:50',
                'latitude'      => 'required|numeric',
                'longitude'     => 'required|numeric',
            ]);

            $customer->update($validated);

            return $this->success(null, 'Customer updated successfully.');
        } catch (\Throwable $th) {
            return $this->success('Internal Server Error', $th->getCode());
        }
    }

    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();

            return $this->success(null, 'Customer deleted (soft) successfully.');
        } catch (ModelNotFoundException $e) {
            return $this->error('Data tidak ditemukan', 404);
        }
    }

    public function nearby(Request $request)
    {
        $this->validate($request, [
            'lat'    => 'required|numeric',
            'lng'    => 'required|numeric',
            'radius' => 'nullable|numeric',
        ]);

        $user   = $request->user();
        $lat    = $request->input('lat');
        $lng    = $request->input('lng');
        $radius = $request->input('radius', 5); // default 5km

        $customers = Customer::selectRaw("
            id, name, address, latitude, longitude,
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?))
            + sin(radians(?)) * sin(radians(latitude)))) AS distance
        ", [$lat, $lng, $lat])
            ->where('user_id', $user->id)
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->limit(20)
            ->get();

        $result = $customers->map(function ($customer) {
            return [
                'id'          => $customer->id,
                'name'        => $customer->name,
                'address'     => $customer->address,
                'latitude'    => $customer->latitude,
                'longitude'   => $customer->longitude,
                'distance_km' => round($customer->distance, 2),
            ];
        });

        return $this->success($result);
    }

}
