<?php

namespace App\Filament\Resources\SliderResource\Pages;

use App\Filament\Resources\SliderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateSlider extends CreateRecord
{
    protected static string $resource = SliderResource::class;

    // Handled by SpatieMediaLibraryFileUpload; no manual attach needed
}
