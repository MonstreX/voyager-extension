<?php

namespace MonstreX\VoyagerExtension\Generators;

use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;
use Str;

class MediaLibraryUrlGenerator extends  DefaultUrlGenerator
{
    public function getUrl(): string
    {

        $url = Str::replaceFirst(request()->getSchemeAndHttpHost(), '', $this->getDisk()->url($this->getPathRelativeToRoot()));

        $url = $this->versionUrl($url);

        return $url;
    }
}