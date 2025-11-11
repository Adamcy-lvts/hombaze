<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OptimalImageResolution implements ValidationRule
{
    protected $recommendedOnly;
    protected $propertyTypeSlug;

    public function __construct($recommendedOnly = false, $propertyTypeSlug = null)
    {
        $this->recommendedOnly = $recommendedOnly;
        $this->propertyTypeSlug = $propertyTypeSlug;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Handle required validation for featured image
        if (empty($value) && !$this->recommendedOnly) {
            $fail('🖼️ A featured image is required to showcase your property effectively.');
            return;
        }

        // Skip validation for empty values (for gallery images)
        if (empty($value)) {
            return;
        }

        // For SpatieMediaLibraryFileUpload, validation happens at form submission
        // The $value might be a temporary file path, UploadedFile, or Media model
        $filesToCheck = [];

        try {
            // Handle different value types
            if ($value instanceof UploadedFile) {
                $filesToCheck[] = ['path' => $value->getRealPath(), 'name' => $value->getClientOriginalName()];
            } elseif (is_string($value) && file_exists($value)) {
                $filesToCheck[] = ['path' => $value, 'name' => basename($value)];
            } elseif (is_array($value)) {
                // Handle multiple files
                foreach ($value as $file) {
                    if ($file instanceof UploadedFile) {
                        $filesToCheck[] = ['path' => $file->getRealPath(), 'name' => $file->getClientOriginalName()];
                    } elseif (is_string($file) && file_exists($file)) {
                        $filesToCheck[] = ['path' => $file, 'name' => basename($file)];
                    }
                }
            }

            foreach ($filesToCheck as $index => $fileInfo) {
                $filePath = $fileInfo['path'];
                $fileName = $fileInfo['name'];

                if (!file_exists($filePath)) {
                    continue;
                }

                // Get image information
                $imageInfo = @getimagesize($filePath);
                if (!$imageInfo) {
                    $fail("❌ The uploaded file '{$fileName}' is not a valid image or appears to be corrupted.");
                    return;
                }

                $width = $imageInfo[0];
                $height = $imageInfo[1];
                $config = getOptimalImageResolution();

                // Validate minimum resolution
                if ($width < $config['min_width'] || $height < $config['min_height']) {
                    $fileIndicator = count($filesToCheck) > 1 ? " (File: {$fileName})" : "";
                    $fail("📐 Image resolution is too low{$fileIndicator}. Minimum required: {$config['min_width']}×{$config['min_height']} pixels. Your image: {$width}×{$height} pixels. Please use a higher resolution image for better quality.");
                    return;
                }

                // Log successful validation
                Log::info('Image validation passed', [
                    'attribute' => $attribute,
                    'file_name' => $fileName,
                    'width' => $width,
                    'height' => $height,
                    'aspect_ratio' => round($width / $height, 2),
                ]);
            }
        } catch (\Exception $e) {
            // Log error but don't fail validation to prevent blocking legitimate uploads
            Log::warning('Image validation error', [
                'attribute' => $attribute,
                'error' => $e->getMessage(),
                'value_type' => gettype($value),
            ]);
        }
    }

    protected function isImage(UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType();
        return in_array($mimeType, [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/jpg'
        ]);
    }
}