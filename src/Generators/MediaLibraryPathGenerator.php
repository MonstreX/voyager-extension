<?php

namespace MonstreX\VoyagerExtension\Generators;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use  Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

use Str;

class MediaLibraryPathGenerator extends DefaultPathGenerator
{
    public function getPath(Media $media) : string
    {
        // Change default media path to path like '<model>/media/<id>/image.jpg'
        $parts = explode('\\', $media->model_type);
        return Str::plural(mb_strtolower(end($parts))).'/media/'.$media->id.'/';
    }
    public function getPathForConversions(Media $media) : string
    {
        // Change default media path to path like '<model>/media/<id>/image.jpg'
        $parts = explode('\\', $media->model_type);
        return Str::plural(mb_strtolower(end($parts))).'/media/'.$media->id.'/';
    }
}
