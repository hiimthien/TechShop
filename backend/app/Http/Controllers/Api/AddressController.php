<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Models\Address;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{

    public function index(): JsonResponse
    {
        $addresses = Address::where('user_id', Auth::id())->get();

        return response()->json([
            'status' => true,
            'data' => $addresses
        ]);
    }

    public function store(StoreAddressRequest $request): JsonResponse
    {
        $address = Address::create([
            'user_id'     => Auth::id(),
            'name'        => $request->name,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'city'        => $request->city,
            'ward_code'   => $request->ward_code,
            'district_id' => $request->district_id,
            'province_id' => $request->province_id,
            'is_default'  => $request->is_default ?? false,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Address created successfully',
        ]);
    }


    public function update(StoreAddressRequest $request, string $id): JsonResponse
    {
        $address = Address::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $address->update($request->only('name', 'phone', 'address', 'city', 'ward_code', 'district_id', 'province_id', 'is_default'));

        return response()->json([
            'status' => true,
            'message' => 'Address updated successfully',
        ]);
    }


    public function destroy(Address $address): JsonResponse
    {
        if ($address->user_id !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền.'], 403);
        }

        $address->delete();

        return response()->json([
            'status' => true,
            'message' => 'Address is deleted'
        ]);
    }
}
