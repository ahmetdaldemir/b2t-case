<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $orders = Order::with(['user', 'products'])->get();
        } else {
            $orders = $user->orders()->with(['products'])->get();
        }

        return response()->json([
            'orders' => $orders,
        ]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'total_price' => 0,
            ]);

            $totalPrice = 0;

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock_quantity < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "Insufficient stock for product: {$product->name}",
                    ], 422);
                }

                $order->orderItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ]);

                $product->decrement('stock_quantity', $item['quantity']);

                $totalPrice += $product->price * $item['quantity'];
            }

            $order->update(['total_price' => $totalPrice]);

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load(['orderItems.product']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, Order $order)
    {
        $user = $request->user();

        if (!$user->isAdmin() && $order->user_id !== $user->id) {
            return response()->json([
                'message' => 'Access denied',
            ], 403);
        }

        return response()->json([
            'order' => $order->load(['user', 'orderItems.product']),
        ]);
    }
}