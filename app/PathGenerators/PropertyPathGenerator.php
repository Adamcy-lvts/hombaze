<?php

namespace App\PathGenerators;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class PropertyPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        $modelType = class_basename($media->model_type);
        $modelId = $media->model_id;
        $collection = $media->collection_name;

        return "{$modelType}/{$collection}/{$modelId}/";
    }

    public function getPathForConversions(Media $media): string
    {
        $modelType = class_basename($media->model_type);
        $modelId = $media->model_id;
        $collection = $media->collection_name;

        return "{$modelType}/{$collection}/{$modelId}/conversions/";
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        $modelType = class_basename($media->model_type);
        $modelId = $media->model_id;
        $collection = $media->collection_name;

        return "{$modelType}/{$collection}/{$modelId}/responsive/";
    }
}