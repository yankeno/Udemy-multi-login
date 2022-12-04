<?php

namespace App\Services;

use InterventionImage;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    public static function upload($imageFile, $folderName)
    {
        $resizedImage = InterventionImage::make($imageFile)->resize(1920, 1080)->encode();
        $fileName = uniqid(rand() . '_');
        $extension = $imageFile->extension();
        $fileNameToStore = $fileName . '.' . $extension;
        Storage::put('public/' . $folderName . '/' . $fileNameToStore, $resizedImage);

        return $fileNameToStore;
    }
}
