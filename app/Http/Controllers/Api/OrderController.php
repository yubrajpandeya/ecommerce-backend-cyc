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
            ->select([
                'id', 'order_number', 'product_id', 'quantity', 'unit_price', 'original_price', 
                'discount_amount', 'was_on_sale', 'total_amount', 'status', 'created_at', 
                'payment_verified_at', 'shipping_address', 'phone_number', 'payment_method', 
                'full_name', 'email', 'city', 'postal_code', 'notes'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        // Add product and payment screenshot details
        $orders->getCollection()->transform(function ($order) {
            $order->product_name = $order->product->name;
            $order->product_slug = $order->product->slug;
            $order->product_image = $order->product->getFirstMediaUrl('image');
            $order->category_name = $order->product->category->name;
            $order->payment_screenshot_url = $order->getFirstMediaUrl('payment_screenshot');
            
            // Add discount information for display
            $order->savings_per_unit = $order->was_on_sale ? $order->discount_amount : 0;
            $order->total_savings = $order->was_on_sale ? ($order->discount_amount * $order->quantity) : 0;
            
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
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'payment_method' => 'required|string|in:cod,qr_payment',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'notes' => 'nullable|string|max:1000',
            'payment_screenshot' => 'required_if:payment_method,qr_payment|file|mimes:jpeg,png,jpg,gif|max:2048',
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
            // Determine initial status based on payment method
            $initialStatus = $request->payment_method === 'cod' ? 'confirmed' : 'payment_verification';
            
            // Get the current price (sale price if on sale, regular price otherwise)
            $currentPrice = $product->getCurrentPrice();
            $originalPrice = $product->price;
            $discountAmount = $product->getSavings();
            $wasOnSale = $product->is_on_sale && $product->sale_price;
            
            // Create the order
            $order = Order::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'order_number' => Order::generateOrderNumber(),
                'quantity' => $request->quantity,
                'unit_price' => $currentPrice,
                'original_price' => $originalPrice,
                'discount_amount' => $discountAmount,
                'was_on_sale' => $wasOnSale,
                'total_amount' => $currentPrice * $request->quantity,
                'status' => $initialStatus,
                'shipping_address' => $request->shipping_address,
                'phone_number' => $request->phone_number,
                'notes' => $request->notes,
                'payment_method' => $request->payment_method,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
            ]);

            // Handle payment screenshot upload (only for QR payments)
            if ($request->payment_method === 'qr_payment' && $request->hasFile('payment_screenshot')) {
                $order->addMediaFromRequest('payment_screenshot')
                    ->toMediaCollection('payment_screenshot');
            }

            // For COD orders, mark as payment verified
            if ($request->payment_method === 'cod') {
                $order->update([
                    'payment_verified_at' => now(),
                    'verified_by' => auth()->id(), // System verification for COD
                ]);
            }

            // Reduce product stock
            $product->decrement('stock', $request->quantity);

            DB::commit();

            $message = $request->payment_method === 'cod' 
                ? 'Order created successfully with Cash on Delivery.'
                : 'Order created successfully. Please wait for payment verification.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'order' => [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'unit_price' => $order->unit_price,
                        'original_price' => $order->original_price,
                        'discount_amount' => $order->discount_amount,
                        'was_on_sale' => $order->was_on_sale,
                        'total_amount' => $order->total_amount,
                        'status' => $order->status,
                        'payment_method' => $order->payment_method,
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
