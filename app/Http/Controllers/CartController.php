<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Http\Resources\CartItemResource;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = auth()->user()->cartItems;

        return response()->json(['data' => CartItemResource::collection($cartItems)], 200);
    }

    public function store(Request $request)
    {
        $validator = $this->requestValidator($request->all());

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }


        // Assume the request includes an array of products
        $products = $request->input('products');

        foreach ($products as $product) {
            $cartItem = new CartItem([
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
                'total_price' => $product['total_price'],
                'user_id' =>  Auth::id(),
            ]);

            $cartItem->save(); // Use save on the model, not on the array
        }


        return response()->json(['message' => 'Cart items added successfully'], 201);
    }

    public function update(Request $request, $id)
    {
        $cartItem = CartItem::findOrFail($id);

        $validator = $this->requestValidator($request->all());

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $cartItem->update([
            'quantity' => $request->input('quantity'),
            'total_price' => $request->input('total_price'),
        ]);

        return response()->json(['data' => new CartItemResource($cartItem)], 200);
    }

    public function destroy($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Cart item deleted successfully'], 200);
    }

    private function requestValidator($requestData)
    {
        $rules = [
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.total_price' => 'required|numeric|min:0',
        ];

        $attributes = [
            'products.*.product_id' => 'Product ID',
            'products.*.quantity' => 'Quantity',
            'products.*.total_price' => 'Total Price',
        ];

        $messages = [];

        return Validator::make($requestData, $rules, $messages)->setAttributeNames($attributes);
    }



        public function calculateTotal($userId)
        {
            // Get user's total purchase amount (replace with your logic)
            $totalAmount = CartItem::where('user_id', $userId)->sum('amount');

            // Check if user is eligible for a coupon
            if ($totalAmount > 500) {
                // Get applicable coupon
                $coupon = Coupon::where('code', 'DISCOUNT10')->first();

                if ($coupon) {
                    // Calculate discount
                    $discount = min($totalAmount * ($coupon->discount_percentage / 100), $coupon->max_discount);

                    // Apply discount to total amount
                    $totalAmount -= $discount;
                }
            }

            return $totalAmount;
        }
}
