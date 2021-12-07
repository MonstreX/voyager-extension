<?php

namespace MonstreX\VoyagerExtension\Traits;
use Illuminate\Database\Eloquent\Model;

use MonstreX\VoyagerExtension\ContentTypes\Services\AdvInlineSetService as InlineSetService;

trait InlineSetTrait
{
    /**
     * Remove Media files attached to Inline Set fields on Delete the model
     */
    public static function bootInlineSetTrait()
    {
        static::deleting(function (Model $model) {
            (new InlineSetService)->removeRelatedSourceData($model);
        });
    }

    /**
     * @param string $inlineSetField
     * @return \Illuminate\Support\Collection
     */
    public function getInlineSet($inlineSetField = '')
    {
        $data = (new InlineSetService)->getInlineDataRows($this, $inlineSetField);
        return collect($data);
    }

}

