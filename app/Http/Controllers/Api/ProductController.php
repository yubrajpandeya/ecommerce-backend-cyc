<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Get products with pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $products = Product::query()
            ->with(['category:id,name,slug'])
            ->where('is_active', true)
            ->select(['id', 'category_id', 'name', 'slug', 'description', 'price', 'stock', 'is_featured', 'is_upcoming', 'available_from'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 12));

        // Add media URLs
        $products->getCollection()->transform(function ($product) {
            $product->image_url = $product->getFirstMediaUrl('image');
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Search products by name or description.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $categoryId = $request->get('category_id');

        $products = Product::query()
            ->with(['category:id,name,slug'])
            ->where('is_active', true)
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                });
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->select(['id', 'category_id', 'name', 'slug', 'description', 'price', 'stock', 'is_featured', 'is_upcoming', 'available_from'])
            ->orderBy('name')
            ->paginate($request->get('per_page', 12));

        // Add media URLs
        $products->getCollection()->transform(function ($product) {
            $product->image_url = $product->getFirstMediaUrl('image');
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Get featured products.
     */
    public function featured(Request $request): JsonResponse
    {
        $products = Product::query()
            ->with(['category:id,name,slug'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->select(['id', 'category_id', 'name', 'slug', 'description', 'price', 'stock', 'is_featured', 'is_upcoming', 'available_from'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 8));

        // Add media URLs
        $products->getCollection()->transform(function ($product) {
            $product->image_url = $product->getFirstMediaUrl('image');
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Get upcoming products.
     */
    public function upcoming(Request $request): JsonResponse
    {
        $products = Product::query()
            ->with(['category:id,name,slug'])
            ->where('is_active', true)
            ->where('is_upcoming', true)
            ->select(['id', 'category_id', 'name', 'slug', 'description', 'price', 'stock', 'is_featured', 'is_upcoming', 'available_from'])
            ->orderBy('available_from', 'asc')
            ->paginate($request->get('per_page', 12));

        // Add media URLs
        $products->getCollection()->transform(function ($product) {
            $product->image_url = $product->getFirstMediaUrl('image');
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
}
