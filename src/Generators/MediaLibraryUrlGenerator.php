<?php

namespace MonstreX\VoyagerExtension\Generators;

use DateTimeInterface;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\UrlGenerator\BaseUrlGenerator;
use Str;

class MediaLibraryUrlGenerator extends  BaseUrlGenerator
{
    public function getUrl(): string
    {

        $url = $this->getDisk()->url($this->getPathRelativeToRoot());

        $url = $this->versionUrl($url);

        return $url;
    }

    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []): string
    {
        return $this->getDisk()->temporaryUrl($this->getPathRelativeToRoot(), $expiration, $options);
    }

    public function getPath(): string
    {
        $adapter = $this->getDisk()->getAdapter();

        $cachedAdapter = '\League\Flysystem\Cached\CachedAdapter';

        if ($adapter instanceof $cachedAdapter) {
            $adapter = $adapter->getAdapter();
        }

        $pathPrefix = $adapter->getPathPrefix();

        return $pathPrefix.$this->getPathRelativeToRoot();
    }

    public function getResponsiveImagesDirectoryUrl(): string
    {
        $base = \Illuminate\Support\Str::finish($this->getBaseMediaDirectoryUrl(), '/');

        $path = $this->pathGenerator->getPathForResponsiveImages($this->media);

        return Str::finish(url($base.$path), '/');
    }

}