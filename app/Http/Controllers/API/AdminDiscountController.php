<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\DiscountResource;
use App\Models\Discount;
use Illuminate\Http\Request;

class AdminDiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $discounts = Discount::latest()->paginate(10);
        return ApiResponse::success(DiscountResource::collection($discounts), 'Show all discount');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'discount_code'     => 'required|unique:discounts',
            'discount_name'     => 'required',
            'discount_amount'   => 'required',
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date',
        ]);

        $discount = Discount::create([
            'discount_code'     => $validated['discount_code'],
            'discount_name'     => $validated['discount_name'],
            'discount_amount'   => $validated['discount_amount'],
            'start_date'        => $validated['start_date'],
            'end_date'          => $validated['end_date'],
            'is_active'         => true,
        ]);

        return ApiResponse::success(new DiscountResource($discount), 'Discount created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $discount = Discount::find($id);
        if (!$discount) {
            return ApiResponse::error('Data not found.', 404);
        }

        $validated = $request->validate([
            'discount_code'     => 'required|unique:discounts,discount_code,' . $discount->id,
            'discount_name'     => 'required',
            'discount_amount'   => 'required|numeric|min:0',
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date',
        ]);

        $discount->discount_code    = $validated['discount_code'];
        $discount->discount_name    = $validated['discount_name'];
        $discount->discount_amount  = $validated['discount_amount'];
        $discount->start_date       = $validated['start_date'];
        $discount->end_date         = $validated['end_date'];

        $discount->save();

        return ApiResponse::success(new DiscountResource($discount), 'Discount update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $discount = Discount::find($id);
        if (!$discount) {
            return ApiResponse::error('Data not found.', 404);
        }
        $discount->delete();

        return ApiResponse::success(new DiscountResource($discount), 'Discount delete succesfully.');
    }

    /**
     * Update status discount
     */
    public function toggleStatus($id)
    {
        $discount = Discount::findOrFail($id);
        $oldStatus = $discount->is_active;

        $discount->is_active = !$discount->is_active;
        $discount->save();

        return ApiResponse::success(new DiscountResource($discount), 'Discount status updated successfully.');
    }
}