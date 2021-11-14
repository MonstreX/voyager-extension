<?php

namespace MonstreX\VoyagerExtension\Models;

use TCG\Voyager\Models\DataType as VoyagerDataType;

class DataType extends VoyagerDataType
{

    public function __construct(array $attributes = [])
    {
        $this->fillable[] = 'extra_details';
        parent::__construct($attributes);
    }

    public function getExtraDetailsAttribute()
    {
        return $this->details->extra_details ? json_encode($this->details->extra_details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}';
    }

    public function setExtraDetailsAttribute($value)
    {
        $value = json_decode(!empty($value) ? $value : '{}');
        $this->attributes['details'] = collect($this->details)->merge(['extra_details' => $value]);
    }

}
