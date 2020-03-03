<?php

namespace MonstreX\VoyagerExtension\Controllers;

use Illuminate\Http\Request;
use MonstreX\VoyagerExtension\FormFields\AdvMediaFilesFormField;
use mysql_xdevapi\Collection;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use TCG\Voyager\Facades\Voyager;
use Spatie\MediaLibrary\Models\Media;

use MonstreX\VoyagerExtension\ContentTypes\KeyValueJsonContentType;
use MonstreX\VoyagerExtension\ContentTypes\AdvImageContentType;
use MonstreX\VoyagerExtension\ContentTypes\AdvMediaFilesContentType;

class VoyagerExtensionBaseController extends VoyagerBaseController
{

    public function edit(Request $request, $id)
    {

        $slug = $this->getSlug($request);
        View::share(['page_slug' => $slug, 'page_id' => $id]);
        return parent::edit($request, $id);
    }

    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);
        View::share(['page_slug' => $slug, 'page_id' => $id]);
        return parent::update($request, $id);
    }

    public function create(Request $request)
    {
        $slug = $this->getSlug($request);
        View::share('page_slug', $slug);
        return parent::create($request);
    }


    public function getContentBasedOnType(Request $request, $slug, $row, $options = null)
    {

        switch ($row->type) {
            case 'adv_image':
                return (new AdvImageContentType($request, $slug, $row, $options))->handle();
            case 'adv_media_files':
                return (new AdvMediaFilesContentType($request, $slug, $row, $options))->handle();
            case 'key_value_json':
                return (new KeyValueJsonContentType($request, $slug, $row, $options))->handle();
            default:
                return Controller::getContentBasedOnType($request, $slug, $row, $options);
        }
    }

    /*
     *  $request - current request object
     *  $slug - slug (table name)
     *  $rows - data types rows of Voyager model
     *  $data - current model record
     */
    public function insertUpdateData($request, $slug, $rows, $data)
    {

        // we need to create a record (in an usual way) before associate image to the actual model record
        $result = VoyagerBaseController::insertUpdateData($request, $slug, $rows, $data);

        // Check through all our new field types
        foreach ($rows as $row) {

            // Bind Single Image to $data record
            if ($row->type == 'adv_image' && $request->hasFile($row->field)) {

                $data->addMediaFromRequest($row->field)
                    ->withCustomProperties(['title' => null, 'alt' => null])
                    ->toMediaCollection($row->field);

            } elseif ($row->type == 'adv_image') {

                // Save new Title and ALt props
                $mediaItem = $data->getFirstMedia($row->field);
                if ($mediaItem) {
                    $mediaItem->setCustomProperty('title', $request->input($row->field . "_title"));
                    $mediaItem->setCustomProperty('alt', $request->input($row->field . "_alt"));
                    $mediaItem->save();
                }

            } elseif ($row->type == 'adv_media_files' && $request->hasFile($row->field)) {

                // Bind Multiple Images to $data record
                $files = $request->file($row->field);
                foreach ($files as $file) {
                    if (!$file->isValid()) {
                        continue;
                    }

                    // Add default fields Title and Alt and Extra Fields if present
                    $fields = ['title' => null, 'alt' => null];
                    if ($row->details && isset($row->details->extra_fields)) {
                        foreach ($row->details->extra_fields as $key => $extra_field) {
                            $fields[$key] = null;
                        }
                    }

                    // Add Image
                    $data->addMedia($file)
                        ->withCustomProperties($fields)
                        ->toMediaCollection($row->field);
                }
            } elseif ($row->type == 'adv_media_files') {
                //


            }


        }

        return $result;
    }

    //***************************************
    //
    //         Clone an item BREA(D)
    //
    //****************************************

    public function clone(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $rows = $dataType->rows()->get();
        $row = $this->getRowByField($rows, 'images')->type;


        //dd($row);

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        $source = $data = app($dataType->model_name);
        $source = $source->where('id', $id)->first();
        $cloned = $source->replicate();

        $reset_types = config('voyager-extension.clone_record.reset_types');
        $suffix_fields = config('voyager-extension.clone_record.suffix_fields');

        foreach ($cloned->getAttributes() as $key => $value) {
            if (in_array($this->getRowByField($rows, $key)->type, $reset_types)) {
                $cloned->{$key} = null;
            }
            if (in_array($key, $suffix_fields)) {
                $cloned->{$key} = $cloned->{$key} . ' (clone)';
            }
        }

        $res = $cloned->save();

        $data = $res
            ? [
                'message' => __('voyager::generic.successfully_cloned') . " {$dataType->display_name_singular}",
                'alert-type' => 'success',
            ]
            : [
                'message' => __('voyager::generic.error_cloning') . " {$dataType->display_name_singular}",
                'alert-type' => 'error',
            ];

        return redirect()->route("voyager.{$dataType->slug}.index")->with($data);
    }


    private function getRowByField($rows, string $field)
    {
        return $rows->filter(function ($value, $key) use ($field) {
            return $value->field === $field;
        })->first();
    }

}
