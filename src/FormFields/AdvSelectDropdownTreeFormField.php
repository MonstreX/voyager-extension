<?php

namespace MonstreX\VoyagerExtension\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class AdvSelectDropdownTreeFormField extends AbstractHandler
{
    protected $name = 'VE Select Dropdown Tree';
    protected $codename = 'adv_select_dropdown_tree';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('voyager-extension::formfields.adv_select_dropdown_tree', [
            'row' => $row,
            'options' => $options,
            'dataType' => $dataType,
            'dataTypeContent' => $dataTypeContent
        ]);
    }
}