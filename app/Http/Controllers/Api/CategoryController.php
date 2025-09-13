<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Get active categories for frontend.
     */
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->select(['id', 'name', 'slug', 'description'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Get products for a specific category.
     */
    public function products($identifier, Request $request): JsonResponse
    {
        // Try to find category by ID first, then by slug
        $category = Category::query()
            ->where('is_active', true)
            ->where(function ($query) use ($identifier) {
                $query->where('id', $identifier)
                      ->orWhere('slug', $identifier);
            })
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
        }

        // Build select list defensively: only include columns that exist in DB.
        $select = ['id', 'category_id', 'name', 'slug'];

        if (\Illuminate\Support\Facades\Schema::hasColumn('products', 'description')) {
            $select[] = 'description';
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('products', 'price')) {
            $select[] = 'price';
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('products', 'stock')) {
            $select[] = 'stock';
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('products', 'is_featured')) {
            $select[] = 'is_featured';
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('products', 'is_upcoming')) {
            $select[] = 'is_upcoming';
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('products', 'available_from')) {
            $select[] = 'available_from';
        }

        $products = $category->products()
            ->where('is_active', true)
            ->select($select)
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
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
            ],
        ]);
    }
}
