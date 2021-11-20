<?php

namespace MonstreX\VoyagerExtension\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class AdvInlineSetFormField extends AbstractHandler
{
    protected $name = 'VE Inline Fields Set';
    protected $codename = 'adv_inline_set';

    /*
     *  $dataTypeContent - Current model record
     */
    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('voyager-extension::formfields.adv_inline_set', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }

}
