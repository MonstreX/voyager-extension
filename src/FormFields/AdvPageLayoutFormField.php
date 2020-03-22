<?php

namespace MonstreX\VoyagerExtension\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class AdvPageLayoutFormField extends AbstractHandler
{
    protected $name = 'VE Page Layout';
    protected $codename = 'adv_page_layout';

    /*
     *  $dataTypeContent - Current model record
     */
    public function createContent($row, $dataType, $dataTypeContent, $options)
    {

        $model = app($options->block_model);
        $blocks = $model::where('status',1)->select('title','key')->orderBy('order', 'asc')->get()->toArray();

        $model = app($options->form_model);
        $forms = $model::where('status',1)->select('title','key')->get()->toArray();

        return view('voyager-extension::formfields.adv_page_layout', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
            'blocks'          => $blocks,
            'forms'           => $forms,
        ]);
    }

}
