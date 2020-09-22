<?php

namespace MonstreX\VoyagerExtension\Generators;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

use Str;

class MediaLibraryPathGenerator implements  PathGenerator
{
    public function getPath(Media $media) : string
    {
        // Change default media path to path like '<model>/media/<id>/image.jpg'
        $parts = explode('\\', $media->model_type);
        return Str::plural(mb_strtolower(end($parts))).'/media/'.$media->id.'/';
    }

    public function getPathForConversions(Media $media) : string
    {
        return $this->getPath($media).'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media).'responsives/';
    }
}