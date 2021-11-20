<?php

namespace MonstreX\VoyagerExtension\ContentTypes;

use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\ContentTypes\BaseType;

class AdvInlineSetContentType extends BaseType
{
    public function handle()
    {
        // $this->request->input($this->row->field.'_id');
        // $this->options

        if (isset($this->options->inline_set->source)) {
            $inlineModel = app($this->options->inline_set->source);
            $modelIDs = [];
            foreach ($this->request->input($this->row->field.'_id') as $index => $recordId) {
                $model = $inlineModel->findOrFail($recordId);
                foreach ($this->options->inline_set->fields as $field_name => $field_data) {
                    $model->{$field_name} = $this->request->input($this->row->field.'_'.$field_name)[$index];
                }
                $model->order = $index;
                $model->save();
                $modelIDs[] = $model->id;
            }

            // Delete all unused related records doesn't include in the Set anymore
            $allRelatedModels = $inlineModel
                ->where('model', $this->request->model_name)
                ->where('model_id', $this->request->model_id)
                ->where('model_field', $this->row->field)
                ->get();

            foreach ($allRelatedModels as $related) {
                if (!in_array($related->id, $modelIDs)) {
                    $related->delete();
                }
            }

            return implode(',', $modelIDs);
        } else {
            return null;
        }
    }
}
