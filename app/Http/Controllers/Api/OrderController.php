<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Get authenticated user's orders.
     */
    public function index(Request $request): JsonResponse
    {
        $orders = auth()->user()->orders()
            ->with(['product.category:id,name,slug'])
            ->select(['id', 'order_number', 'product_id', 'quantity', 'unit_price', 'total_amount', 'status', 'created_at', 'payment_verified_at'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        // Add product and payment screenshot details
        $orders->getCollection()->transform(function ($order) {
            $order->product_name = $order->product->name;
            $order->product_slug = $order->product->slug;
            $order->product_image = $order->product->getFirstMediaUrl('image');
            $order->category_name = $order->product->category->name;
            $order->payment_screenshot_url = $order->getFirstMediaUrl('payment_screenshot');
            unset($order->product);
            return $order;
        });

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * Create a new order.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string|max:500',
            'phone_number' => 'required|string|max:10',
            'notes' => 'nullable|string|max:500',
            'payment_screenshot' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = Product::findOrFail($request->product_id);

        // Check if product is active and has sufficient stock
        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Product is not available.',
            ], 400);
        }

        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Available: ' . $product->stock,
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Create the order
            $order = Order::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'order_number' => Order::generateOrderNumber(),
                'quantity' => $request->quantity,
                'unit_price' => $product->price,
                'total_amount' => $product->price * $request->quantity,
                'status' => 'payment_verification',
                'shipping_address' => $request->shipping_address,
                'phone_number' => $request->phone_number,
                'notes' => $request->notes,
            ]);

            // Handle payment screenshot upload
            if ($request->hasFile('payment_screenshot')) {
                $order->addMediaFromRequest('payment_screenshot')
                    ->toMediaCollection('payment_screenshot');
            }

            // Reduce product stock
            $product->decrement('stock', $request->quantity);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully. Please wait for payment verification.',
                'data' => [
                    'order' => [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'total_amount' => $order->total_amount,
                        'status' => $order->status,
                        'payment_screenshot_url' => $order->getFirstMediaUrl('payment_screenshot'),
                    ],
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order. Please try again.',
            ], 500);
        }
    }

    /**
     * Get specific order details.
     */
    public function show($id): JsonResponse
    {
        $order = auth()->user()->orders()
            ->with(['product.category:id,name,slug', 'verifier:id,name,email'])
            ->findOrFail($id);

        // Add additional details
        $order->product_name = $order->product->name;
        $order->product_slug = $order->product->slug;
        $order->product_image = $order->product->getFirstMediaUrl('image');
        $order->category_name = $order->product->category->name;
        $order->payment_screenshot_url = $order->getFirstMediaUrl('payment_screenshot');

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    /**
     * Cancel an order (only if status is pending or payment_verification).
     */
    public function cancel($id): JsonResponse
    {
        $order = auth()->user()->orders()->findOrFail($id);

        if (!in_array($order->status, ['pending', 'payment_verification'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel order with current status.',
            ], 400);
        }

        // Restore product stock
        $order->product->increment('stock', $order->quantity);

        $order->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully.',
        ]);
    }
}
