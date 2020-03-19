<?php

namespace MonstreX\VoyagerExtension\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class AdvFieldsGroupFormField extends AbstractHandler
{
    protected $name = 'VE Fields Group';
    protected $codename = 'adv_fields_group';

    /*
     *  $dataTypeContent - Current model record
     */
    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('voyager-extension::formfields.adv_fields_group', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }

}
