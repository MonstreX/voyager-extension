<?php

namespace MonstreX\VoyagerExtension\ContentTypes;

use phpDocumentor\Reflection\Types\Integer;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\ContentTypes\BaseType;

class AdvInlineSetContentType extends BaseType
{
    private $model_name;
    private $model_id;

    public function handle()
    {

        $this->model_name = $this->request->model_name;
        $this->model_id = $this->request->model_id;

        if (!$requestedIDs = $this->request->input($this->row->field.'_id')) {
            return null;
        }

        // ----------------------------------------------
        // Store inline set in the related storage model
        // ----------------------------------------------
        if (isset($this->options->inline_set->source)) {
            $inlineModel = app($this->options->inline_set->source);
            $inlineRowIDs = [];
            foreach ($requestedIDs as $index => $rowID) {
                if ((int)$rowID === 0 && $this->request->input($this->row->field.'_delete')[$index] !== 'true') {
                    // Create a NEW ROW
                    $model = new $inlineModel;
                    $model->model = $this->model_name;
                    $model->model_id = $this->model_id;
                    $model->model_field = $this->row->field;
                    $model = $this->setModelFields($model, $index, $rowID);
                    $model->save();
                    $inlineRowIDs[] = $model->id;
                } else if ((int)$rowID > 0) {
                    // Update EXISTED ROWs (or delete)
                    $model = $inlineModel->findOrFail($rowID);
                    if ($this->request->input($this->row->field.'_delete')[$index] === 'true') {
                        $model->delete();
                    } else {
                        $model = $this->setModelFields($model, $index, $rowID);
                        $model->save();
                        $inlineRowIDs[] = $model->id;
                    }
                }
            }
            return implode(',', $inlineRowIDs);

        // ----------------------------------------------
        // Store inline set in the local field
        // ----------------------------------------------
        } else {
            $inlineRows = [];
            foreach ($requestedIDs as $index => $rowID) {
                $model = (object)[];
                $model->id = $rowID;
                $model->order = $index;
                $delete = $this->request->input($this->row->field.'_delete')[$index];
                if ($delete !== 'true' && $rowID != null && (int) $rowID >= 0) {
                    $model = $this->setModelFields($model, $index, $rowID);
                    $inlineRows[] = $model;
                }
            }
            return json_encode($inlineRows);
        }
    }

    private function setModelFields($model, $requestIndex, $rowID = null)
    {
        foreach ($this->options->inline_set->fields as $field_name => $field_data) {

            if ($field_data->type === 'media') {
                $value = $this->request->input($this->row->field.'_'.$field_name.'_media_'.$rowID);

                if ($value) {
                    $model->{$field_name} = $value;
                } else {
                    // Check for the new media files in the Request
                    $mediaLibraryName = $this->row->field.'_'.$field_name.'_'.$rowID;
                    if ($this->request->{$mediaLibraryName}) {
                        $model->{$field_name} = $mediaLibraryName;
                    }
                }

//                if ($mediaFiles = $this->request->{$this->row->field.'_'.$field_name.'_'.$index}) {
//                    foreach ($mediaFiles as $mediaFile) {
//                        // dd($this->request);
//                    }
//                }
            } else {
                $model->{$field_name} = $this->request->input($this->row->field.'_'.$field_name)[$requestIndex];
            }

        }
        $model->order = $requestIndex;

        return $model;
    }

}
