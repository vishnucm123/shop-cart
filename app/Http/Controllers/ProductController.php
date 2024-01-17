<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductTransformer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return response()->json(['data' => ProductResource::collection($products)], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    public function requestValidator($requestData)
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'quantity'=>'required',
            'manufacture_date' => 'nullable|date|date_format:Y-m-d',
            'expiry_date' => 'nullable|date|date_format:Y-m-d',

        ];

        $attributes = [
            'name' => 'Product Name',
        ];

        $messages = [];

        return Validator::make($requestData, $rules, $messages)->setAttributeNames($attributes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = $this->requestValidator($request->all());

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $product = new Product();

        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->quantity = $request->input('quantity');
        $product->manufacture_date = $request->input('manufacture_date');
        $product->expiry_date = $request->input('expiry_date'); // Corrected line
        $product->save();


        return response()->json(['data' => new ProductResource($product)], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json(['data' => new ProductResource($product)], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $validator = $this->requestValidator($request->all());

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $product->update($request->all());

        return response()->json(['data' => new ProductResource($product)], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}


