<?php

namespace MonstreX\VoyagerExtension\ContentTypes\Services;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use Str;

class AdvInlineSetService
{
    /**
     * Store master record ID on the source rows
     * @param Request $request
     * @param Model $data
     * @param object $row
     */
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

    /**
     * @param Request $request
     * @param Model $data
     * @param object $row
     */
    public function checkAndDeleteMediaFiles(Request $request, Model $data, object $row)
    {
        $mediaNames = explode(',', $request->input($row->field.'_deleted_media'));

        if (count($mediaNames) > 0 && !empty($mediaNames[0])) {
            foreach ($mediaNames as $mediaName) {
                $data->clearMediaCollection($mediaName);
            }
        }
    }

    /**
     * Check if media files are presents in the request and save them
     * @param Request $request
     * @param Model $data
     * @param object $row
     */
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

    /**
     * Get all media library names
     * @param Request $request
     * @param Model $data
     * @param object $row
     * @return array
     */
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

    /**
     * Get All Inline Fields with their rows
     * @param string $slug
     * @param int $id
     * @return array
     */
    public function getAllInlineDataRows(string $slug, int $id)
    {
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $model = app($dataType->model_name);
        $model = $model->findOrFail($id);
        $rows = $dataType->rows()->get();

        $resultInlineRows = [];
        foreach ($rows as $row) {
            if ($row->type === 'adv_inline_set') {
                if (isset($row->details->inline_set->source)) {
                    $this->checkSourceFields($row->details->inline_set);
                }
                $resultInlineRows[$row->field] = $this->getInlineDataRows($model, $row->field);
            }
        }
        return $resultInlineRows;
    }

    /**
     * Get fields rows for ONE Inline Field
     * @param Model $data
     * @param string $fieldName
     * @return array|mixed
     */
    public function getInlineDataRows(Model $data, string $fieldName)
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
    public function removeRelatedSourceData(Model $data)
    {
        $masterModel = get_class($data);
        $dataType = Voyager::model('DataType')->where('model_name', '=', $masterModel)->first();

        foreach ($dataType->rows as $row) {
            if ($row->type === 'adv_inline_set' && isset($row->details->inline_set->source)) {
                $source = app($row->details->inline_set->source);
                $source->where('model', $masterModel)->where('model_id', $data->id)->delete();
            }
        }
    }

    /**
     * @param $file
     * @return mixed|string
     */
    private function getFileName($file)
    {
        $fullName = $file->getClientOriginalName();
        $filename = pathinfo($fullName, PATHINFO_FILENAME);
        $extension = pathinfo($fullName, PATHINFO_EXTENSION);

        return config('voyager-extension.slug_filenames')? Str::slug($filename) . '.' . $extension : $fullName;
    }

    /**
     * Check if all the source fields are present
     * @param object $inlineSet
     * @return string
     */
    private function checkSourceFields(object $inlineSet)
    {
        if (!isset($inlineSet->fields)) {
            throw new \InvalidArgumentException('Inline Set: has no fields list in the details');
        }

        if ($inlineSet->source) {
            $inlineModel = app($inlineSet->source);
            $table = $inlineModel->getTable();
            $columns = Schema::getColumnListing($table);

            foreach ($inlineSet->fields as $field_name => $field) {
                if (!in_array($field_name, $columns)) {
                    throw new \InvalidArgumentException('Inline Set: has no corresponding source field "' . $field_name . '" on the table "' . $table .'"');
                }
            }
        }
    }

}
