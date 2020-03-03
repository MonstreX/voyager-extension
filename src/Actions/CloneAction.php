<?php

namespace MonstreX\VoyagerExtension\Actions;

use TCG\Voyager\Actions\AbstractAction;

class CloneAction extends AbstractAction
{
    public function getTitle()
    {
        return __('voyager-extension::bread.clone_action');
    }

    public function getIcon()
    {
        return 'voyager-documentation';
    }

    public function getPolicy()
    {
        return 'add';
    }

    public function getAttributes()
    {
        return [
            'class'   => 'btn btn-sm btn-success pull-right clone',
            'data-id' => $this->data->{$this->data->getKeyName()},
            'id'      => 'clone-'.$this->data->{$this->data->getKeyName()},
        ];
    }

    public function getDefaultRoute()
    {
        return 'javascript:;';
    }
}
