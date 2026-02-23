<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ImageService
{
    protected $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Upload dan compress image
     * 
     * @param UploadedFile $file
     * @param string $folder
     * @param int $maxWidth
     * @param int $quality
     * @return string
     */
    public function uploadAndCompress(UploadedFile $file, string $folder = 'images', int $maxWidth = 800, int $quality = 75): string
    {
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.jpg'; // Always save as jpg for smaller size
        $path = $folder . '/' . $filename;

        // Read and process image
        $image = $this->manager->read($file->getRealPath());

        // Resize if width is larger than maxWidth
        if ($image->width() > $maxWidth) {
            $image->scale(width: $maxWidth);
        } else {
            // Even if smaller, resize to maxWidth for consistency
            $image->scale(width: $maxWidth);
        }

        // Encode with compression (always to JPEG for smaller file size)
        $encoded = $image->toJpeg($quality);

        // Save to storage (explicit mimetype to avoid finfo PHP extension dependency)
        Storage::disk('public')->put($path, $encoded, ['mimetype' => 'image/jpeg']);

        return $path;
    }

    /**
     * Delete image from storage
     * 
     * @param string|null $path
     * @return bool
     */
    public function delete(?string $path): bool
    {
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    /**
     * Get full URL of image
     * 
     * @param string|null $path
     * @return string|null
     */
    public function getUrl(?string $path): ?string
    {
        if ($path) {
            return Storage::disk('public')->url($path);
        }

        return null;
    }
}
