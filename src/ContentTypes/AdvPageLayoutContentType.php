<?php

namespace MonstreX\VoyagerExtension\ContentTypes;

use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\ContentTypes\BaseType;

class AdvPageLayoutContentType extends BaseType
{
    public function handle()
    {
        if(isset($this->request->{$this->row->field})) {
            return $this->request->{$this->row->field};
        }
        return null;
    }
}
