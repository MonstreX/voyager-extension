<?php

namespace MonstreX\VoyagerExtension\ContentTypes\Services;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Facades\Voyager;
use Str;

class AdvInlineSetService
{

    public function setMasterIDOnSource(Request $request, Model $data, object $row)
    {
        if (!$request->model_id && !empty($data->{$row->field}) && isset($row->details->inline_set->source)) {
            $inlineModel = app($row->details->inline_set->source);
            $inlineIDs = explode(',', $data->{$row->field});

            foreach ($inlineIDs as $inlineID) {
                if (!empty($inlineID)) {
                    $model = $inlineModel->findOrFail((int)$inlineID);
                    $model->model_id = $data->id;
                    $model->save();
                }
            }
        }
    }

    public function checkAndDeleteMediaFiles(Request $request, Model $data, object $row)
    {
        $mediaNames = explode(',', $request->input($row->field.'_deleted_media'));

        if (count($mediaNames) > 0 && !empty($mediaNames[0])) {
            foreach ($mediaNames as $mediaName) {
                $data->clearMediaCollection($mediaName);
            }
        }
    }

    public function checkAndSaveMediaFiles(Request $request, Model $data, object $row)
    {
        $mediaNames = $this->getAllMediaLibraryNames($request, $data, $row);
        foreach ($mediaNames as $mediaName) {
            if ($files = $request->file($mediaName)) {
                foreach ($files as $file) {
                    if (!$file->isValid()) {
                        continue;
                    }
                    $data->addMedia($file)
                        ->setFileName($this->getFileName($file))
                        ->toMediaCollection($mediaName);
                }
            }
        }
    }

    private function getAllMediaLibraryNames(Request $request, Model $data, object $row)
    {
        $fields = $row->details->inline_set->fields;
        $fieldsRows = $this->getInlineDataRows($data, $row->field);

        $mediaNames = [];
        foreach ($fieldsRows as $fieldRow) {
            foreach ($fieldRow as $fieldName => $fieldValue) {
                if (isset($fields->{$fieldName}->type) && $fields->{$fieldName}->type === 'media') {
                    $mediaNames[] = $fieldValue;
                }
            }
        }
        return $mediaNames;
    }

    //---------------------------------------------
    // Get All Inline Fields with their rows
    //---------------------------------------------
    public function getAllInlineDataRows($slug, $id)
    {
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $model = app($dataType->model_name);
        $model = $model->findOrFail($id);
        $rows = $dataType->rows()->get();
        $resultInlineRows = [];
        foreach ($rows as $row) {
            if ($row->type === 'adv_inline_set') {
                $resultInlineRows[$row->field] = $this->getInlineDataRows($model, $row->field);
            }
        }
        return $resultInlineRows;
    }

    //---------------------------------------------
    // Get fields rows for ONE Inline Field
    //---------------------------------------------
    public function getInlineDataRows($data, $fieldName)
    {
        $masterModel = get_class($data);
        $dataType = Voyager::model('DataType')->where('model_name', '=', $masterModel)->first();

        $row = $dataType->editRows->filter(function ($value, $key) use ($fieldName) {
            return $value->field === $fieldName;
        })->first();

        if (isset($row->details->inline_set->source)) {
            $inlineModel = app($row->details->inline_set->source);
            $fieldsData = $inlineModel
                ->where('model', get_class($data))
                ->where('model_id', $data->id)
                ->where('model_field', $row->field)
                ->orderBy('order', 'ASC')
                ->get()->toArray();
        } else {
            $fieldsData = json_decode($data->{$row->field}, true);
        }

        return $fieldsData?? [];
    }

    /**
     * Remove ALL Related Source Data
     * @param $model
     */
    public function removeRelatedSourceData($model)
    {
        $masterModel = get_class($model);
        $dataType = Voyager::model('DataType')->where('model_name', '=', $masterModel)->first();

        foreach ($dataType->rows as $row) {
            if ($row->type === 'adv_inline_set' && isset($row->details->inline_set->source)) {
                $source = app($row->details->inline_set->source);
                $source->where('model', $masterModel)->where('model_id', $model->id)->delete();
            }
        }
    }

    private function getFileName($file)
    {
        $fullName = $file->getClientOriginalName();
        $filename = pathinfo($fullName, PATHINFO_FILENAME);
        $extension = pathinfo($fullName, PATHINFO_EXTENSION);

        return config('voyager-extension.slug_filenames')? Str::slug($filename) . '.' . $extension : $fullName;
    }

}
