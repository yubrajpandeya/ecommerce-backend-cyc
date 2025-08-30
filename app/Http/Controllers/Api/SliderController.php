<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;

class SliderController extends Controller
{
    /**
     * Get active sliders for frontend.
     */
    public function index(): JsonResponse
    {
        $sliders = Slider::query()
            ->where('is_active', true)
            ->select(['id', 'title', 'link_url', 'position'])
            ->orderBy('position')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($slider) {
                $slider->image_url = $slider->getFirstMediaUrl('image');
                return $slider;
            });

        return response()->json([
            'success' => true,
            'data' => $sliders,
        ]);
    }
}
