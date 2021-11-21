<?php

namespace MonstreX\VoyagerExtension\ContentTypes;

use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\ContentTypes\BaseType;

class AdvInlineSetContentType extends BaseType
{
    public function handle()
    {
        $requestedIDs = $this->request->input($this->row->field.'_id');

        if (isset($this->options->inline_set->source)) {
            // Store inline set in the related storage model
            $inlineModel = app($this->options->inline_set->source);
            $inlineRowIDs = [];

            foreach ($requestedIDs as $index => $rowID) {
                if ((int)$rowID === 0 && $this->request->input($this->row->field.'_delete')[$index] !== 'true') {
                    // Create new Row
                    $model = new $inlineModel;
                    $model->model = $this->request->input('model_name');;
                    $model->model_id = $this->request->input('model_id');
                    $model->model_field = $this->row->field;
                    $model = $this->setModelFields($model, $index);
                    $model->save();
                    $inlineRowIDs[] = $model->id;
                } else if ((int)$rowID > 0) {
                    // Update Existed Rows (or delete)
                    $model = $inlineModel->findOrFail($rowID);
                    if ($this->request->input($this->row->field.'_delete')[$index] === 'true') {
                        $model->delete();
                    } else {
                        $model = $this->setModelFields($model, $index);
                        $model->save();
                        $inlineRowIDs[] = $model->id;
                    }
                }
            }
            return implode(',', $inlineRowIDs);
        } else {
            // Store inline set in the local field
            $inlineRows = [];
            foreach ($requestedIDs as $index => $rowID) {
                $model = (object)[];
                $delete = $this->request->input($this->row->field.'_delete')[$index];
                if ($delete !== 'true' && $rowID != null && (int) $rowID >= 0) {
                    $model = $this->setModelFields($model, $index);
                    $inlineRows[] = $model;
                }
            }
            return json_encode($inlineRows);
        }
    }

    private function setModelFields($model, $index)
    {
        foreach ($this->options->inline_set->fields as $field_name => $field_data) {
            $model->{$field_name} = $this->request->input($this->row->field.'_'.$field_name)[$index];
        }
        $model->order = $index;

        return $model;
    }

}
