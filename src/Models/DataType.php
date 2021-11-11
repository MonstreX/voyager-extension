<?php

namespace MonstreX\VoyagerExtension\Models;

use TCG\Voyager\Models\DataType as VoyagerDataType;

class DataType extends VoyagerDataType
{    
    /**
     * setOrderColumnAttribute
     *
     * @param mixed $value
     *
     * @return void
     */
    public function setOrderColumnAttribute($value)
    {
        //dd($this->attributes['details'], collect($this->details)->merge(['browse_order' => 12345, 'order_column' => $value]));
        //$this->attributes['details'] = collect($this->details)->merge(['order_column' => $value]);
        $this->attributes['details'] = collect($this->details)->merge(['browse_order' => 12345, 'order_column' => $value]);
    }
}
