<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Get products with pagination.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::query()->with(['category:id,name,slug'])->where('is_active', true);

            $select = ['id', 'category_id', 'name', 'slug', 'description'];
            if (Schema::hasColumn('products', 'price')) {
                $select[] = 'price';
            }
            if (Schema::hasColumn('products', 'sale_price')) {
                $select[] = 'sale_price';
            }
            if (Schema::hasColumn('products', 'is_on_sale')) {
                $select[] = 'is_on_sale';
            }
            if (Schema::hasColumn('products', 'stock')) {
                $select[] = 'stock';
            }
            if (Schema::hasColumn('products', 'is_featured')) {
                $select[] = 'is_featured';
            }
            if (Schema::hasColumn('products', 'is_upcoming')) {
                $select[] = 'is_upcoming';
            }
            if (Schema::hasColumn('products', 'available_from')) {
                $select[] = 'available_from';
            }

            $products = $query->select($select)
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 12));
        } catch (\Exception $e) {
            Log::error('ProductController@index error: '.$e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Server error while fetching products.',
            ], 500);
        }

        // Add media URLs and calculated prices
        $products->getCollection()->transform(function ($product) {
            $product->image_url = $product->getFirstMediaUrl('image');
            $product->current_price = $product->getCurrentPrice();
            $product->savings = $product->getSavings();
            $product->discount_percentage = $product->getDiscountPercentage();
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Get specific product details by slug.
     */
    public function show($slug): JsonResponse
    {
        // Find product by slug (guarded in case the slug column is missing)
        $query = Product::query()->with(['category:id,name,slug'])->where('is_active', true);

        if (Schema::hasColumn('products', 'slug')) {
            $query->where('slug', $slug);
        } else {
            // If slug column doesn't exist, attempt to find by id if numeric
            if (is_numeric($slug)) {
                $query->where('id', (int) $slug);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Product lookup by slug is unavailable on this environment.',
                ], 400);
            }
        }

        $select = ['id', 'category_id', 'name', 'description', 'created_at', 'updated_at'];
        if (Schema::hasColumn('products', 'slug')) {
            $select[] = 'slug';
        }
        if (Schema::hasColumn('products', 'price')) {
            $select[] = 'price';
        }
        if (Schema::hasColumn('products', 'sale_price')) {
            $select[] = 'sale_price';
        }
        if (Schema::hasColumn('products', 'is_on_sale')) {
            $select[] = 'is_on_sale';
        }
        if (Schema::hasColumn('products', 'stock')) {
            $select[] = 'stock';
        }
        if (Schema::hasColumn('products', 'is_featured')) {
            $select[] = 'is_featured';
        }
        if (Schema::hasColumn('products', 'is_upcoming')) {
            $select[] = 'is_upcoming';
        }
        if (Schema::hasColumn('products', 'available_from')) {
            $select[] = 'available_from';
        }

        $product = $query->select($select)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        // Add media URLs (multiple images if available)
        $product->images = $product->getMedia('image')->map(function ($media) {
            return [
                'id' => $media->id,
                'name' => $media->name,
                'url' => $media->getUrl(),
                'size' => $media->size,
            ];
        });

        // Add main image URL for backward compatibility
        $product->image_url = $product->getFirstMediaUrl('image');
        
        // Add calculated prices
        $product->current_price = $product->getCurrentPrice();
        $product->savings = $product->getSavings();
        $product->discount_percentage = $product->getDiscountPercentage();

        return response()->json([
            'success' => true,
            'data' => $product,
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
            // Select all product columns to avoid referencing columns that may be missing
            ->select('products.*')
            ->orderBy('name')
            ->paginate($request->get('per_page', 12));

        // Add media URLs and calculated prices
        $products->getCollection()->transform(function ($product) {
            $product->image_url = $product->getFirstMediaUrl('image');
            $product->current_price = $product->getCurrentPrice();
            $product->savings = $product->getSavings();
            $product->discount_percentage = $product->getDiscountPercentage();
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
        try {
            $query = Product::query()->with(['category:id,name,slug'])->where('is_active', true);
            if (Schema::hasColumn('products', 'is_featured')) {
                $query->where('is_featured', true);
            }

            $select = ['id', 'category_id', 'name', 'slug', 'description'];
            if (Schema::hasColumn('products', 'price')) {
                $select[] = 'price';
            }
            if (Schema::hasColumn('products', 'sale_price')) {
                $select[] = 'sale_price';
            }
            if (Schema::hasColumn('products', 'is_on_sale')) {
                $select[] = 'is_on_sale';
            }
            if (Schema::hasColumn('products', 'stock')) {
                $select[] = 'stock';
            }
            if (Schema::hasColumn('products', 'is_featured')) {
                $select[] = 'is_featured';
            }
            if (Schema::hasColumn('products', 'is_upcoming')) {
                $select[] = 'is_upcoming';
            }
            if (Schema::hasColumn('products', 'available_from')) {
                $select[] = 'available_from';
            }

            $products = $query->select($select)
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 8));
        } catch (\Exception $e) {
            Log::error('ProductController@featured error: '.$e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Server error while fetching featured products.',
            ], 500);
        }

        // Add media URLs and calculated prices
        $products->getCollection()->transform(function ($product) {
            $product->image_url = $product->getFirstMediaUrl('image');
            $product->current_price = $product->getCurrentPrice();
            $product->savings = $product->getSavings();
            $product->discount_percentage = $product->getDiscountPercentage();
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
        try {
            $query = Product::query()->with(['category:id,name,slug'])->where('is_active', true);
            if (Schema::hasColumn('products', 'is_upcoming')) {
                $query->where('is_upcoming', true);
            }

            $select = ['id', 'category_id', 'name', 'slug', 'description'];
            if (Schema::hasColumn('products', 'price')) {
                $select[] = 'price';
            }
            if (Schema::hasColumn('products', 'sale_price')) {
                $select[] = 'sale_price';
            }
            if (Schema::hasColumn('products', 'is_on_sale')) {
                $select[] = 'is_on_sale';
            }
            if (Schema::hasColumn('products', 'stock')) {
                $select[] = 'stock';
            }
            if (Schema::hasColumn('products', 'is_featured')) {
                $select[] = 'is_featured';
            }
            if (Schema::hasColumn('products', 'is_upcoming')) {
                $select[] = 'is_upcoming';
            }
            if (Schema::hasColumn('products', 'available_from')) {
                $select[] = 'available_from';
            }

            $products = $query->select($select);

            // Only order by available_from if the column exists
            if (Schema::hasColumn('products', 'available_from')) {
                $products = $products->orderBy('available_from', 'asc');
            }

            $products = $products->paginate($request->get('per_page', 12));
        } catch (\Exception $e) {
            Log::error('ProductController@upcoming error: '.$e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Server error while fetching upcoming products.',
            ], 500);
        }

        // Add media URLs and calculated prices
        $products->getCollection()->transform(function ($product) {
            $product->image_url = $product->getFirstMediaUrl('image');
            $product->current_price = $product->getCurrentPrice();
            $product->savings = $product->getSavings();
            $product->discount_percentage = $product->getDiscountPercentage();
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Get products on sale.
     */
    public function onSale(Request $request): JsonResponse
    {
        $products = Product::query()
            ->with(['category:id,name,slug'])
            ->where('is_active', true)
            ->where('is_on_sale', true)
            ->whereNotNull('sale_price')
            ->select(['id', 'category_id', 'name', 'slug', 'description', 'price', 'sale_price', 'is_on_sale', 'stock', 'is_featured', 'is_upcoming', 'available_from'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 12));

        // Add media URLs and calculated prices
        $products->getCollection()->transform(function ($product) {
            $product->image_url = $product->getFirstMediaUrl('image');
            $product->current_price = $product->getCurrentPrice();
            $product->savings = $product->getSavings();
            $product->discount_percentage = $product->getDiscountPercentage();
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
}
