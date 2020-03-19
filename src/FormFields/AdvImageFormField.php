<?php

namespace MonstreX\VoyagerExtension\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class AdvImageFormField extends AbstractHandler
{
    protected $name = 'VE Image';
    protected $codename = 'adv_image';

    /*
     *  $dataTypeContent - Current model record
     */
    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('voyager-extension::formfields.adv_image', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }

}
