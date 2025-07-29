<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    public function index()
    {
        $products = Cache::remember('products', 3600, function () {
            return Product::all();
        });

        return response()->json([
            'products' => $products,
        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku|max:255',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = Product::create($request->all());

        Cache::forget('products');

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product,
        ], 201);
    }


    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'sku' => 'sometimes|required|string|unique:products,sku,' . $product->id . '|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'stock_quantity' => 'sometimes|required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $product->update($request->all());

        Cache::forget('products');

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
        ]);
    }


    public function destroy(Product $product)
    {
        $product->delete();

        Cache::forget('products');

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }
}